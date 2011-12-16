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
  class PropertyManager extends Object {
    protected static 
      $instance     = NULL;

    protected
      $paths    = array();

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
      $this->addPath($path);
    }

    public function addPath($path) {
      if (isset($this->paths[$path])) return;
      $this->paths[$path]= array();
    }
    
    /**
     * Register a certain property object to a specified name
     *
     * @param   string name
     * @param   util.Properties properties
     */
    public function register($name, $properties) {
      $this->paths[$this->_path][$name]= $properties;
    }

    /**
     * Return whether a given property file exists
     *
     * @param   string name
     * @return  bool
     */
    public function hasProperties($name) {
      // First check cache
      foreach ($this->paths as $path => $elements) {
        if (isset($elements[$name])) return TRUE;
      }

      // Second loop checks fs
      foreach ($this->paths as $path => $elements) {
        if (file_exists($path.DIRECTORY_SEPARATOR.$name.'.ini')) return TRUE;
      }

      return FALSE;
    }
   
    /**
     * Return properties by name
     *
     * @param   string name
     * @return  util.Properties
     */
    public function getProperties($name) {
      foreach ($this->paths as $path => $elements) {
        if (isset($elements[$name])) return $elements[$name];
      }

      foreach ($this->paths as $path => $elements) {
        if (isset($elements[$name])) continue;
        if (file_exists($path.DIRECTORY_SEPARATOR.$name.'.ini')) {
          $this->paths[$path][$name]= new Properties(
            $path.DIRECTORY_SEPARATOR.$name.'.ini'
          );
          return $this->paths[$path][$name];
        }
      }
    }
  }
?>
