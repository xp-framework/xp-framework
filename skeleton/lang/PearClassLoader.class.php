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
  class PearClassLoader extends ClassLoader {
    var
      $codebase= '';
    
    /**
     * Constructor
     * 
     * @access  public
     * @param   string codebase default '' the codebase (path), if empty, the
     *          predefined constant PEAR_INSTALL_DIR is used
     */
    function __construct($codebase= '') {
      $this->codebase= empty($codebase) ? PEAR_INSTALL_DIR : $codebase;
      parent::__construct();
    }
    
    /**
     * Load
     *
     * @access  public, static
     * @param   string className fully qualified class name io.File
     * @param   string codebase the codebase (path)
     * @return  string class' name for instantiation
     * @throws  ClassNotFoundException in case the class can not be found
     */
    function loadClass($className, $codebase= '') {     
      if (isset($this)) {
        $codebase= $this->codebase;
      } else if (empty($codebase)) {
        $codebase= PEAR_INSTALL_DIR;
      }
      
      $p= ini_get('include_path');
      ini_set('include_path', $codebase.':'.$p);
      
      $result= include_once(strtr($className, '_', '/').'.php');
      ini_set('include_path', $p);
      
      if (FALSE === $result) return throw(new ClassNotFoundException(sprintf(
        'class "%s" [codebase %s] not found',
        $className,
        $codebase
      )));
      
      $GLOBALS['php_class_names'][strtolower($className)]= 'php.pear.'.$className;
      return $className;
    }
  }
?>
