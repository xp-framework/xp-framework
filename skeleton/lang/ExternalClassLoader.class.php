<?php
/* This class is part of the XP framework
 * 
 * $Id$
 */
 
  uses('lang.ClassLoader');
  
  /** 
   * Loads an "external" class not belonging to the XP framework
   * 
   * Usage:
   * <code>
   *   $e= &new ExternalClassLoader('/path/to/my/classes');
   *   try(); {
   *     $name= $e->loadClass($argv[1]);
   *   } if (catch('ClassNotFoundException', $e)) {
   *     die($e->printStackTrace());
   *   }
   *
   *   $obj= &new $name();
   * </code>
   *
   * @access    public, static
   */
  class ExternalClassLoader extends ClassLoader {
    var
      $codebase= '',
      $format=   '';
    
    /**
     * Constructor
     * 
     * @access  public
     * @param   string codebase the codebase (path)
     * @param   string format default '%s.class.php' (format for filename from classname)
     */
    function __construct($codebase, $format= '%s.class.php') {
      $this->codebase= $codebase;
      $this->format= $format;
      parent::__construct();
    }
    
    /**
     * Load
     *
     * @access  public, static
     * @param   string className fully qualified class name io.File
     * @param   string codebase the codebase (path)
     * @param   string format default '%s.class.php' (format for filename from classname)
     * @return  string class' name for instantiation
     * @throws  ClassNotFoundException in case the class can not be found
     */
    function loadClass($className, $codebase= '', $format= '%s.class.php') {
      if (isset($this)) {
        $p= ini_get('include_path');
        $codebase= $this->codebase;
        $format= $this->format;
        ini_set('include_path', $codebase.':'.$p);
      }
      $result= include_once(sprintf($format, $className));
      if (isset($p)) ini_set('include_path', $p);
      
      if (FALSE === $result) return throw(new ClassNotFoundException(sprintf(
        'class "%s" [codebase %s, format %s] not found',
        $className,
        $codebase,
        $format
      )));
      
      $GLOBALS['php_class_names'][strtolower($className)]= 'php.external.'.ucfirst($className);
      return $className;
    }
  }
?>
