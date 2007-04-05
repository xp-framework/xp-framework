<?php
/* This class is part of the XP framework
 * 
 * $Id$
 */
 
  uses(
    'lang.IClassLoader',
    'lang.FileSystemClassLoader',
    'lang.archive.ArchiveReader',
    'lang.archive.ArchiveClassLoader'
  );
  
  /** 
   * Loads classes
   * 
   * @purpose  Load classes
   * @see      xp://lang.XPClass#forName
   */
  class ClassLoader extends Object implements IClassLoader {
    protected static
      $delegates  = array();
    
    static function __static() {
      xp::$registry['loader']= new self();
      
      // Scan include-path, setting up classloaders for each element
      foreach (xp::$registry['classpath'] as $element) {
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
    
    /**
     * (Insert method's description here)
     *
     * @param   XXX l
     * @return  XXX
     */
    public static function registerLoader($l) {
      self::$delegates[]= $l;
      return $l;
    }
    
    /**
     * Loads a class
     *
     * @param   string class fully qualified class name
     * @return  string class name of class loaded
     */
    public function load($class) {
      $name= xp::reflect($class);
      if (class_exists($name) || interface_exists($name)) return $name;
      
      // Ask delegates
      foreach (self::$delegates as $delegate) {
        if ($delegate->providesClass($class)) return $delegate->load($class);
      }
      throw new ClassNotFoundException(sprintf(
        'No classloader provides class "%s" {%s}',
        $class,
        xp::stringOf(self::$delegates)
      ));
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
