<?php
/* This class is part of the XP framework
 * 
 * $Id$
 */
 
  uses('lang.ClassNotFoundException');
  
  /** 
   * Loads classes
   * 
   * @purpose  Load classes
   * @see      xp://lang.XPClass#forName
   */
  class ClassLoader extends Object {
    protected static
      $delegates  = array();
    
    static function __static() {
      $paths= xp::$registry['loader']->paths;

      xp::$registry['loader']= new self();
      
      // Scan include-path, setting up classloaders for each element
      foreach ($paths as $element) {
        if (is_dir($element)) {
          FileSystemClassLoader::instanceFor($element);
        } else if (is_file($element)) {
          ArchiveClassLoader::instanceFor($element);
        }
      }
    }
    
    /**
     * Retrieve the default class loader
     *
     * @return  lang.ClassLoader
     */
    public static function getDefault() {
      return xp::$registry['loader'];
    }
    
    public static function registerLoader($l) {
      self::$delegates[]= $l;
      return $l;
    }
    
    public function load($class) {
      foreach (self::$delegates as $delegate) {
        if ($delegate->providesClass($class)) return $delegate->load($class);
      }
      throw new ClassNotFoundException('No classloader provides class "'.$class.'" {'.xp::stringOf(self::$delegates).'}');
    }
    
    /**
     * Load the class by the specified name
     *
     * @param   string class fully qualified class name io.File
     * @return  lang.XPClass
     * @throws  lang.ClassNotFoundException in case the class can not be found
     */
    public function loadClass($class) {
      return new XPClass($this->load($class));
    }    
  }
?>
