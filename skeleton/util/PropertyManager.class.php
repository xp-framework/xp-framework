<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('util.Properties');
  
  /**
   * Property-Manager
   * 
   * @purpose  Container
   */
  class PropertyManager extends Object {
    var 
      $_path    = '.',
      $_prop    = array();
    
    /**
     * Retreive this property manager's instance
     * 
     * @model   static
     * @access  public
     * @return  &util.PropertyManager
     */
    function &getInstance() {
      static $__instance;
      
      if (!isset($__instance)) $__instance= new PropertyManager();
      return $__instance;
    }

    /**
     * Configure this property manager
     *
     * @access  public
     * @param   string path search path to the property files
     */
    function configure($path) {
      $this->_path= $path;
    }
    
    /**
     * Return properties by name
     *
     * @access  public
     * @param   string name
     * @return  &util.Properties
     */
    function &getProperties($name) {
      if (!isset($this->_prop[$this->_path.$name])) {
        $this->_prop[$this->_path.$name]= &new Properties($this->_path.'/'.$name.'.ini');
      }
      return $this->_prop[$this->_path.$name];
    }
  }
?>
