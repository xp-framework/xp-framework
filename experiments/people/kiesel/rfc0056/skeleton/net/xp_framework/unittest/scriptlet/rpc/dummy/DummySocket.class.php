<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('peer.Socket');

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class DummySocket extends Socket {
    var
      $isConnected  = FALSE;
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function __construct($string) {
      $this->_data= explode("\n", $string);
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function isConnected() {
      return $this->isConnected;
    }    
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function connect($timeout= 2.0) {
      $this->isConnected= TRUE;
      return 1;
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function close() {
      $this->isConnected= FALSE;
      return TRUE;
    } 
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function setTimeout() {
      $this->_timeout= $timeout;
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function setBlocking($blockMode) {}
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function canRead() {
      return 0 < sizeof($this->_data);
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function read($len= 4096) {
      if (0 == sizeof($this->_data)) return NULL;
      return array_shift($this->_data);
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function readLine($len) {
      return $this->read($len);
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function readBinary() {}
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function eof() {
      return 0 == sizeof($this->_data);
    }
  }
?>
