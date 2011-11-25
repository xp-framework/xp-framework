<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('peer.Socket', 'rdbms.tds.TdsProtocolException', 'util.Date');

  /**
   * TDS V7 protocol implementation
   *
   * @see   http://en.wikipedia.org/wiki/Tabular_Data_Stream
   * @see   http://msdn.microsoft.com/en-us/library/cc448435.aspx
   * @see   http://www.freetds.org/tds.html
   * @see   https://github.com/mono/mono/tree/master/mcs/class/Mono.Data.Tds/Mono.Data.Tds.Protocol
   */
  class TdsV7Protocol extends Object {
    protected $pkt= 0;
    protected $sock= NULL;
    public $connected= FALSE;

    const EOM          = 0x01;

    // Messages
    const MSG_QUERY    = 0x1;
    const MSG_REPLY    = 0x4;
    const MSG_CANCEL   = 0x6;
    const MSG_LOGIN7   = 0x10;
    const MSG_LOGOFF   = 0x71;

    /**
     * Creates a new protocol instance
     *
     * @param   peer.Socket s
     */
    public function __construct(Socket $s) {
      $this->sock= $s;
    }

    /**
     * Helper method for dump()
     *
     * @param   string bytes
     * @param   int offset
     * @return  string
     */
    protected static function chars($bytes, $offset) {
      $s= '';
      for ($j= $offset- 16, $l= min($offset, strlen($bytes)); $j < $l; $j++) {
        $c= $bytes{$j};
        $s.= $c < "\x20" || $c > "\x7F" ? ' ' : $c;
      }
      return $s;
    }

    /**
     * Creates a hexdump
     *
     * @param   string bytes
     * @return  string
     */
    protected static function dump($bytes) {
      $s= '';
      for ($i= 0, $n= strlen($bytes); $i < $n; $i++) {
        if (0 === $i) {
          $s= '  0: ';
        } else if (0 === ($i % 16)) {
          $s.= sprintf("|%s|\n%3d: ", self::chars($bytes, $i), $i);
        }
        $s.= sprintf('%02X ', ord($bytes{$i}));
      }
      if ($r= ($i % 16)) {
        $s.= str_repeat('   ', 16 - ($i % 16));
      }
      $s.= '|'.self::chars($bytes, $i).'|';
      return $s;
    }

    /**
     * Scrambles password
     *
     * @param   string password
     * @return  string
     */
    protected function scramble($password) {
      $xor= 0x5A5A;
      $s= '';
      for ($i= 0, $l= strlen($password); $i < $l; $i++) {
        $c= ord($password{$i}) ^ $xor;
        $s.= chr((($c >> 4) & 0x0F0F) | (($c << 4) & 0xF0F0))."\xA5";
      }
      return $s;
    }

    /**
     * Connect
     *
     * @param   string user
     * @param   string password
     * @throws  io.IOException
     */
    public function connect($user= '', $password= '') {
      $this->sock->isConnected() || $this->sock->connect();

      $params= array(
        'hostname'   => array(TRUE, 'CARLA'),
        'username'   => array(TRUE, $user),
        'password'   => array(FALSE, $this->scramble($password), strlen($password)),
        'appname'    => array(TRUE, 'XP-Framework'),
        'servername' => array(TRUE, 'localhost'),
        'unused'     => array(FALSE, '', 0),
        'library'    => array(TRUE, $this->getClassName()),
        'language'   => array(TRUE, ''),
        'database'   => array(TRUE, 'master')
      );
      
      // Initial packet
      $login= pack(
        'VVVVVCCCCVV',
        0x71000001,         //  4: TDSVersion 7.1
        0,                  //  8: PacketSize
        7,                  // 12: ClientProgVer
        getmypid(),         // 16: ClientPID
        0,                  // 20: ConnectionID
        0x20 | 0x40 | 0x80, // 24: OptionFlags1 (use warning, initial db change, set language)
        0x03,               // 25: OptionFlags2
        0,                  // 26: TypeFlags
        0,                  // 27: (FRESERVEDBYTE / OptionFlags3)
        0,                  // 28: ClientTimZone
        0                   // 32: ClientLCID
      );

      // Offsets
      $offset= 86;
      $data= '';
      foreach ($params as $name => $param) {
        if ($param[0]) {
          $chunk= iconv('iso-8859-1', 'ucs-2le', $param[1]);
          $length= strlen($param[1]);
        } else {
          $chunk= $param[1];
          $length= $param[2];
        }
        $login.= pack('vv', $offset, $length);
        $offset+= strlen($chunk);
        $data.= $chunk;
      }
      
      // Packet end
      $login.= "\x00\x00\x00\x00\x00\x00";  // ClientID
      $login.= pack(
        'vvvv',
        $offset, 0,         // SSPI
        $offset, 0          // SSPILong
      );

      // Login
      $this->write(self::MSG_LOGIN7, pack('V', $offset).$login.$data);
      $response= $this->read();
      
      $this->connected= TRUE;
    }
    
    /**
     * Creates a token stream of TokenType, TokenPosition and TokenLeng
     * followed by the data itself.
     *
     * @param   [:string] tokens
     * @return  string
     */
    protected function streamOf($tokens) {
      $s= '';
      $i= 0;
      $offset= (5 * sizeof($tokens)) + 1;
      foreach ($tokens as $token) {
        $length= strlen($token);
        $s.= pack('vvC', $i, $offset, $length);
        $offset+= $length;
        $i++;
      }
      $s.= "\xFF";
      foreach ($tokens as $token) {
        $s.= $token;
      }
      return $s;
    }
    
    /**
     * Decodes a complete token stream into tokens
     *
     * @param   string stream
     * @return  string[] tokens
     */
    protected function tokensIn($stream) {
      $tokens= array();
      for ($t= 0, $l= strlen($stream); $t < $l && "\xFF" !== $stream{$t}; $t+= 5) {
        $pos= unpack('Cn/noffset/nlength', substr($stream, $t, 5));
        $tokens[]= substr($stream, $pos['offset'], $pos['length']);
      }
      return $tokens;
    }

    /**
     * Protocol write
     *
     * @param   int type the message type one of the MSG_* constants
     * @param   string arg
     * @throws  peer.ProtocolException
     */
    protected function write($type, $arg) {
      $length= strlen($arg)+ 8;
      $packet= pack('CCnnCc', $type, self::EOM, $length, 0x0000, $this->pkt, 0).$arg;

      Console::$err->writeLine('W-> ', array(
        'type'    => $type,
        'status'  => self::EOM,
        'length'  => $length,
        'spid'    => 0x0000,
        'packet'  => $this->pkt,
        'window'  => 0
      ));
      Console::$err->writeLine(self::dump($packet));
 
      $this->sock->write($packet);
      $this->pkt= $this->pkt+ 1 & 0xFF;
    }

    /**
     * Read a number of bytes
     *
     * @param   int bytes
     * @return  string
     */
    protected function readFully($bytes) {
      $b= '';
      while (($s= strlen($b)) < $bytes) {
        $c= $this->sock->readBinary($bytes- $s);
        if ('' === $c) break;
        $b.= $c;
      }
      return $b;
    }
    
    /**
     * Returns a string from a given buffer and increased consumed length
     *
     * @param   string data
     * @param   string t token
     * @param   &int consumed
     * @return  string
     */
    protected function lstr($data, $t, &$consumed) {
      static $b= array('C' => 1, 'v' => 2);

      $l= current(unpack($t, substr($data, $consumed, $b[$t]))) * 2;
      $consumed+= $b[$t];
      if (0 === $l) return NULL;

      $chunk= iconv('ucs-2le', 'iso-8859-1//IGNORE', substr($data, $consumed, $l));
      $consumed+= $l;
      return $chunk;
    }
    
    const SYBCHAR               = 47; // 0x2F
    const SYBVARCHAR            = 39; // 0x27
    const SYBINTN               = 38; // 0x26
    const SYBINT1               = 48; // 0x30
    const SYBDATE               = 49; // 0x31 Sybase 12
    const SYBTIME               = 51; // 0x33 Sybase 12
    const SYBINT2               = 52; // 0x34
    const SYBINT4               = 56; // 0x38
    const SYBINT8               = 127;// 0x7F
    const SYBFLT8               = 62; // 0x3E
    const SYBDATETIME           = 61; // 0x3D
    const SYBBIT                = 50; // 0x32
    const SYBTEXT               = 35; // 0x23
    const SYBNTEXT              = 99; // 0x63
    const SYBIMAGE              = 34; // 0x22
    const SYBMONEY4             = 122;// 0x7A
    const SYBMONEY              = 60; // 0x3C
    const SYBDATETIME4          = 58; // 0x3A
    const SYBREAL               = 59; // 0x3B
    const SYBBINARY             = 45; // 0x2D
    const SYBVOID               = 31; // 0x1F
    const SYBVARBINARY          = 37; // 0x25
    const SYBNVARCHAR           = 103;// 0x67
    const SYBBITN               = 104;// 0x68
    const SYBNUMERIC            = 108;// 0x6C
    const SYBDECIMAL            = 106;// 0x6A
    const SYBFLTN               = 109;// 0x6D
    const SYBMONEYN             = 110;// 0x6E
    const SYBDATETIMN           = 111;// 0x6F
    const SYBDATEN              = 123;// 0x7B SYBASE 12
    const SYBTIMEN              = 147;// 0x93 SYBASE 12
    const XSYBCHAR              = 175;// 0xAF
    const XSYBVARCHAR           = 167;// 0xA7
    const XSYBNVARCHAR          = 231;// 0xE7
    const XSYBNCHAR             = 239;// 0xEF
    const XSYBVARBINARY         = 165;// 0xA5
    const XSYBBINARY            = 173;// 0xAD
    const SYBUNITEXT            = 174;// 0xAE SYBASE 15
    const SYBLONGBINARY         = 225;// 0xE1 SYBASE 12
    const SYBSINT1              = 64; // 0x40
    const SYBUINT2              = 65; // 0x41 SYBASE 15
    const SYBUINT4              = 66; // 0x42 SYBASE 15
    const SYBUINT8              = 67; // 0x43 SYBASE 15
    const SYBUINTN              = 68; // 0x44 SYBASE 15
    const SYBUNIQUE             = 36; // 0x24
    const SYBVARIANT            = 98; // 0x62
    const SYBSINT8              = 191;// 0xBF SYBASE 15

    protected static $fixed= array(
      self::SYBINT1   => 1,
      self::SYBINT2   => 2,
      self::SYBINT4   => 4,
      self::SYBINT8   => 8,
      self::SYBFLT8   => 8,
      self::SYBBIT    => 1,
      self::SYBMONEY4 => 4,
      self::SYBMONEY  => 8,
      self::SYBREAL   => 4,
      self::SYBDATE   => 4,
      self::SYBTIME   => 4,
      self::SYBSINT1  => 1,
      self::SYBUINT2  => 2,
      self::SYBUINT4  => 3,
      self::SYBUINT8  => 8,
      self::SYBSINT8  => 8,
    );
    
    /**
     * Protocol read
     *
     * @return  string
     * @throws  peer.ProtocolException
     */
    protected function read() {
      $header= unpack('Ctype/Cstatus/nlength/nspid/Cpacket/cwindow', $this->readFully(8));
      Console::$err->writeLine('R<- ', $header);

      $data= $this->readFully($header['length'] - 8);
      Console::$err->writeLine(self::dump($data));

      // See 2.2.5.7 Data Buffer Stream Tokens
      switch ($data{0}) {
        case "\xAA":    // ERROR
          $meta= unpack('vlength/Vnumber/Cstate/Cclass', substr($data, 1, 8));
          $consumed= 9;
          $meta['message']= $this->lstr($data, 'v', $consumed);
          $meta['server']= $this->lstr($data, 'C', $consumed);
          $meta['proc']= $this->lstr($data, 'C', $consumed);
          $meta['line']= current(unpack('v', substr($data, $consumed, 2)));

          throw new TdsProtocolException(
            $meta['message'],
            $meta['number'], 
            $meta['state'], 
            $meta['class'],
            $meta['server'],
            $meta['proc'],
            $meta['line']
          );
        
        case "\x81":    // COLMETADATA
          $nfields= current(unpack('v', substr($data, 1, 2)));
          $consumed= 3;
          $fields= array();
          for ($i= 0; $i < $nfields; $i++) {
            $field= unpack('Cx1/Cx2/Cflags/Cx3/Ctype', substr($data, $consumed, 5));
            $consumed+= 5;

            // Handle column. TODO: blob types - read table name
            if ($field['type'] > 128) {
              $field['size']= current(unpack('v', substr($data, $consumed, 2)));
              $consumed+= 2;
              $consumed+= 5;  // Collation?
            } else if (isset(self::$fixed[$field['type']])) {
              $field['size']= self::$fixed[$field['type']];
            } else {
              $field['size']= current(unpack('C', substr($data, $consumed, 1)));
              $consumed+= 1;
            }

            $field['name']= $this->lstr($data, 'C', $consumed);
            $fields[]= $field;
          }
          Console::$err->writeLine($nfields, ' FIELDS ', $fields);

          $rows= array();
          while ("\xD1" === $data{$consumed}) {
            $consumed++;
            $row= array();
            foreach ($fields as $field) {
              switch ($field['type']) {
                case self::XSYBVARCHAR:    // XX Collation?
                  $len= current(unpack('v', substr($data, $consumed, 2)));
                  $row[$field['name']]= substr($data, $consumed+ 2, $len);
                  $consumed+= $len+ 2;
                  break;

                case self::SYBINTN:
                  $len= current(unpack('C', substr($data, $consumed, 1)));
                  switch ($len) {
                    case 4: $row[$field['name']]= current(unpack('V', substr($data, $consumed+ 1, 4)));
                  }
                  $consumed+= $len+ 1;
                  break;
              }
            }
            $rows[]= $row;
          }

          return $rows;

        case "\xFD":    // DONE
          $meta= unpack('vstatus/vcmd/Vrowcount', substr($data, 1, 8));
          return $meta;
        
        default:
          Console::$err->writeLinef('Unhandled packet %02X', ord($data{0}));
      }
      return $data;
    }

    /**
     * Issues a query and returns the results
     *
     * @param   string sql
     * @return  var
     */
    public function query($sql) {
      $this->write(self::MSG_QUERY, iconv('iso-8859-1', 'ucs-2le', $sql));
      return $this->read();
    }

    /**
     * Close
     *
     */
    public function close() {
      if (!$this->sock->isConnected()) return;

      try {
        $this->write(self::MSG_LOGOFF, "\0");
      } catch (IOException $ignored) {
        // Can't do much here
      } 

      $this->sock->close();
      $this->connected= FALSE;
    }
    
    /**
     * Creates a string representation
     *
     * @return  string
     */
    public function toString() {
      return $this->getClassName().'('.xp::stringOf($this->sock->getHandle()).')';
    }
  }
?>
