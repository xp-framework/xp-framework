<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('peer.Socket', 'rdbms.mysqlx.MySqlxProtocolException', 'rdbms.mysqlx.MySqlPassword', 'util.Date');

  /**
   * TDS V7 protocol implementation
   *
   * @see   http://en.wikipedia.org/wiki/Tabular_Data_Stream
   * @see   http://msdn.microsoft.com/en-us/library/cc448435.aspx
   * @see   http://www.freetds.org/tds.html
   */
  class TdsV7Protocol extends Object {
    protected $pkt= 0;
    protected $sock= NULL;
    public $connected= FALSE;

    const EOM          = 0x01;

    // Messages
    const MSG_PRELOGIN = 0x12;
    const MSG_LOGIN7   = 0x10;

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
        $s.= chr((($c >> 4) & 0x0F0F) | (($c << 4) & 0xF0F0));
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

      // Pre-login
      /*
      $this->write(self::MSG_PRELOGIN, self::EOM, $this->streamOf(array(
        'VERSION'      => "\x08\x00\x01\x55\x00\x00",
        'ENCRYPTION'   => pack('C', 0),
        'INSTOPT'      => "\x00",
        'THREADID'     => "\x01\x02\x00\x00"
      )));
      $tokens= $this->tokensIn($this->read());
      Console::writeLine('Server Version ', $tokens); // array_map('dechex', unpack('Nversion/nsubbuild', $tokens[0])));
      */
      
      $params= array(
        'hostname'   => 'CARLA',
        'username'   => $user,
        'password'   => $this->scramble($password),
        'appname'    => 'jTDS',
        'servername' => 'localhost',
        'unused'     => '',
        'library'    => 'jTDS',
        'language'   => '',
        'database'   => 'master'
      );

      $login= pack(
        'VVVVVCCCC',
        0x71000001,         //  4: TDSVersion 7.1
        0,                  //  8: PacketSize
        7,                  // 12: ClientProgVer
        123,         // 16: ClientPID
        0,                  // 20: ConnectionID
        0x20 | 0x40 | 0x80, // 24: OptionFlags1 (use warning, initial db change, set language)
        0x03,               // 25: OptionFlags2
        0,                  // 26: TypeFlags
        0                   // 27: (FRESERVEDBYTE / OptionFlags3)
      );
      $login.= "\x00\x00\x00\x00";  // ClientTimZone
      $login.= "\x00\x00\x00\x00";  // ClientLCID

      $offset= 86;
      $data= '';
      foreach ($params as $name =>$param) {
        $ucs= iconv('iso-8859-1', 'ucs-2le', $param);
        $length= strlen($param);
        $login.= pack('vv', $offset, $length);
        $offset+= strlen($ucs);
        $data.= $ucs;
      }
      $login.= "\x00\x00\x00\x00\x00\x00";    // ClientID
      $login.= pack('vv', $offset, 0);        // SSPI
      $login.= pack('vv', $offset, 0);        // SSPILong
      
      // Login
      $this->write(self::MSG_LOGIN7, self::EOM, pack('V', $offset).$login.$data);
      $this->read();
      
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
     * @param   int status the status
     * @param   string arg
     * @throws  peer.ProtocolException
     */
    protected function write($type, $status, $arg) {
      static $i= 0;
      Console::$err->writeLine('W-> ', array(
        'type'    => $type,
        'status'  => $status,
        'length'  => strlen($arg)+ 8,
        'spid'    => 0x0000,
        'packet'  => 0, // $this->pkt,
        'window'  => 0
      ));
      
      $packet= pack('CCnnCc', $type, $status, strlen($arg)+ 8, 0x0000, 1, 0).$arg;
      $packet.= str_repeat("\0", 512- 8- strlen($arg));
      file_put_contents('tds'.$i++, $packet);
      Console::$err->writeLine($this->dump($packet));
 
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
     * Protocol read
     *
     * @return  string
     * @throws  peer.ProtocolException
     */
    protected function read() {
      $header= unpack('Ctype/Cstatus/nlength/nspid/Cpacket/cwindow', $this->readFully(8));
      Console::$err->writeLine('R<- ', $header);

      $data= $this->readFully($header['length'] - 8);
      Console::$err->writeLine($this->dump($data));
      
      return $data;
    }

    /**
     * Close
     *
     */
    public function close() {
      if (!$this->sock->isConnected()) return;

      // TODO

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
