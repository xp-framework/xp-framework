<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('util.Properties', 'util.CompositeProperties');
  
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
   * @test      xp://net.xp_framework.unittest.util.PropertyManagerTest
   * @purpose  Container
   */
  class PropertyManager extends Object {
    const
      MEMORY  = ':mem;';      // Special name for in-memory properties' section

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
      $this->appendPath($path);
    }

    /**
     * Append path to paths to search
     *
     * @param   string path
     */
    public function appendPath($path) {
      if (isset($this->paths[$path])) return;
      $this->paths[$path]= array();
    }

    /**
     * Prepend path to paths to search
     *
     * @param   string path
     */
    public function prependPath($path) {
      if (isset($this->paths[$path])) return;
      $this->paths= array_merge(array($path => array()), $this->paths);
    }

    /**
     * Register a certain property object to a specified name
     *
     * @param   string name
     * @param   util.Properties properties
     */
    public function register($name, $properties) {
      if (!isset($this->paths[$name])) $this->prependPath(self::MEMORY);
      $this->paths[self::MEMORY][$name]= $properties;
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
        if (self::MEMORY == $path) continue;
        if (file_exists($path.DIRECTORY_SEPARATOR.$name.'.ini')) return TRUE;
      }

      return FALSE;
    }
   
    /**
     * Return properties by name
     *
     * @param   string name
     * @return  util.PropertyAccess
     */
    public function getProperties($name) {
      $found= array();
      foreach ($this->paths as $path => $elements) {
        if (isset($elements[$name])) {
          $found[]= $elements[$name];
        }
      }

      foreach ($this->paths as $path => $elements) {
        if (self::MEMORY == $path) continue;
        if (isset($elements[$name])) continue;

        if (file_exists($path.DIRECTORY_SEPARATOR.$name.'.ini')) {
          $this->paths[$path][$name]= new Properties(
            $path.DIRECTORY_SEPARATOR.$name.'.ini'
          );
          $found[]= $this->paths[$path][$name];
        }
      }

      if (0 == sizeof($found)) return NULL;
      if (1 == sizeof($found)) return $found[0];
      return new CompositeProperties(array_shift($found), $found);
    }
  }
?>
