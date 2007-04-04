<?php
/* This class is part of the XP framework
 *
 * $Id$ 
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
      foreach ($this->loaders as $loader) {
        if ($loader->providesClass($this->name.$name)) return TRUE;
      }
      return FALSE;   // No loader provides this class
    }

    /**
     * Get all classes in this package. Loads classes if not already
     * loaded.
     *
     * @return  lang.XPClass[]
     */
    public function getClasses() { 
      // TBI
    }

    /**
     * Get the names of classes in this package, not loading them.
     *
     * @return  string
     */
    public function getClassNames() { 
      // TBI
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
        $name= substr($name, $p);
      }

      return XPClass::forName($this->name.$name);
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
    public static function forName($name, $classloader= NULL) { 
      if (!$classloader) {
        $classloaders= array();
        $pname= strtr('.', DIRECTORY_SEPARATOR, $name);
        foreach (explode(PATH_SEPARATOR, ini_get('include_path')) as $path) {

          // File-system based
          if (is_dir($path) && is_dir($path.DIRECTORY_SEPARATOR.$pname)) {
            $classloaders[]= ClassLoader::getDefault();
            continue;
          }

          // Archive-based
          if (is_file($path)) {
            $cl= ArchiveClassLoader::instanceFor($path);
            $cl->archive->rewind();
            while ($id= $cl->archive->getEntry()) {
              if (0 == strncmp($id, $pname, strlen($pname))) {
                $classloader= $cl;
                continue 2;
              }
            }
          }
        }

        if (!$classloaders) {
          throw new IllegalArgumentException('Package "'.$name.'" not found');
        }
      } else {
        $classloaders= array($classloader);
      }

      $p= new self();
      $p->name= rtrim($name, '.').'.';   // Normalize
      $p->loaders= $classloaders;
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
