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
      $this->appendSource(new FilesystemPropertySource($path));
    }

    /**
     * Check if given source is new source
     *
     * @param   util.PropertySource source
     * @return  bool
     */
    public function hasSource(PropertySource $source) {
      return isset($this->provider[$source->hashCode()]);
    }

    /**
     * Append path to paths to search
     *
     * @param   util.PropertySource source
     * @return  util.PropertySource the added path
     */
    public function appendSource(PropertySource $source) {
      $this->provider[$source->hashCode()]= $source;
      return $source;
    }

    /**
     * Set path to paths to search
     *
     * @param   util.PropertySource[] source
     */
    public function setSources(array $sources) {
      $provider= $this->provider;
      $this->provider= array();
      try {
        foreach ($sources as $source) {
          $this->appendSource($source);
        }
      } catch (IllegalArgumentException $e) {
        $this->provider= $provider;
        throw $e;
      }
    }

    /**
     * Prepend path to paths to search
     *
     * @param   util.PropertySource source
     * @return  util.PropertySource the added path
     */
    public function prependSource(PropertySource $source) {
      if (!$this->hasSource($source)) $this->provider= array_merge(array($source->hashCode() => $source), $this->provider);
      return $source;
    }

    /**
     * Get all paths used to search
     *
     * @return  util.PropertySource[]
     */
    public function getSources() {
      return array_values($this->provider);
    }

    /**
     * Remove path from search list
     *
     * @param   util.PropertySource source
     * @return  bool whether the path was removed
     */
    public function removeSource(PropertySource $source) {
      $removed= isset($this->provider[$source->hashCode()]);
      unset($this->provider[$source->hashCode()]);
      return $removed;
    }

    /**
     * Register a certain property object to a specified name
     *
     * @param   string name
     * @param   util.Properties properties
     */
    public function register($name, $properties) {
      $this->prependSource(new RegisteredPropertySource($name, $properties));
    }

    /**
     * Return whether a given property file exists
     *
     * @param   string name
     * @return  bool
     */
    public function hasProperties($name) {
      foreach ($this->provider as $source) {
        if ($source->provides($name)) return TRUE;
      }

      return FALSE;
    }
   
    /**
     * Return properties by name
     *
     * @param   string name
     * @return  util.PropertyAccess
     * throws   lang.ElementNotFoundException
     */
    public function getProperties($name) {
      $found= array();

      foreach ($this->provider as $source) {
        if ($source->provides($name)) {
          $found[]= $source->fetch($name);
        }
      }

      switch (sizeof($found)) {
        case 1: return $found[0];
        case 0: raise('lang.ElementNotFoundException', sprintf(
          'Cannot find properties "%s" in any of %s',
          $name,
          xp::stringOf(array_values($this->provider))
        ));
        default: return new CompositeProperties($found);
      }
	  }
  }
?>
