<?php
/* This class is part of the XP framework
 *
 * $Id: DummySocket.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace net::xp_framework::unittest::scriptlet::rpc::dummy;

  ::uses('peer.Socket');

  /**
   * Dummy socket
   *
   * @purpose  Unittesting dummy
   */
  class DummySocket extends peer::Socket {
    public
      $isConnected  = FALSE;

    /**
     * Constructor
     *
     * @param   string string
     */
    public function __construct($string) {
      $this->_data= explode("\n", $string);
    }
    
    /**
     * Returns whether this socket is connected
     *
     * @return  bool
     */
    public function isConnected() {
      return $this->isConnected;
    }    
    
    /**
     * Connect
     *
     * @param   float timeout default 2.0
     * @return  bool
     */
    public function connect($timeout= 2.0) {
      $this->isConnected= TRUE;
      return 1;
    }
    
    /**
     * Close
     *
     * @return  bool
     */
    public function close() {
      $this->isConnected= FALSE;
      return TRUE;
    } 
    
    /**
     * Set timeout
     *
     * @param   float timeout default 2.0
     */
    public function setTimeout($timeout= 2.0) {
      $this->_timeout= $timeout;
    }
    
    /**
     * Set blocking (NOOP)
     *
     * @param   bool blockMode
     */
    public function setBlocking($blockMode) {}
    
    /**
     * Check whether data is available
     *
     * @return  bool
     */
    public function canRead() {
      return 0 < sizeof($this->_data);
    }
    
    /**
     * Read
     *
     * @param   int len default 4096
     * @return  string
     */
    public function read($len= 4096) {
      if (0 == sizeof($this->_data)) return NULL;
      return array_shift($this->_data);
    }
    
    /**
     * Read a line
     *
     * @param   int len
     * @return  string
     */
    public function readLine($len) {
      return $this->read($len);
    }
    
    /**
     * Read binary (NOOP)
     *
     * @return  string
     */
    public function readBinary() {}
    
    /**
     * Returns whether we're at the end of the data
     *
     * @return  bool
     */
    public function eof() {
      return 0 == sizeof($this->_data);
    }
  }
?>
