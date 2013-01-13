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
  class Module extends Object {
    protected $loader;
    protected $definition;
    protected $name;
    protected $version;

    /**
     * Creates a new instance of a module with a given name.
     *
     * @param  lang.IClassLoader loader
     * @param  lang.XPClass definition
     * @param  string name
     * @param  string version
     */
    public function __construct($loader, $definition, $name= NULL, $version= NULL) {
      $this->loader= $loader;
      $this->definition= $definition;

      // This is a bit redundant, name and version are also static fields inside the
      // class represented by $definition, but retrieving them reflectively is a 
      // unneccessary - we calculate them right before definition anyways!
      $this->name= (NULL === $name ? $definition->getField('name')->get(NULL) : $name);
      $this->version= (NULL === $version ? $definition->getField('version')->get(NULL) : $version);
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
      return xp::nameOf(__CLASS__).'<'.$this->name.(NULL === $this->version ? '' : ':'.$this->version).'>';
    }

    /**
     * Returns a hashcode for this object
     *
     * @return  string
     */
    public function hashCode() {
      return 'module'.$this->name.$this->version;
    }
  }
?>