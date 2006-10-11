<?php
/* This class is part of the XP framework
 * 
 * $Id$
 */
 
  uses('lang.ClassNotFoundException');
  
  /** 
   * Loads a class
   * 
   * @purpose  Load classes
   * @test     xp://net.xp_framework.unittest.reflection.ClassLoaderTest
   * @see      xp://lang.XPClass#forName
   */
  class ClassLoader extends Object {
    var 
      $classpath= '';
    
    /**
     * Constructor. 
     *
     * The path argument is optional and lets you define where to search for
     * classes (it will be prefixed to the class name)
     *
     * @access  public
     * @param   string path default '' classpath
     */
    function __construct($path= '') {
      if (!empty($path)) $this->classpath= $path.'.';
    }

    /**
     * Load class bytes
     *
     * @access  public
     * @param   string name fully qualified class name
     * @return  string
     */
    function loadClassBytes($name) {
      return file_get_contents($this->findClass($name));
    }
    
    /**
     * Retrieve the default class loader
     *
     * @model   static
     * @access  public
     * @return  &lang.ClassLoader
     */
    function &getDefault() {
      static $instance= NULL;
      
      if (!$instance) $instance= new ClassLoader();
      return $instance;
    }
    
    /**
     * Find a class by the specified name (but do not load it)
     *
     * @access  public
     * @param   string class fully qualified class name io.File
     * @return  string filename, FALSE if not found
     */
    function findClass($class) {
      if (!$class) return FALSE;    // Border case

      $filename= str_replace('.', DIRECTORY_SEPARATOR, $this->classpath.$class).'.class.php';
      foreach (array_unique(explode(PATH_SEPARATOR, ini_get('include_path'))) as $dir) {
        if (!file_exists($dir.DIRECTORY_SEPARATOR.$filename)) continue;
        return realpath($dir.DIRECTORY_SEPARATOR.$filename);
      }
      return FALSE;
    }
    
    /**
     * Load the class by the specified name
     *
     * @access  public
     * @param   string class fully qualified class name io.File
     * @return  &lang.XPClass
     * @throws  lang.ClassNotFoundException in case the class can not be found
     */
    function &loadClass($class) {
      $name= xp::reflect($class);

      if (!class_exists($name)) {
        $qname= $this->classpath.$class;
        if (FALSE === include(strtr($qname, '.', DIRECTORY_SEPARATOR).'.class.php')) {
          return throw(new ClassNotFoundException('Class "'.$qname.'" not found'));
        }
        xp::registry('class.'.$name, $qname);
        is_callable(array($name, '__static')) && call_user_func(array($name, '__static'));
      }

      $c= &new XPClass($name);
      return $c;
    }

    /**
     * Define a class with a given name
     *
     * @access  protected
     * @param   string class fully qualified class name
     * @param   string bytes sourcecode of the class
     * @return  &lang.XPClass
     * @throws  lang.FormatException in case the class cannot be defined
     */
    function &_defineClassFromBytes($class, $bytes) {
      $name= xp::reflect($class);

      if (!class_exists($name)) {
        $qname= $this->classpath.$class;
        if (FALSE === eval($bytes)) {
          return throw(new FormatException('Cannot define class "'.$qname.'"'));
        }
        if (!class_exists($name)) {
          return throw(new FormatException('Class "'.$qname.'" not defined'));
        }
        xp::registry('class.'.$name, $qname);
        is_callable(array($name, '__static')) && call_user_func(array($name, '__static'));
      }      

      $c= &new XPClass($name);
      return $c;
    }
    
    /**
     * Define a class with a given name
     *
     * @access  public
     * @param   string class fully qualified class name
     * @param   string parent either sourcecode of the class or FQCN of parent
     * @param   string[] interfaces default NULL FQCNs of implemented interfaces
     * @param   string bytes default NULL inner sourcecode of class (containing {}) 
     * @return  &lang.XPClass
     * @throws  lang.FormatException in case the class cannot be defined
     * @throws  lang.ClassNotFoundException if given parent class does not exist
     */
    function &defineClass($class, $parent, $interfaces= NULL, $bytes= NULL) {
      
      // If invoked with less than four arguments, old behaviour will be executed
      if (NULL === $bytes) {
        return $this->_defineClassFromBytes($class, $parent);
      }
      
      $name= xp::reflect($class);
      if (!class_exists($name)) {
        $qname= $this->classpath.$class;
        $parentName= xp::reflect($parent);
        
        if (!class_exists($parentName)) {
          return throw(new ClassNotFoundException('Parent class '.$parent.' does not exist.'));
        }
        
        $newBytes= 'class '.$name.' extends '.$parentName.' '.$bytes;
        if (FALSE === eval($newBytes)) {
          return throw(new FormatException('Cannot define class "'.$qname.'"'));
        }
        
        if (!class_exists($name)) {
          return throw(new FormatException('Class "'.$qname.'" not defined'));
        }
        
        xp::registry('class.'.$name, $qname);
        if (sizeof($interfaces)) { xp::implements($name, $interfaces); }
        is_callable(array($name, '__static')) && call_user_func(array($name, '__static'));
      }
      
      $c= &new XPClass($name);
      return $c;
    }
    
    /**
     * Loads a resource.
     *
     * @access  public
     * @param   string string name of resource
     * @return  string
     */
    function getResource($string) {
      foreach (array_unique(explode(PATH_SEPARATOR, ini_get('include_path'))) as $dir) {
        if (!file_exists($dir.DIRECTORY_SEPARATOR.$filename)) continue;
        return file_get_contents($dir.DIRECTORY_SEPARATOR.$filename);
      }
    
      return throw(new ElementNotFoundException('Could not load resource '.$string));
    }
    
    /**
     * Retrieve a stream to the resource
     *
     * @access  public
     * @param   string string name of resource
     * @return  &io.File
     */
    function getResourceAsStream($string) {
      foreach (array_unique(explode(PATH_SEPARATOR, ini_get('include_path'))) as $dir) {
        if (!file_exists($dir.DIRECTORY_SEPARATOR.$filename)) continue;
        return new File($filename);
      }
    
      return throw(new ElementNotFoundException('Could not load resource '.$string));
    }
  }
?>
