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
      $this->path= rtrim(realpath($path), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
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
      return file_get_contents($this->path.strtr($name, '.', DIRECTORY_SEPARATOR).xp::CLASS_FILE_EXT);
    }
    
    /**
     * Checks whether this loader can provide the requested class
     *
     * @param   string class
     * @return  bool
     */
    public function providesClass($class) {
      return is_file($this->path.strtr($class, '.', DIRECTORY_SEPARATOR).xp::CLASS_FILE_EXT);
    }
    
    /**
     * Checks whether this loader can provide the requested resource
     *
     * @param   string filename
     * @return  bool
     */
    public function providesResource($filename) {
      return is_file($this->path.$filename);
    }

    /**
     * Checks whether this loader can provide the requested package
     *
     * @param   string package
     * @return  bool
     */
    public function providesPackage($package) {
      return is_dir($this->path.strtr($package, '.', DIRECTORY_SEPARATOR));
    }
    
    /**
     * Load the class by the specified name
     *
     * @param   string class fully qualified class name io.File
     * @return  lang.XPClass
     * @throws  lang.ClassNotFoundException in case the class can not be found
     */
    public function loadClass($class) {
      return new XPClass($this->loadClass0($class));
    }

    /**
     * Load the class by the specified name
     *
     * @param   string class fully qualified class name io.File
     * @return  string class name
     * @throws  lang.ClassNotFoundException in case the class can not be found
     */
    public function loadClass0($class) {
      if (isset(xp::$registry['classloader.'.$class])) {
        return substr(array_search($class, xp::$registry), 6);
      }

      xp::$registry['classloader.'.$class]= __CLASS__.'://'.$this->path;
      $package= NULL;
      if (FALSE === include(ClassLoader::$transform.$this->path.strtr($class, '.', DIRECTORY_SEPARATOR).xp::CLASS_FILE_EXT)) {
        unset(xp::$registry['classloader.'.$class]);
        throw new ClassNotFoundException('Class "'.$class.'" not found');
      }
      $name= ($package ? strtr($package, '.', '·').'·' : '').xp::reflect($class);
      xp::$registry['class.'.$name]= $class;
      is_callable(array($name, '__static')) && call_user_func(array($name, '__static'));
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
      if (!is_file($this->path.strtr($filename, '/', DIRECTORY_SEPARATOR))) {
        return raise('lang.ElementNotFoundException', 'Could not load resource '.$filename);
      }
      return file_get_contents($this->path.$filename);
    }
    
    /**
     * Retrieve a stream to the resource
     *
     * @param   string filename name of resource
     * @return  io.File
     * @throws  lang.ElementNotFoundException in case the resource cannot be found
     */
    public function getResourceAsStream($filename) {
      if (!is_file($this->path.strtr($filename, '/', DIRECTORY_SEPARATOR))) {
        return raise('lang.ElementNotFoundException', 'Could not load resource '.$filename);
      }
      return new File($this->path.$filename);
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
        $pool[$path]= new self($path);
      }
      
      return $pool[$path];
    }

    /**
     * Get package contents
     *
     * @param   string package
     * @return  string[] filenames
     */
    public function packageContents($package) {
      $contents= array();
      if ($d= @dir($this->path.strtr($package, '.', DIRECTORY_SEPARATOR))) {
        while ($e= $d->read()) {
          if ('.' != $e{0}) $contents[]= $e.(is_dir($d->path.DIRECTORY_SEPARATOR.$e) ? '/' : '');
        }
        $d->close();
      }
      return $contents;
    }
  }
?>
