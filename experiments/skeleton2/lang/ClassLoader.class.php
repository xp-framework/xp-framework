<?php
/* This class is part of the XP framework
 * 
 * $Id$
 */
 
  uses('lang.ClassNotFoundException');
  
  /** 
   * Loads a class
   * 
   * Usage (static calls):
   * <code>
   *   try(); {
   *     $name= ClassLoader::loadClass($argv[1]);
   *   } if (catch('ClassNotFoundException', $e)) {
   *     die($e->printStackTrace());
   *   }
   *
   *   $obj= &new $name();
   * </code>
   *
   * Usage (class loader instance):
   * <code>
   *   $loader= &new ClassLoader('info.binford6100.webservices');
   *   try();
   *     $name= $loader->loadClass($argv[1]);
   *   } if (catch('ClassNotFoundException', $e)) {
   *     die($e->printStackTrace());
   *   }
   *
   *   $obj= &new $name();
   * </code>
   *
   * @access    public, static
   */
  class ClassLoader extends Object {
    var $classpath= '';
    
    /**
     * Constructor. 
     * The path argument is optional and lets you define where to search for
     * classes (it will be prefixed to the class name)
     *
     * @access  public
     * @param   string path default '' classpath
     */
    function __construct($path= '') {
      if (!empty($path)) $this->classpath= $path.'.';
      parent::__construct();
    }
    
    /**
     * Returns whether this class is a built-in class (e.g., "Directory")
     *
     * @access  static
     * @param   string name
     * @return  bool true if name represents a built-in class
     */
    function isBuiltin($name) {
      return ('php.' == substr($name, 0, 4));
    }
    
    /**
     * Load
     *
     * @access  public, static
     * @param   string className fully qualified class name io.File
     * @return  string class' name for instantiation
     * @throws  ClassNotFoundException in case the class can not be found
     */
    function loadClass($className) {
      if (!ClassLoader::isBuiltin($className)) {
        $str= (isset($this) ? @$this->classpath : '').$className;
        uses($str);
        $phpName= xp::reflect($str);
      } else {
        $phpName= substr($className, 4);
      }
      if (class_exists($phpName)) return $phpName;
      
      throw(new ClassNotFoundException(sprintf(
        'class "%s" [%s] not found',
        $className,
        $phpName
      )));
    }
  }
?>
