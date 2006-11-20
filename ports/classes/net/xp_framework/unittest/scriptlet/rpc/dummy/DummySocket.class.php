<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('peer.Socket');

  /**
   * Dummy socket
   *
   * @purpose  Unittesting dummy
   */
  class DummySocket extends Socket {
    var
      $isConnected  = FALSE;

    /**
     * Constructor
     *
     * @access  public
     * @param   string string
     */
    function __construct($string) {
      $this->_data= explode("\n", $string);
    }
    
    /**
     * Returns whether this socket is connected
     *
     * @access  public
     * @return  bool
     */
    function isConnected() {
      return $this->isConnected;
    }    
    
    /**
     * Connect
     *
     * @access  public
     * @param   float timeout default 2.0
     * @return  bool
     */
    function connect($timeout= 2.0) {
      $this->isConnected= TRUE;
      return 1;
    }
    
    /**
     * Close
     *
     * @access  public
     * @return  bool
     */
    function close() {
      $this->isConnected= FALSE;
      return TRUE;
    } 
    
    /**
     * Set timeout
     *
     * @access  public
     * @param   float timeout default 2.0
     */
    function setTimeout($timeout= 2.0) {
      $this->_timeout= $timeout;
    }
    
    /**
     * Set blocking (NOOP)
     *
     * @access  public
     * @param   bool blockMode
     */
    function setBlocking($blockMode) {}
    
    /**
     * Check whether data is available
     *
     * @access  public
     * @return  bool
     */
    function canRead() {
      return 0 < sizeof($this->_data);
    }
    
    /**
     * Read
     *
     * @access  public
     * @param   int len default 4096
     * @return  string
     */
    function read($len= 4096) {
      if (0 == sizeof($this->_data)) return NULL;
      return array_shift($this->_data);
    }
    
    /**
     * Read a line
     *
     * @access  public
     * @param   int len
     * @return  string
     */
    function readLine($len) {
      return $this->read($len);
    }
    
    /**
     * Read binary (NOOP)
     *
     * @access  public
     * @return  string
     */
    function readBinary() {}
    
    /**
     * Returns whether we're at the end of the data
     *
     * @access  public
     * @return  bool
     */
    function eof() {
      return 0 == sizeof($this->_data);
    }
  }
?>
