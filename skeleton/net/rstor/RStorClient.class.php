<?php
/* Diese Klasse ist Teil des XP-Frameworks
 *
 * $Id$
 */
  uses('net.Socket');

  /**
   * (Insert class description here)
   *
   * @access  public
   */
  class RStorClient extends Socket {
    var
      $_timeout= 1;
      
    /**
     * Private Helper-Funktion
     * Sendet eine Zeile und nimmt die Antwort auseinander
     *
     * @access  
     * @param   
     * @return  
     */
    function _cmd() {
      $args= func_get_args();
      $this->write(vsprintf($args[0]."\n", array_slice($args, 1)));
      $return= chop($this->read(65536));
      
      // +OK text saved.
      // -ERR SET format: key=val
      // -ERR not understood
      if ('+OK' != substr($return, 0, $i= strpos($return, ' '))) return throw(new Exception(
        $return
      ));
      return substr($return, $i+ 1);
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function deleteKey($key) {
      return FALSE !== $this->_cmd('DELE %s', $key);
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function readKey($key) {
      if (FALSE === ($return= $this->_cmd('GET %s', $key))) return FALSE;
      return unserialize($return);
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function writeKey($key, $value) {
      return FALSE !== $this->_cmd('SET %s=%s', $key, serialize($value));
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function hasKey($key) {
      return FALSE !== $this->_cmd('GET %s', $key);
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function getKeys() {
      if (FALSE === ($return= $this->_cmd('KEYS'))) return FALSE;
      return explode('|', substr($return, 0, -1));
    }
  }
  
?>
