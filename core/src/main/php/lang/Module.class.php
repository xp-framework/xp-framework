<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * A module represents a named class path.
   *
   * @test  xp://net.xp_framework.unittest.core.modules.AnnotatedModuleTest
   * @test  xp://net.xp_framework.unittest.core.modules.ImagingModuleTest
   * @test  xp://net.xp_framework.unittest.core.modules.CoreModuleTest
   * @test  xp://net.xp_framework.unittest.core.modules.ModuleTest
   * @test  xp://net.xp_framework.unittest.core.modules.ModuleWithStaticInitializerTest
   * @see   https://github.com/xp-framework/rfc/issues/220
   */
  class Module extends Object implements IClassLoader {
    protected $loader;
    protected $definition;
    protected $name;
    protected $provides;
    protected $version;

    /**
     * Creates a new instance of a module with a given name.
     *
     * @param  lang.IClassLoader loader
     * @param  lang.XPClass definition
     * @param  string[] provides
     * @param  string name
     * @param  string version
     */
    public function __construct($loader, $definition, $provides= NULL, $name= NULL, $version= NULL) {
      $this->loader= $loader;
      $this->definition= $definition;
      $this->provides= $provides;

      // This is a bit redundant, name and version are also static fields inside the
      // class represented by $definition, but retrieving them reflectively is a 
      // unneccessary - we calculate them right before definition anyways!
      $this->name= (NULL === $name ? $definition->getField('name')->get(NULL) : $name);
      $this->version= (NULL === $version ? $definition->getField('version')->get(NULL) : $version);
    }

    /**
     * Checks whether this loader can provide the requested class
     *
     * @param   string class
     * @return  bool
     */
    public function providesClass($class) {
      if (NULL === $this->provides) return $this->loader->providesClass($class);
      foreach ($this->provides as $package) {
        if (0 === strncmp($class, $package, strlen($package))) return $this->loader->providesClass($class);
      }
      return FALSE;
    }

    /**
     * Checks whether this loader can provide the requested resource
     *
     * @param   string filename
     * @return  bool
     */
    public function providesResource($filename) {
      if (NULL === $this->provides) return $this->loader->providesResource($filename);
      $cmp= strtr($filename, '/', '.');
      foreach ($this->provides as $package) {
        if (0 === strncmp($cmp, $package, strlen($package))) return $this->loader->providesResource($filename);
      }
      return FALSE;
    }

    /**
     * Checks whether this loader can provide the requested package
     *
     * @param   string name
     * @return  bool
     */
    public function providesPackage($name) {
      if (NULL === $this->provides) return $this->loader->providesPackage($name);
      foreach ($this->provides as $package) {
        if (0 === strncmp($name, $package, strlen($package))) return $this->loader->providesPackage($name);
      }
      return FALSE;
    }

    /**
     * Get package contents
     *
     * @param   string package
     * @return  string[] filenames
     */
    public function packageContents($package) {
      return $this->loader->packageContents($package);
    }

    /**
     * Load the class by the specified name
     *
     * @param   string class fully qualified class name io.File
     * @return  lang.XPClass
     * @throws  lang.ClassNotFoundException in case the class can not be found
     */
    public function loadClass($class) {
      return $this->loader->loadClass($class);
    }

    /**
     * Load the class by the specified name
     *
     * @param   string class fully qualified class name io.File
     * @return  string class name
     * @throws  lang.ClassNotFoundException in case the class can not be found
     */
    public function loadClass0($class) {
      return $this->loader->loadClass0($class);
    }

    /**
     * Loads a resource.
     *
     * @param   string string name of resource
     * @return  string
     * @throws  lang.ElementNotFoundException in case the resource cannot be found
     */
    public function getResource($string) {
      return $this->loader->getResource($string);
    }

    /**
     * Retrieve a stream to the resource
     *
     * @param   string string name of resource
     * @return  io.Stream
     * @throws  lang.ElementNotFoundException in case the resource cannot be found
     */
    public function getResourceAsStream($string) {
      return $this->loader->getResourceAsStream($string);
    }
    
    /**
     * Returns module name
     *
     * @return  string
     */
    public function getName() {
      return $this->name;
    }

    /**
     * Returns module version or NULL if no version is set
     *
     * @return  string
     */
    public function getVersion() {
      return $this->version;
    }

    /**
     * Returns comment
     *
     * @return  string
     */
    public function getComment() {
      return $this->definition->getComment();
    }

    /**
     * Check whether an annotation exists
     *
     * @param   string name
     * @param   string key default NULL
     * @return  bool
     */
    public function hasAnnotation($name, $key= NULL) {
      return $this->definition->hasAnnotation($name, $key);
    }

    /**
     * Retrieve annotation by name
     *
     * @param   string name
     * @param   string key default NULL
     * @return  var
     * @throws  lang.ElementNotFoundException
     */
    public function getAnnotation($name, $key= NULL) {
      return $this->definition->getAnnotation($name, $key);
    }

    /**
     * Retrieve whether this module has annotations
     *
     * @return  bool
     */
    public function hasAnnotations() {
      return $this->definition->hasAnnotations();
    }

    /**
     * Retrieve all of a module's annotations
     *
     * @return  var[] annotations
     */
    public function getAnnotations() {
      return $this->definition->getAnnotations();
    }

    /**
     * Retrieve class loader associated with this module
     *
     * @return  lang.IClassLoader
     */
    public function getClassLoader() {
      return $this->loader;
    }

    /**
     * Gets a module by a given name
     *
     * @param   string name
     * @return  lang.reflect.Module
     * @throws  lang.ElementNotFoundException if the module doesn't exist
     */
    public static function forName($name) {
      if (!isset(xp::$registry['modules'][$name])) {
        raise('lang.ElementNotFoundException', 'No such module '.$name);
      }
      
      return xp::$registry['modules'][$name];
    }

    /**
     * Gets all declared modules
     *
     * @return  lang.reflect.Module[]
     */
    public static function getModules() {
      $r= array();
      foreach (xp::$registry['modules'] as $name => $instance) {
        $r[]= $instance;
      }
      return $r;
    }

    /**
     * Returns whether another module is equal to this module.
     *
     * @param   var cmo
     * @return  bool
     */
    public function equals($cmp) {
      return (
        $cmp instanceof self &&
        $cmp->name === $this->name &&
        $cmp->version === $this->version
      );
    }

    /**
     * Creates a string representation of this object
     *
     * @return  string
     */
    public function toString() {
      return sprintf(
        'Module<%s%s, %s>',
        $this->name,
        NULL === $this->version ? '' : ':'.$this->version,
        $this->loader->toString()
      );
    }

    /**
     * Returns a hashcode for this object
     *
     * @return  string
     */
    public function hashCode() {
      return 'module@'.$this->name.$this->version;
    }

    /**
     * Returns a unique identifier for this class loader instance
     *
     * @return  string
     */
    public function instanceId() {
      return $this->loader->instanceId();
    }
  }
?>
