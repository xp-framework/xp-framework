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
    public static $instance= NULL;
    public
      $classpath= '';
    
    /**
     * Constructor. 
     *
     * The path argument is optional and lets you define where to search for
     * classes (it will be prefixed to the class name).
     *
     * @access  public
     * @param   string path default '' classpath
     */
    public function __construct($path= '') {
      if (!empty($path)) $this->classpath= $path.'.';
    }
    
    /**
     * Retrieve the default class loader
     *
     * @model   static
     * @access  public
     * @return  &lang.ClassLoader
     */
    public static function getDefault() {
      if (!self::$instance) self::$instance= new ClassLoader();
      return self::$instance;
    }
    
    /**
     * Find a class by the specified name (but do not load it)
     *
     * @access  public
     * @param   string class fully qualified class name io.File
     * @return  string filename, FALSE if not found
     */
    public function findClass($class) {
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
    public function loadClass($class) {
      $qname= $this->classpath.$class;
      $name= xp::reflect($qname);

      if (class_exists($name) || uses($qname)) {
        return new XPClass($name);
      }
      
      throw (new ClassNotFoundException('Class "'.$qname.'" not found'));
    }
  }
?>
