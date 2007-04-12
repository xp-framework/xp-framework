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
     * @param   IClassLoader l
     * @return  IClassLoader
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
      return new XPClass($this->load($class));
    }    

    /**
     * Define a class with a given name
     *
     * @param   string class fully qualified class name
     * @param   string bytes sourcecode of the class
     * @return  lang.XPClass
     * @throws  lang.FormatException in case the class cannot be defined
     */
    protected function _defineClassFromBytes($class, $bytes) {
      $name= xp::reflect($class);

      if (!class_exists($name) && !interface_exists($name)) {
        
        // Load InlineLoader
        XPClass::forName('lang.InlineLoader');
        InlineLoader::setClassBytes($class, $bytes);
        if (FALSE === include('inline://'.$class)) {
          throw new FormatException('Cannot define class "'.$class.'"');
        }
        InlineLoader::removeClassBytes($class);
        
        if (!class_exists($name) && !interface_exists($name)) {
          throw new FormatException('Class "'.$class.'" not defined');
        }
        xp::$registry['class.'.$name]= $class;
        xp::$registry['classloader.'.$class]= __CLASS__.'://'.$this->path;
        is_callable(array($name, '__static')) && call_user_func(array($name, '__static'));
      }      

      return new XPClass($name);
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
      
      // If invoked with less than four arguments, old behaviour will be executed
      if (NULL === $bytes) {
        return $this->_defineClassFromBytes($this->path.$class, $parent);
      }
      
      $name= xp::reflect($class);
      if (!class_exists($name)) {
        $qname= $this->path.$class;
        $parentName= xp::reflect($parent);
        
        if (!class_exists($parentName)) {
          throw(new ClassNotFoundException('Parent class '.$parent.' does not exist.'));
        }
        
        $newBytes= 'class '.$name.' extends '.$parentName;
        if (sizeof($interfaces)) {
          $newBytes.= ' implements ';

          $ifaces= array();
          foreach ($interfaces as $i) { $ifaces[]= xp::reflect($i); }
          
          $newBytes.= implode(', ', $ifaces);
        }
        
        $newBytes.= ' '.$bytes;
        
        return $this->_defineClassFromBytes($qname, $newBytes);
      }
      
      return new XPClass($name);
    }
  }
?>
