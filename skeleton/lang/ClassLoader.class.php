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
     * classes (it will be prefixed to the class name).
     *
     * @access  public
     * @param   string path default '' classpath
     */
    function __construct($path= '') {
      parent::__construct();
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
     * Load the class by the specified name
     *
     * @access  public
     * @param   string class fully qualified class name io.File
     * @return  &lang.XPClass
     * @throws  lang.ClassNotFoundException in case the class can not be found
     */
    function &loadClass($class) {
      $qname= $this->classpath.$class;
      $name= xp::reflect($qname);

      if (class_exists($name) || uses($qname)) {
        return new XPClass($name);
      }
      
      return throw(new ClassNotFoundException('Class "'.$qname.'" not found'));
    }
  }
?>
