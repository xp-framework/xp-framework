<?php
/* This class is part of the XP framework
 *
 * $Id: PropertyManager.class.php 10594 2007-06-11 10:04:54Z friebe $ 
 */

  namespace util;

  ::uses('util.Properties');
  
  /**
   * Property-Manager
   * 
   * Usage:
   * <code>
   *   PropertyManager::getInstance()->configure('etc');
   *
   *   // ... later on ...
   *   $prop= PropertyManager::getInstance()->getProperties('database');
   *  
   *   // $prop is now a util.Property object with the properties
   *   // from etc/database.ini
   * </code>
   *
   * @purpose  Container
   */
  class PropertyManager extends lang::Object {
    protected static 
      $instance     = NULL;

    public 
      $_path    = '.',
      $_prop    = array();

    static function __static() {
      self::$instance= new self();
    }
    
    /**
     * Constructor.
     *
     */
    protected function __construct() {
    }
    
    /**
     * Retrieve this property manager's instance
     * 
     * @return  util.PropertyManager
     */
    public static function getInstance() {
      return self::$instance;
    }

    /**
     * Configure this property manager
     *
     * @param   string path search path to the property files
     */
    public function configure($path) {
      $this->_path= $path;
    }
    
    /**
     * Register a certain property object to a specified name
     *
     * @param   string name
     * @param   util.Properties properties
     */
    public function register($name, $properties) {
      $this->_prop[$this->_path.$name]= $properties;
    }

    /**
     * Return whether a given property file exists
     *
     * @param   string name
     * @return  bool
     */
    public function hasProperties($name) {
      return (
        isset($this->_prop[$this->_path.$name]) || 
        file_exists($this->_path.DIRECTORY_SEPARATOR.$name.'.ini')
      );
    }
   
    /**
     * Return properties by name
     *
     * @param   string name
     * @return  util.Properties
     */
    public function getProperties($name) {
      if (!isset($this->_prop[$this->_path.$name])) {
        $this->_prop[$this->_path.$name]= new Properties(
          $this->_path.DIRECTORY_SEPARATOR.$name.'.ini'
        );
      }
      return $this->_prop[$this->_path.$name];
    }
  }
?>
