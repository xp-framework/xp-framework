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
      $codebase= '';
    
    /**
     * Constructor
     * 
     * @access  public
     * @param   string codebase
     */
    function __construct($codebase) {
      $this->codebase= $codebase;
      parent::__construct();
    }
    
    /**
     * Load
     *
     * @access  static
     * @param   string className fully qualified class name io.File
     * @return  string class' name for instantiation
     * @throws  ClassNotFoundException in case the class can not be found
     */
    function loadClass($className) {
      $p= ini_get('include_path');
      ini_set('include_path', $this->codebase.':'.$p);
      $result= parent::loadClass($className);
      ini_set('include_path', $p);
      
      return $result;
    }
  }
?>
