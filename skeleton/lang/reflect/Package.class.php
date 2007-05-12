<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Represents a package
   *
   * @purpose  Reflection
   */
  class Package extends Object {
    protected
      $name= '';

    /**
     * Gets the fully qualified package name
     *
     * @return  string
     */  
    public function getName() {
      return $this->name;
    }
    
    /**
     * Checks if a specific class is provided by this package
     *
     * @param   string name
     * @return  bool
     */
    public function providesClass($name) { 
      return ClassLoader::getDefault()->providesClass($this->name.'.'.$name);
    }

    /**
     * Checks if a specific subpackage is provided by this package
     *
     * @param   string name
     * @return  bool
     */
    public function providesPackage($name) { 
      return ClassLoader::getDefault()->providesPackage($this->name.'.'.$name);
    }

    /**
     * Checks if a specific resource is provided by this package
     *
     * @param   string name
     * @return  bool
     */
    public function providesResource($name) { 
      return ClassLoader::getDefault()->providesResource(strtr($this->name, '.', '/').'/'.$name);
    }

    /**
     * Get all classes in this package. Loads classes if not already
     * loaded.
     *
     * @return  lang.XPClass[]
     */
    public function getClasses() { 
      return array_map(array('XPClass', 'forName'), $this->getClassNames());
    }

    /**
     * Get the names of classes in this package, not loading them.
     *
     * @return  string
     */
    public function getClassNames() { 
      $classes= array();
      foreach (ClassLoader::getDefault()->packageContents($this->name) as $file) {
        if (xp::CLASS_FILE_EXT == substr($file, -10)) $classes[]= $this->name.'.'.substr($file, 0, -10);
      }
      return $classes;
    }
    
    /**
     * Load a specific class by its name, which may be either locally
     * qualified (without dots) or fully qualified (with dots).
     *
     * @param   string name
     * @return  lang.XPClass
     * @throws  lang.IllegalArgumentException
     */
    public function loadClass($name) { 
    
      // Handle fully qualified names
      if (FALSE !== ($p= strrpos($name, '.'))) {
        if (substr($name, 0, $p) != $this->name) {
          throw new IllegalArgumentException('Class '.$name.' is not in '.$this->name);
        }
        $name= substr($name, $p+ 1);
      }

      return XPClass::forName($this->name.'.'.$name);
    }

    /**
     * Returns a list of subpackages in this package.
     *
     * @return  lang.reflect.Package[]
     */
    public function getPackages() {
      return array_map(array('Package', 'forName'), $this->getPackageNames());
    } 

    /**
     * Returns a list of subpackages in this package.
     *
     * @return  string[]
     */
    public function getPackageNames() { 
      $packages= array();
      foreach (ClassLoader::getDefault()->packageContents($this->name) as $file) {
        if ('/' == substr($file, -1)) $packages[]= $this->name.'.'.substr($file, 0, -1);
      }
      return $packages;
    }

    /**
     * Returns a list of resources in this package.
     *
     * @return  string[]
     */
    public function getResources() {
      $resources= array();
      foreach (ClassLoader::getDefault()->packageContents($this->name) as $file) {
        if ('/' == substr($file, -1) || xp::CLASS_FILE_EXT == substr($file, -10)) continue;
        $resources[]= strtr($this->name, '.', '/').'/'.$file;
      }
      return $resources;
    }

    /**
     * Get a specific subpackage of this package by its name, which 
     * may be either locally qualified (without dots) or fully 
     * qualified (with dots).
     *
     * @param   string name
     * @return  lang.reflect.Package
     * @throws  lang.IllegalArgumentException
     */
    public function getPackage($name) {

      // Handle fully qualified names
      if (FALSE !== ($p= strrpos($name, '.'))) {
        if (substr($name, 0, $p) != $this->name) {
          throw new IllegalArgumentException('Package '.$name.' is not in '.$this->name);
        }
        $name= substr($name, $p+ 1);
      }

      return self::forName($this->name.'.'.$name);
    }
    
    /**
     * Returns a Package object for a given fully qualified name.
     *
     * @param   string name
     * @return  lang.reflect.Package
     * @throws  lang.ElementNotFoundException
     */
    public static function forName($name) { 
      $p= new self();
      $p->name= rtrim($name, '.');   // Normalize
      
      if (!ClassLoader::getDefault()->providesPackage($p->name)) {
        raise('lang.ElementNotFoundException', 'No classloaders provide '.$name);
      }
      return $p;
    }

    /**
     * Loads a resource.
     *
     * @param   string filename name of resource
     * @return  string
     * @throws  lang.ElementNotFoundException in case the resource cannot be found
     */
    public function getResource($filename) {

      // Handle fully qualified names
      if (FALSE !== ($p= strrpos($filename, '/'))) {
        if (substr($filename, 0, $p) != strtr($this->name, '.', '/')) {
          throw new IllegalArgumentException('Resource '.$filename.' is not in '.$this->name);
        }
        $filename= substr($filename, $p+ 1);
      }
      return ClassLoader::getDefault()->getResource(strtr($this->name, '.', '/').'/'.$filename);
    }
    
    /**
     * Retrieve a stream to the resource
     *
     * @param   string filename name of resource
     * @return  io.File
     * @throws  lang.ElementNotFoundException in case the resource cannot be found
     */
    public function getResourceAsStream($filename) {

      // Handle fully qualified names
      if (FALSE !== ($p= strrpos($filename, '/'))) {
        if (substr($filename, 0, $p) != strtr($this->name, '.', '/')) {
          throw new IllegalArgumentException('Resource '.$filename.' is not in '.$this->name);
        }
        $filename= substr($filename, $p+ 1);
      }
      return ClassLoader::getDefault()->getResourceAsStream(strtr($this->name, '.', '/').'/'.$filename);
    }
    
    /**
     * Creates a string representation of this package
     * 
     * Example:
     * <pre>
     *   lang.reflect.Package<fully.qualified.package.Name>
     * </pre>
     *
     * @return  string
     */
    public function toString() { 
      return $this->getClassName().'<'.$this->name.'>';
    }

    /**
     * Checks whether a given object is equal to this Package instance.
     * 
     * @param   lang.Generic cmp
     * @return  bool
     */
    public function equals($cmp) { 
      return $cmp instanceof self && $this->name === $cmp->name;
    }

    /**
     * Creates a hashcode for this package
     * 
     */
    public function hashCode() {
      return 'P['.$this->name;
    }
  }
?>
