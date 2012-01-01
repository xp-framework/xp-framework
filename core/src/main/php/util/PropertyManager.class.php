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
      return isset($this->provider[$p->hashCode()]);
    }

    /**
     * Append path to paths to search
     *
     * @param   util.PropertySource path
     * @return  util.PropertySource the added path
     */
    public function appendPath(PropertySource $path) {
      if (!$this->hasPath($path)) $this->provider[$path->hashCode()]= $path;
      return $path;
    }

    /**
     * Prepend path to paths to search
     *
     * @param   util.PropertySource path
     * @return  util.PropertySource the added path
     */
    public function prependPath(PropertySource $path) {
      if (!$this->hasPath($path)) $this->provider= array_merge(array($path->hashCode() => $path), $this->provider);
      return $path;
    }

    /**
     * Get all paths used to search
     *
     * @return  util.PropertySource[]
     */
    public function getPaths() {
      return array_values($this->provider);
    }

    /**
     * Remove path from search list
     *
     * @param   util.PropertySource path
     * @return  bool whether the path was removed
     */
    public function removePath(PropertySource $path) {
      $removed= isset($this->provider[$path->hashCode()]);
      unset($this->provider[$path->hashCode()]);
      return $removed;
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
