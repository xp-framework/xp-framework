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
   * @see   https://gist.github.com/4701515
   */
  class Module extends Object implements IClassLoader {
    private $lookup= array();

    protected $delegates= array();
    protected $definition;
    protected $name;
    protected $version;

    /**
     * Creates a new instance of a module with a given name.
     *
     * @param  lang.XPClass definition
     * @param  string name
     * @param  string version
     */
    public function __construct($definition, $name= NULL, $version= NULL) {
      $this->definition= $definition;

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
      foreach ($this->lookup as $l) {
        if (
          (isset($l[1]) || 0 === strncmp($class, $l[1], strlen($l[1]))) &&
          $l[0]->providesClass($class)
        ) return TRUE;
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
      $cmp= strtr($filename, '/', '.');
      foreach ($this->lookup as $l) {
        if (
          (isset($l[1]) || 0 === strncmp($cmp, $l[1], strlen($l[1]))) &&
          $l[0]->providesResource($filename)
        ) return TRUE;
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
      foreach ($this->lookup as $l) {
        if (
          (isset($l[1]) || 0 === strncmp($name, $l[1], strlen($l[1]))) &&
          $l[0]->providesPackage($name)
        ) return TRUE;
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
      $contents= array();
      foreach ($this->delegates as $l) {
        $contents= array_merge($contents, $l->packageContents($package));
      }
      return $contents;
    }

    /**
     * Load the class by the specified name
     *
     * @param   string class fully qualified class name io.File
     * @return  lang.XPClass
     * @throws  lang.ClassNotFoundException in case the class can not be found
     */
    public function loadClass($class) {
      foreach ($this->delegates as $l) {
        if ($l->providesClass($class)) return $l->loadClass($class);
      }
      throw new ClassNotFoundException('Cannot find class '.$class);
    }

    /**
     * Load the class by the specified name
     *
     * @param   string class fully qualified class name io.File
     * @return  string class name
     * @throws  lang.ClassNotFoundException in case the class can not be found
     */
    public function loadClass0($class) {
      foreach ($this->delegates as $l) {
        if ($l->providesClass($class)) return $l->loadClass0($class);
      }
      throw new ClassNotFoundException('Cannot find class '.$class);
    }

    /**
     * Loads a resource.
     *
     * @param   string string name of resource
     * @return  string
     * @throws  lang.ElementNotFoundException in case the resource cannot be found
     */
    public function getResource($string) {
      foreach ($this->delegates as $l) {
        if ($l->providesResource($string)) return $l->getResource($string);
      }
      throw new ElementNotFoundException('Cannot find resource '.$string);
    }

    /**
     * Retrieve a stream to the resource
     *
     * @param   string string name of resource
     * @return  io.Stream
     * @throws  lang.ElementNotFoundException in case the resource cannot be found
     */
    public function getResourceAsStream($string) {
      foreach ($this->delegates as $l) {
        if ($l->providesResource($string)) return $l->getResourceAsStream($string);
      }
      throw new ElementNotFoundException('Cannot find resource '.$string);
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
     * Retrieve class loaders associated with this module
     *
     * @param   string name The delegate
     * @return  lang.IClassLoader
     * @throws  lang.IllegalArgumentException If no delegate with the specified name exists
     */
    public function getDelegate($name) {
      if (!isset($this->delegates[$name])) {
        throw new IllegalArgumentException('No delegate named "'.$name.'"');
      }
      return $this->delegates[$name];
    }

    /**
     * Retrieve class loaders associated with this module
     *
     * @return  [:lang.IClassLoader]
     */
    public function getDelegates() {
      return $this->delegates;
    }

    /**
     * Add a class loader to the class loader lookup associated with this module
     *
     * @param   string name
     * @param   lang.IClassLoader l
     * @param   string[] provides
     */
    public function addDelegate($name, $l, $provides) {
      $this->delegates[$name]= $l;
      foreach ($provides as $package) {
        $this->lookup[]= array($l, $package);
      }
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
      $lookup= '';
      foreach ($this->lookup as $delegate) {
        $lookup.= '  '.(isset($delegate[1]) ? $delegate[1] : '**').': '.$delegate[0]->toString()."\n";
      }
      return sprintf(
        "Module<%s%s>@[\n%s]",
        $this->name,
        NULL === $this->version ? '' : ':'.$this->version,
        $lookup
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
      return $this->delegates[NULL]->instanceId();
    }
  }
?>
