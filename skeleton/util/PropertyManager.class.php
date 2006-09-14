<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('util.Properties');
  
  /**
   * Property-Manager
   * 
   * Usage:
   * <code>
   *   $pm= &PropertyManager::getInstance();
   *   $pm->configure('etc');
   *
   *   // ... later on ...
   *   $pm= &PropertyManager::getInstance();
   *   $prop= &$pm->getProperties('database');
   *  
   *   // $prop is now a util.Property object with the properties
   *   // from etc/database.ini
   * </code>
   *
   * @purpose  Container
   */
  class PropertyManager extends Object {
    var 
      $_path    = '.',
      $_prop    = array();
    
    /**
     * Retrieve this property manager's instance
     * 
     * @model   static
     * @access  public
     * @return  &util.PropertyManager
     */
    function &getInstance() {
      static $instance;
      
      if (!isset($instance)) $instance= new PropertyManager();
      return $instance;
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
     * Register a certain property object to a specified name
     *
     * @access  public
     * @param   string name
     * @param   &util.Properties properties
     */
    function register($name, &$properties) {
      $this->_prop[$this->_path.$name]= &$properties;
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
        $this->_prop[$this->_path.$name]= &new Properties(
          $this->_path.DIRECTORY_SEPARATOR.$name.'.ini'
        );
      }
      return $this->_prop[$this->_path.$name];
    }
  }
?>
