<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('peer.Socket', 'rdbms.tds.TdsDataStream', 'rdbms.tds.TdsProtocolException', 'util.Date');

  /**
   * TDS V7 protocol implementation
   *
   * @see   http://en.wikipedia.org/wiki/Tabular_Data_Stream
   * @see   http://msdn.microsoft.com/en-us/library/cc448435.aspx
   * @see   http://www.freetds.org/tds.html
   * @see   https://github.com/mono/mono/tree/master/mcs/class/Mono.Data.Tds/Mono.Data.Tds.Protocol
   */
  class TdsV7Protocol extends Object {
    protected $stream= NULL;
    protected $done= FALSE;
    public $connected= FALSE;

    // Messages
    const MSG_QUERY     = 0x1;
    const MSG_REPLY     = 0x4;
    const MSG_CANCEL    = 0x6;
    const MSG_LOGIN7    = 0x10;
    const MSG_LOGOFF    = 0x71;

    // Types
    const T_CHAR       = 0x2F;
    const T_VARCHAR    = 0x27;
    const T_INTN       = 0x26;
    const T_INT1       = 0x30;
    const T_DATE       = 0x31;
    const T_TIME       = 0x33;
    const T_INT2       = 0x34;
    const T_INT4       = 0x38;
    const T_INT8       = 0x7F;
    const T_FLT8       = 0x3E;
    const T_DATETIME   = 0x3D;
    const T_BIT        = 0x32;
    const T_TEXT       = 0x23;
    const T_NTEXT      = 0x63;
    const T_IMAGE      = 0x22;
    const T_MONEY4     = 0x7A;
    const T_MONEY      = 0x3C;
    const T_DATETIME4  = 0x3A;
    const T_REAL       = 0x3B;
    const T_BINARY     = 0x2D;
    const T_VOID       = 0x1F;
    const T_VARBINARY  = 0x25;
    const T_NVARCHAR   = 0x67;
    const T_BITN       = 0x68;
    const T_NUMERIC    = 0x6C;
    const T_DECIMAL    = 0x6A;
    const T_FLTN       = 0x6D;
    const T_MONEYN     = 0x6E;
    const T_DATETIMN   = 0x6F;
    const T_DATEN      = 0x7B;
    const T_TIMEN      = 0x93;
    const XT_CHAR      = 0xAF;
    const XT_VARCHAR   = 0xA7;
    const XT_NVARCHAR  = 0xE7;
    const XT_NCHAR     = 0xEF;
    const XT_VARBINARY = 0xA5;
    const XT_BINARY    = 0xAD;
    const T_UNITEXT    = 0xAE;
    const T_LONGBINARY = 0xE1;
    const T_SINT1      = 0x40;
    const T_UINT2      = 0x41;
    const T_UINT4      = 0x42;
    const T_UINT8      = 0x43;
    const T_UINTN      = 0x44;
    const T_UNIQUE     = 0x24;
    const T_VARIANT    = 0x62;
    const T_SINT8      = 0xBF;

    protected static $fixed= array(
      self::T_INT1     => 1,
      self::T_INT2     => 2,
      self::T_INT4     => 4,
      self::T_INT8     => 8,
      self::T_FLT8     => 8,
      self::T_BIT      => 1,
      self::T_MONEY4   => 4,
      self::T_MONEY    => 8,
      self::T_REAL     => 4,
      self::T_DATE     => 4,
      self::T_TIME     => 4,
      self::T_DATETIME => 8,
      self::T_SINT1    => 1,
      self::T_UINT2    => 2,
      self::T_UINT4    => 3,
      self::T_UINT8    => 8,
      self::T_SINT8    => 8,
    );

    /**
     * Creates a new protocol instance
     *
     * @param   peer.Socket s
     */
    public function __construct(Socket $s) {
      $this->stream= new TdsDataStream($s);
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
      $this->stream->connect();

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
      $this->stream->write(self::MSG_LOGIN7, pack('V', $offset).$login.$data);
      $response= $this->read();

      $this->cancel();    // TODO: Handle response, e.g. ENVCHANGE

      $this->connected= TRUE;
    }
    
    /**
     * Protocol read
     *
     * @return  string the message token
     * @throws  peer.ProtocolException
     */
    protected function read() {
      $this->done= FALSE;
      $type= $this->stream->begin();

      // Check for message type
      if (self::MSG_REPLY !== $type) {
        $this->cancel();
        throw new TdsProtocolException('Unknown message type '.$type);
      }
      
      // Handle errors - see also 2.2.5.7: Data Buffer Stream Tokens
      $token= $this->stream->getToken();
      if ("\xAA" === $token) {
        $meta= $this->stream->get('vlength/Vnumber/Cstate/Cclass', 8);
        $this->done= TRUE;
        throw new TdsProtocolException(
          $this->stream->getString($this->stream->getShort()),
          $meta['number'], 
          $meta['state'], 
          $meta['class'],
          $this->stream->getString($this->stream->getByte()),
          $this->stream->getString($this->stream->getByte()),
          $this->stream->getByte()
        );
      }
      
      return $token;
    }

    /**
     * Check whether connection is ready
     *
     * @return  bool
     */
    public function ready() {
      return $this->done;
    }

    /**
     * Cancel result set
     *
     */
    public function cancel() {
      if (!$this->done) {
        $this->stream->read(-1);    // TODO: Send cancel, then read rest. Will work like this, though, too.
        $this->done= TRUE;
      }
    }

    /**
     * Execute SQL in "fire and forget" mode.
     *
     * @param   string sql
     */
    public function exec($sql) {
      if (is_array($r= $this->query($sql))) {
        $this->cancel();
      }
    }

    /**
     * Issues a query and returns the results
     *
     * @param   string sql
     * @return  var
     */
    public function query($sql) {
      $this->stream->write(self::MSG_QUERY, iconv('iso-8859-1', 'ucs-2le', $sql));
      $token= $this->read();

      if ("\x81" === $token) {          // COLMETADATA
        $fields= array();
        $nfields= $this->stream->getShort();
        for ($i= 0; $i < $nfields; $i++) {
          $field= $this->stream->get('Cx1/Cx2/Cflags/Cx3/Ctype', 5);

          // Handle column. TODO: blob types - read table name
          if (self::T_NUMERIC === $field['type'] || self::T_DECIMAL === $field['type']) {
            $field['size']= $this->stream->getByte();
            $field['prec']= $this->stream->getByte();
            $field['scale']= $this->stream->getByte();
          } else if ($field['type'] > 128) {
            $field['size']= $this->stream->getShort();
            $this->stream->read(5);     // XXX Collation?
          } else if (isset(self::$fixed[$field['type']])) {
            $field['size']= self::$fixed[$field['type']];
          } else {
            $field['size']= $this->stream->getByte();
          }

          $field['name']= $this->stream->getString($this->stream->getByte());
          $fields[]= $field;
        }
        return $fields;
      } else if ("\xFD" === $token) {   // DONE
        $meta= $this->stream->get('vstatus/vcmd/Vrowcount', 8);
        $this->done= TRUE;
        // TODO: Maybe?
        return $meta['rowcount'];
      } else if ("\xE3" === $token) {   // ENVCHANGE, e.g. from "use [db]" queries
        // HANDLE!
        $this->cancel();
      } else {
        throw new TdsProtocolException(
          sprintf('Unexpected token 0x%02X', ord($token)),
          0,    // Number
          0,    // State
          0,    // Class
          NULL, // Server
          NULL, // Proc
          -1    // Line
        );
      }
    }

    /**
     * Fetches one record
     *
     * @param   [:var][] fields
     * @return  [:var] record
     */
    public function fetch($fields) {
      $token= $this->stream->getToken();
      if ("\xD1" !== $token) {
        // Console::$err->writeLinef('END TOKEN %02x', $token);
        $this->done= TRUE;
        return NULL;
      }
      
      $record= array();
      foreach ($fields as $i => $field) {
        switch ($field['type']) {
          case self::XT_VARCHAR: case self::XT_NVARCHAR:
            $len= $this->stream->getShort();
            $record[$i]= 0xFFFF === $len ? NULL : $this->stream->read($len);
            break;

          case self::T_INTN:
            $len= $this->stream->getByte();
            switch ($len) {
              case 1: $record[$i]= $this->stream->getByte();
              case 2: $record[$i]= $this->stream->getShort();
              case 4: $record[$i]= $this->stream->getLong();
              default: $record[$i]= NULL;
            }
            break;

          case self::T_INT1:
            $record[$i]= $this->stream->getByte();
            break;

          case self::T_INT2:
            $record[$i]= $this->stream->getShort();
            break;

          case self::T_INT4:
            $record[$i]= $this->stream->getLong();
            break;

          case self::T_NUMERIC:
            $len= $this->stream->getByte()- 1;
            $pos= $this->stream->getByte();
            $bits= array();
            for ($j= 0; $j < $len; $j+= 4) {
              $bits[]= $this->stream->getLong();
            }
            $record[$i]= array($pos, $bits);    // TODO: Create number from this
            break;

          case self::T_DATETIME:
            $days= $this->stream->getLong();
            $seconds= $this->stream->getLong();
            $record[$i]= Date::create(1900, 1, 1 + $days, 0, 0, $seconds / 300);
            break;

          case self::T_MONEY:
            $hi= $this->stream->getLong();
            $lo= $this->stream->getLong();
            if ($hi < 0) {
              $hi= ~$hi;
              $lo= ~($lo - 1);
              $div= -10000;
            } else {
              $div= 10000;
            }
            $record[$i]= bcdiv(bcadd(bcmul($hi, '4294967296'), $lo), $div, 5);
            break;

          default:
            Console::$err->writeLinef('Unknown field type 0x%02x', $field['type']);
        }
      }
      return $record;
    }

    /**
     * Close
     *
     */
    public function close() {
      if (!$this->connected) return;

      try {
        $this->stream->write(self::MSG_LOGOFF, "\0");
      } catch (IOException $ignored) {
        // Can't do much here
      } 

      $this->stream->close();
      $this->connected= FALSE;
    }
    
    /**
     * Creates a string representation
     *
     * @return  string
     */
    public function toString() {
      return $this->getClassName().'('.xp::stringOf($this->stream).')';
    }
  }
?>
