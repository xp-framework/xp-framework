<?php
/* This class is part of the XP framework
 *
 * $Id: Package.class.php 9847 2007-04-04 10:58:40Z friebe $ 
 */

  /**
   * (Insert class' description here)
   *
   * @purpose  Represent a package
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
     * Get all classes in this package. Loads classes if not already
     * loaded.
     *
     * @param   string name
     * @return  bool
     */
    public function providesClass($name) { 
      return !is(NULL, ClassLoader::getDefault()->findClass($this->name.'.'.$name));
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
        if ('.class.php' == substr($file, -10)) $classes[]= $this->name.'.'.substr($file, 0, -10);
      }
      return $classes;
    }
    
    /**
     * Load a specific class by its name, which may be either locally
     * qualified (without dots) or fully qualified (with dots).
     *
     * @param   string name
     * @return  lang.XPClass
     * @throws  lang.IllegalArgumentException in case 
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
     */
    public function getPackages() { 
      // TBI
    }

    /**
     * Get a specific subpackage of this package by its name, which 
     * may be either locally qualified (without dots) or fully 
     * qualified (with dots).
     *
     */
    public function getPackage($name) {
      // TBI
    }
    
    /**
     * Returns a Package object for a given fully qualified name.
     *
     * @param   string name
     * @return  lang.reflect.Package
     */
    public static function forName($name) { 
      $p= new self();
      $p->name= rtrim($name, '.');   // Normalize
      return $p;
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
