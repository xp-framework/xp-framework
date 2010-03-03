<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'peer.Socket',
    'security.checksum.CRC32',
    'org.nagios.nsca.NscaMessage',
    'lang.MethodNotImplementedException'
  );

  define('NSCA_VERSION_2',  2);  
  define('NSCA_VERSION_3',  3);
  
  // Encryption methods
  define('NSCA_CRYPT_NONE', 0x0000);
  define('NSCA_CRYPT_XOR',  0x0001);

  /**
   * NSCA (Nagios Service Check Acceptor) Client
   *
   * <code>
   *   uses('org.nagios.nsca.NscaClient');
   *   
   *   $c= new NscaClient('nagios.example.com');
   *   try {
   *     $c->connect();
   *     $c->send(new NscaMessage(
   *       'soap1.example.com', 
   *       'queue_check', 
   *       NSCA_OK, 
   *       'Up and running'
   *     ));
   *     $c->send(new NscaMessage(
   *       'soap1.example.com', 
   *       'queue_check', 
   *       NSCA_ERROR, 
   *       'No answer on port 80 after 2 seconds'
   *     ));
   *     $c->close();
   *   } catch(XPException, $e) {
   *     $e->printStackTrace();
   *   }
   *
   *   echo $c->toString();
   * </code>
   *
   * @see      http://www.nagios.org/download/extras.php
   * @see      http://nagios.sourceforge.net/download/cvs/nsca-cvs.tar.gz
   * @see      http://jasonplancaster.com/projects/scripts/send_nsca/send_nsca_pl.source  
   * @purpose  Passive checks for Nagios
   */
  class NscaClient extends Object {
    public
      $version      = 0,
      $cryptmethod  = 0,
      $timeout      = 10;
      
    public
      $_xorkey      = '',
      $_timestamp   = '';

    /**
     * Constructor
     *
     * @param   string host the host the NSCA server is running on
     * @param   int port
     * @param   int version default NSCA_VERSION_3
     * @param   int cryptmethod default NSCA_CRYPT_XOR
     */
    public function __construct(
      $host, 
      $port= 5667, 
      $version= NSCA_VERSION_3, 
      $cryptmethod= NSCA_CRYPT_XOR
    ) {
      
      $this->sock= new Socket($host, $port);
      $this->version= $version;
      $this->cryptmethod= $cryptmethod;
    }

    /**
     * Set Version
     *
     * @param   int version
     */
    public function setVersion($version) {
      $this->version= $version;
    }

    /**
     * Get Version
     *
     * @return  int
     */
    public function getVersion() {
      return $this->version;
    }

    /**
     * Set Cryptmethod
     *
     * @param   int cryptmethod
     */
    public function setCryptmethod($cryptmethod) {
      $this->cryptmethod= $cryptmethod;
    }

    /**
     * Get Cryptmethod
     *
     * @return  int
     */
    public function getCryptmethod() {
      return $this->cryptmethod;
    }

    /**
     * Set read Timeout
     *
     * @param   int timeout
     */
    public function setTimeout($timeout) {
      $this->timeout= $timeout;
    }

    /**
     * Get Timeout
     *
     * @return  int
     */
    function getTimeout() {
      return $this->timeout;
    }

    /**
     * Connects to the NSCA server
     *
     * @return  bool
     */
    public function connect() {
      if (!$this->sock->connect()) return FALSE;
      $this->sock->setTimeout($this->getTimeout());

      // Get 128bit xor key and 4bit timestamp
      $this->_xorkey= $this->sock->readBinary(0x0080);
      $this->_timestamp= $this->sock->readBinary(0x0004);
      return TRUE;
    }
    
    /**
     * Returns a string representation of this object
     *
     * @return  string
     */
    public function toString() {
      static $cryptname= array(
        NSCA_CRYPT_NONE => 'NONE',
        NSCA_CRYPT_XOR  => 'XOR'
      );

      return sprintf(
        "%s@{\n".
        "  [endpoint]    nsca://%s:%d\n".
        "  [version]     %d\n".
        "  [cryptmethod] %d (%s)\n".
        "  [timestamp]   (%d bytes) %s\n".
        "  [xorkey]      (%d bytes) %s\n".
        "}\n",
        $this->getClassName(),
        $this->sock->host,
        $this->sock->port,
        $this->version,
        $this->cryptmethod,
        $cryptname[$this->cryptmethod],
        strlen($this->_timestamp),
        addcslashes($this->_timestamp, "\0..\37!@\177..\377"),
        strlen($this->_xorkey),
        addcslashes($this->_xorkey, "\0..\37!@\177..\377")
      );
    }
    
    /**
     * Closes the communication socket to the NSCA server
     *
     * @return  bool 
     */
    public function close() {
      return $this->sock->isConnected() ? $this->sock->close() : TRUE;
    }
    
    /**
     * Helper method which packs the message 
     *
     * @param   string crc
     * @param   &org.nagios.nsca.NscaMessage message
     * @return  string packed data
     */
    public function pack($crc, $message) {
      return pack(
        'nxxNa4na64a128a512xx',
        $this->version,
        $crc,
        $this->_timestamp,
        $message->getStatus(),
        $message->getHost(),
        $message->getService(),
        $message->getInformation()
      );
    }
    
    /**
     * Helper method which encrypts data
     *
     * @param   string data
     * @return  string encrypted data
     * @throws  lang.MethodNotImplementedException in case the encryption method is not supported
     */
    public function encrypt($data) {
      switch ($this->cryptmethod) {
        case NSCA_CRYPT_NONE:
          return $data;
          
        case NSCA_CRYPT_XOR:
          $len= strlen($data);
          return substr(
            $data ^ (str_repeat($this->_xorkey, intval(($len + 127) / 128))),
            0,
            $len
          );
       
        default:
          throw new MethodNotImplementedException(
            'Encryption method '.$this->cryptmethod.' not supported'
          );
      }
    }
  
    /**
     * Send a NSCA message to the server
     *
     * @param   org.nagios.nsca.NscaMessage message
     * @return  bool
     * @throws  lang.IllegalStateException
     */
    public function send($message) {
      if (!$this->sock->isConnected()) {
        throw new IllegalStateException('Not connected');
      }
      
      // Calculate CRC32 checksum, then build the final packet with the sig
      // and encrypt it using defined crypt method
      $data= $this->encrypt(
        $this->pack(CRC32::fromString($this->pack(0, $message))->asInt32(), $message)
      );
      
      // Finally, send data to the socket
      return $this->sock->write($data);
    }
  }
?>
