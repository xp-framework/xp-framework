<?php
/* This class is part of the XP framework
 * 
 * $Id$
 */
 
  uses('lang.IClassLoader');
  
  /** 
   * Loads a class from the filesystem
   * 
   * @purpose  Load classes
   * @see      xp://lang.XPClass#forName
   */
  class FileSystemClassLoader extends Object implements IClassLoader {
    public 
      $path= '';
    
    /**
     * Constructor. 
     *
     * The path argument is optional and lets you define where to search for
     * classes (it will be prefixed to the class name)
     *
     * @param   string path default '' file system path
     */
    public function __construct($path= '') {
      $this->path= realpath($path);
    }
    
    /**
     * Checks whether two class loaders are equal
     *
     * @param   lang.Generic cmp
     * @return  bool
     */
    public function equals($cmp) {
      return $cmp instanceof self && $cmp->path === $this->path;
    }

    /**
     * Creates a string representation
     *
     * @return  string
     */
    public function toString() {
      return $this->getClassName(). '<'.$this->path.'>';
    }

    /**
     * Load class bytes
     *
     * @param   string name fully qualified class name
     * @return  string
     */
    public function loadClassBytes($name) {
      return file_get_contents($this->path.DIRECTORY_SEPARATOR.strtr($name, '.', '/').'.class.php');
    }
    
    /**
     * Checks whether this loader can provide the requested class
     *
     * @param   string class
     * @return  bool
     */
    public function providesClass($class) {
      return is_file($this->path.DIRECTORY_SEPARATOR.strtr($class, '.', '/').'.class.php');
    }

    /**
     * Checks whether this loader can provide the requested package
     *
     * @param   string package
     * @return  bool
     */
    public function providesPackage($package) {
      return is_dir($this->path.DIRECTORY_SEPARATOR.strtr($class, '.', '/'));
    }
    
    /**
     * Load the class by the specified name
     *
     * @param   string class fully qualified class name io.File
     * @return  lang.XPClass
     * @throws  lang.ClassNotFoundException in case the class can not be found
     */
    public function loadClass($class) {
      return new XPClass($this->load($class));
    }

    public function load($class) {
      $name= xp::reflect($class);

      if (!class_exists($name) && !interface_exists($name)) {
        if (FALSE === include($this->path.DIRECTORY_SEPARATOR.strtr($class, '.', DIRECTORY_SEPARATOR).'.class.php')) {
          throw new ClassNotFoundException('Class "'.$class.'" not found');
        }
        xp::$registry['class.'.$name]= $class;
        xp::$registry['classloader.'.$class]= __CLASS__.'://'.$this->path;
        is_callable(array($name, '__static')) && call_user_func(array($name, '__static'));
      }
      return $name;
    }
    
    /**
     * Loads a resource.
     *
     * @param   string filename name of resource
     * @return  string
     * @throws  lang.ElementNotFoundException in case the resource cannot be found
     */
    public function getResource($filename) {
      foreach (array_unique(explode(PATH_SEPARATOR, ini_get('include_path'))) as $dir) {
        if (!file_exists($dir.DIRECTORY_SEPARATOR.$filename)) continue;
        return file_get_contents($dir.DIRECTORY_SEPARATOR.$filename);
      }
    
      return raise('lang.ElementNotFoundException', 'Could not load resource '.$filename);
    }
    
    /**
     * Retrieve a stream to the resource
     *
     * @param   string filename name of resource
     * @return  &io.File
     * @throws  lang.ElementNotFoundException in case the resource cannot be found
     */
    public function getResourceAsStream($filename) {
      foreach (array_unique(explode(PATH_SEPARATOR, ini_get('include_path'))) as $dir) {
        if (!file_exists($dir.DIRECTORY_SEPARATOR.$filename)) continue;
        return new File($filename);
      }
    
      return raise('lang.ElementNotFoundException', 'Could not load resource '.$filename);
    }

    /**
     * Fetch instance of classloader by the path to the archive
     *
     * @param   string path
     * @return  lang.FileSystemClassLoader
     */
    public static function instanceFor($path) {
      static $pool= array();
      
      if (!isset($pool[$path])) {
        $pool[$path]= ClassLoader::registerLoader(new self($path));
      }
      
      return $pool[$path];
    }
  }
?>
