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
    protected $sock= NULL;
    public $connected= FALSE;

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
      $this->write(
        "\x12\x01\x00\x2F\x00\x00\x01\x00".
        "\x00\x00\x1A\x00\x06\x01\x00\x20".
        "\x00\x01\x02\x00\x21\x00\x01\x03\x00\x22\x00\x04\x04\x00\x26\x00".
        "\x01\xFF\x09\x00\x00\x00\x00\x00\x01\x00\xB8\x0D\x00\x00\x01".
        ''
      );
      $this->read();
      
      // Login
      
      
      $this->connected= TRUE;
    }

    /**
     * Protocol write
     *
     * @param   string arg
     * @throws  peer.ProtocolException
     */
    protected function write($arg) {
      Console::$err->writeLine('W-> ', new Bytes($arg));
      
      $this->sock->write($arg);
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
      $header= unpack('ctype/cstatus/nlength/nspid/cpacket/cwindow', $this->readFully(8));
      
      $data= $this->readFully($header['length'] - 8);
      Console::$err->writeLine('R<- ', new Bytes($data));
      
      return '';
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
