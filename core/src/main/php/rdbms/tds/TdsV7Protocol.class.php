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
     * Connect
     *
     * @param   string user
     * @param   string password
     * @throws  io.IOException
     */
    public function connect($user= '', $password= '') {
      $this->sock->isConnected() || $this->sock->connect();
      
      // Pre-login
      $this->write(self::MSG_PRELOGIN, self::EOM, $this->streamOf(array(
        'VERSION'      => pack('Nn', 0x0900, 0),
        'ENCRYPTION'   => pack('C', 0),
        'INSTOPT'      => "\x00",
        'THREADID'     => pack('N', getmypid()),
        'MARS'         => pack('C', 0),
      )));
      $tokens= $this->tokensIn($this->read());
      // DEBUG Console::writeLine('Server Version ', array_map('dechex', unpack('Nversion/nsubbuild', $tokens[0])));
      
      $params= array(
        'hostname'   => 'localhost',
        'username'   => $user,
        'password'   => $password,        // TODO: Scramble
        'appname'    => 'XP-Framework',
        'servername' => $this->sock->host,
        'unused'     => '',
        'library'    => 'rdbms.tds',
        'language'   => '',
        'database'   => ''
      );

      $login= pack(
        'NVNVVCCCC',
        0x02000972,         // TDSVersion 7.2
        4096,               // PacketSize
        7,                  // ClientProgVer
        getmypid(),         // ClientPID
        0,                  // ConnectionID
        0x20 | 0x40 | 0x80, // OptionFlags1 (use warning, initial db change, set language)
        0x03,               // OptionFlags2
        0,                  // TypeFlags
        0                   // (FRESERVEDBYTE / OptionFlags3)
      );
      $login.= "\xE0\01\00\00";  // ClientTimZone
      $login.= "\x09\04\00\00";  // ClientLCID

      $offset= 94;
      $data= '';
      foreach ($params as $param) {
        $ucs= iconv('iso-8859-1', 'ucs-2le', $param);
        $length= strlen($ucs);
        $login.= pack('nn', $offset, $length);
        $offset+= $length;
        $data.= $ucs;
      }
      $login.= "\x00\x1D\x92\xAB\x33\xDA";    // ClientID
      $login.= pack('nn', $offset, 0);        // SSPI
      $login.= pack('nn', $offset, 0);        // AtchDBFile
      $login.= pack('nn', $offset, 0);        // ChangePassword
      $login.= pack('N', 0);                  // SSPILong
      
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
      $offset= 5 * sizeof($tokens) + 1;
      foreach ($tokens as $token) {
        $length= strlen($token);
        $s.= pack('Cnn', $i, $offset, $length);
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
      Console::$err->writeLine('W-> ', array(
        'type'    => $type,
        'status'  => $status,
        'length'  => strlen($arg)+ 8,
        'spid'    => 0x0000,
        'packet'  => $this->pkt,
        'window'  => 0
      ));
      Console::$err->writeLine(': ', new Bytes($arg));
 
      $this->sock->write(pack('CCnnCc', $type, $status, strlen($arg)+ 8, 0x0000, $this->pkt, 0));
      $this->sock->write($arg);

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
      while (!$this->sock->eof() && ($s= strlen($b)) < $bytes) {
        $b.= $this->sock->readBinary($bytes- $s);
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
      Console::$err->write('R<- ', $header);

      $data= $this->readFully($header['length'] - 8);
      Console::$err->writeLine(': ', new Bytes($data));
      
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
