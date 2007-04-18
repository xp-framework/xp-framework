<?php
/* This class is part of the XP framework
 * 
 * $Id$
 */
 
  uses(
    'lang.IClassLoader',
    'lang.FileSystemClassLoader',
    'lang.DynamicClassLoader',
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
     * Register a class loader as a delegate
     *
     * @param   IClassLoader l
     * @return  IClassLoader the registered loader
     */
    public static function registerLoader(IClassLoader $l) {
      self::$delegates[]= $l;
      return $l;
    }
    
    /**
     * Loads a class
     *
     * @param   string class fully qualified class name
     * @return  string class name of class loaded
     * @throws  lang.ClassNotFoundException in case the class can not be found
     */
    public function loadClass0($class) {
      $name= xp::reflect($class);
      if (class_exists($name) || interface_exists($name)) return $name;
      
      // Ask delegates
      foreach (self::$delegates as $delegate) {
        if ($delegate->providesClass($class)) return $delegate->loadClass0($class);
      }
      throw new ClassNotFoundException(sprintf(
        'No classloader provides class "%s" {%s}',
        $class,
        xp::stringOf(self::$delegates)
      ));
    }

    /**
     * Find the class by the specified name
     *
     * @param   string class fully qualified class name
     * @return  lang.IClassLoader the classloader that provides this class
     */
    public function findClass($class) {
      foreach (self::$delegates as $delegate) {
        if ($delegate->providesClass($class)) return $delegate;
      }
      return xp::null();
    }    
    
    /**
     * Load the class by the specified name
     *
     * @param   string class fully qualified class name
     * @return  lang.XPClass
     * @throws  lang.ClassNotFoundException in case the class can not be found
     */
    public function loadClass($class) {
      return new XPClass($this->loadClass0($class));
    }    

    /**
     * Define a class with a given name
     *
     * @param   string class fully qualified class name
     * @param   string parent either sourcecode of the class or FQCN of parent
     * @param   string[] interfaces default NULL FQCNs of implemented interfaces
     * @param   string bytes default NULL inner sourcecode of class (containing {}) 
     * @return  lang.XPClass
     * @throws  lang.FormatException in case the class cannot be defined
     * @throws  lang.ClassNotFoundException if given parent class does not exist
     */
    public function defineClass($class, $parent, $interfaces= NULL, $bytes= NULL) {
      $name= xp::reflect($class);
      if (!class_exists($name) && !interface_exists($name)) {
        $super= xp::reflect($parent);

        // Test for existance        
        if (!class_exists($super)) {
          throw new ClassNotFoundException('Parent class "'.$parent.'" does not exist.');
        }
        
        $if= array_map(array('xp', 'reflect'), $interfaces);
        foreach ($if as $implemented) {
          if (interface_exists($implemented)) continue;
          throw new ClassNotFoundException('Implemented interface "'.$implemented.'" does not exist.');
        }

        with ($dyn= DynamicClassLoader::instanceFor($class)); {
          $dyn->setClassBytes($class, sprintf(
            'class %s extends %s%s %s',
            $name,
            $super,
            $interfaces ? ' implements '.implode(', ', $if) : '',
            $bytes
          ));
          
          try {
            $name= $dyn->loadClass0($class);
          } catch (Throwable $e) {
            // Fall through so class bytes get removed in any case
          } finally(); {
            $dyn->removeClassBytes($class);
            if ($e) throw $e;
          }
        }
      }
      
      return new XPClass($name);
    }
  }
?>
