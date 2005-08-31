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
        if (!include_once(strtr($qname, '.', DIRECTORY_SEPARATOR).'.class.php')) {
          return throw(new ClassNotFoundException('Class "'.$qname.'" not found'));
        }
        xp::registry('class.'.$name, $qname);
        is_callable(array($name, '__static')) && call_user_func(array($name, '__static'));
      }
      return new XPClass($name);
    }

    /**
     * Define a class with a given name
     *
     * @access  public
     * @param   string class fully qualified class name
     * @param   string bytes sourcecode of the class
     * @return  &lang.XPClass
     * @throws  lang.FormatException in case the class cannot be defined
     */
    function &defineClass($class, $bytes) {
      $name= xp::reflect($class);

      if (!class_exists($name)) {
        $qname= $this->classpath.$class;
        if (FALSE === eval($bytes)) {
          return throw(new FormatException('Cannot define class "'.$qname.'"'));
        }
        xp::registry('class.'.$name, $qname);
        is_callable(array($name, '__static')) && call_user_func(array($name, '__static'));
      }      
      return new XPClass($name);
    }
  }
?>
