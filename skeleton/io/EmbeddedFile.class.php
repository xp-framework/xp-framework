<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'io.File',
    'io.Stream',
    'util.io.VirtualFileManager'
  );
  
  /**
   * This is a proxy class to an embedded stream or
   * a normal file. 
   *
   * @ext     overload
   */  
  class EmbeddedFile extends Object {
    public
      $_stream=   NULL;

    /**
     * Constructor
     *
     * @param   string filename
     * @return  mixed
     */
    public function __construct($filename) {
      $vfm= VirtualFileManager::getInstance();
      if (FALSE !== ($s= $vfm->getByFilename($filename))) {
        $this->_stream= $s;
      } else {
        $this->_stream= new File();
      }
    }

    /**
     * Proxy method to get values
     *
     * @param   string name
     * @param   mixed value
     * @return  boolean 
     */    
    public function __get($name) {
      if (!isset($this->_stream->{$name}))
        return FALSE;
        
      $value= $this->_stream->{$name};
      return $value;
    }

    /**
     * Proxy method to set values
     *
     * @param   string name
     * @param   mixed value
     */    
    public function __set($name, $value) {
      $this->_stream->{$name}= $value;
    }

    /**
     * Proxy method to call methods
     *
     * @param   string method
     * @param   array params
     * @param   mixed return
     * @return  boolean
     */    
    public function __call($method, $params) {
      $return= call_user_func_array(array($this->_stream, $method), $params);
      return $return;
    }
  } overload('EmbeddedFile');
?>
