<?php
/* This class is part of the XP framework
 * 
 * $Id$
 */
 
uses('lang.ClassNotFoundException');

namespace lang {
  
  /** 
   * Loads a class.
   * 
   * Usage:
   * <code>
   *   $names= array();
   *
   *   try {
   *     $names[]= lang::ClassLoader::getInstance()->loadClass('lang.Object');
   *     $names[]= lang::ClassLoader::getInstance('info.binford6100')->loadClass('Power');
   *   } catch(ClassNotFoundException $e)) {
   *     $e->printStackTrace();
   *     exit(-1);
   *   }
   *
   *   var_dump($names);
   * </code>
   *
   * The return value of the loadClass method is a fully qualified class name
   * in PHPs terms, such as "info:binford6100::Power", which is used by 
   * lang.XPClass#forName to retrieve a class object.
   *
   * @see      xp://lang.XPClass#forName
   * @purpose  Load classes
   */
  class ClassLoader extends lang::Object {
    private
      $classpath    = '';
      
    private static
      $instance     = array();
    
    /**
     * Constructor. 
     * The path argument is optional and lets you define where to search for
     * classes (it will be prefixed to the class name)
     *
     * @access  private
     * @param   string path default '' classpath
     * @see     xp://lang.ClassLoader#getInstance
     */
    private function __construct($path= '') {
      if (!empty($path)) $this->classpath= $path.'.';
    }
    
    /**
     * Get an instance
     *
     * @model   static
     * @access  public
     * @param   string path default '' classpath
     * @return  lang.ClassLoader
     */
    public static function getInstance($path= '') {
      if (!isset(self::$instance[$path])) {
        self::$instance[$path]= new lang::ClassLoader($path);
      }
      
      return self::$instance[$path];
    }
    
    /**
     * Load
     *
     * @access  public
     * @param   string className fully qualified class name io.File
     * @return  string class' name for instantiation
     * @throws  lang.ClassNotFoundException in case the class can not be found
     */
    public function loadClass($className) {
      $className= $this->classpath.$className;
      list($namespace, $class)= xp::seperatecn($className);
      if (!include_once(
        str_replace('.', DIRECTORY_SEPARATOR, $namespace).
        DIRECTORY_SEPARATOR.
        $class.'.class.php')
      ) throw new lang::ClassNotFoundException('Class '.$className.' cannot be found');

      $phpName= strtr($namespace, '.', ':').'::'.strtolower($class);
      xp::registry::$names[$phpName]= $className;
      
      if (!class_exists($phpName)) {
        throw new lang::ClassNotFoundException('Class '.$className.' cannot be found');
      }
      
      return $phpName;
    }
  }
}
?>
