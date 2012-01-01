<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'util.Properties',
    'util.CompositeProperties',
    'util.RegisteredPropertySource',
    'util.FilesystemPropertySource'
  );
  
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
    protected static 
      $instance     = NULL;

    protected
      $provider     = array();

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
      $this->appendPath(new FilesystemPropertySource($path));
    }

    /**
     * Check if given source is new source
     *
     * @param   util.PropertySource p
     * @return  bool
     */
    public function hasPath(PropertySource $p) {
      foreach ($this->provider as $provider) {
        if ($provider->equals($p)) return TRUE;
      }

      return FALSE;
    }

    /**
     * Append path to paths to search
     *
     * @param   util.PropertySource path
     */
    public function appendPath(PropertySource $path) {
      if ($this->hasPath($path)) return;
      $this->provider[]= $path;
    }

    /**
     * Prepend path to paths to search
     *
     * @param   util.PropertySource path
     */
    public function prependPath(PropertySource $path) {
      if ($this->hasPath($path)) return;
      array_unshift($this->provider, $path);
    }

    /**
     * Remove path from search list
     *
     * @param   util.PropertySource path
     * @return  bool whether the path was removed
     */
    public function removePath(PropertySource $path) {
      foreach ($this->provider as $i => $provider) {
        if (!$provider->equals($path)) continue;
        unset($this->provider[$i]);
        return TRUE;
      }

      return FALSE;
    }

    /**
     * Register a certain property object to a specified name
     *
     * @param   string name
     * @param   util.Properties properties
     */
    public function register($name, $properties) {
      $this->prependPath(new RegisteredPropertySource($name, $properties));
    }

    /**
     * Return whether a given property file exists
     *
     * @param   string name
     * @return  bool
     */
    public function hasProperties($name) {
      foreach ($this->provider as $path) {
        if ($path->provides($name)) return TRUE;
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

      foreach ($this->provider as $path) {
        if ($path->provides($name)) {
          $found[]= $path->fetch($name);
        }
      }

      if (0 == sizeof($found)) return NULL;
      if (1 == sizeof($found)) return $found[0];
      return new CompositeProperties($found);
    }
  }
?>
