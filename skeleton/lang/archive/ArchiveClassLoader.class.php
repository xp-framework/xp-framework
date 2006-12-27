<?php
/* This class is part of the XP framework
 * 
 * $Id$
 */
 
  uses('lang.ClassLoader');
  
  /** 
   * Loads XP classes from a XAR (XP Archive)
   * 
   * Usage:
   * <code>
   *   $l= &new ArchiveClassLoader(new Archive(new File('soap.xar')));
   *   try(); {
   *     $class= &$l->loadClass($argv[1]);
   *   } if (catch('ClassNotFoundException', $e)) {
   *     $e->printStackTrace();
   *     exit(-1);
   *   }
   * 
   *   $obj= &$class->newInstance();
   * </code>
   *
   * @test     xp://net.xp_framework.unittest.io.ArchiveClassLoaderTest
   * @purpose  Load classes from an archive
   * @see      xp://lang.ClassLoader
   * @see      xp://lang.archive.Archive
   * @ext      tokenize
   */
  class ArchiveClassLoader extends ClassLoader {
    public
      $archive  = NULL;
    
    /**
     * Constructor
     * 
     * @param   &lang.archive.Archive archive
     */
    public function __construct($archive) {
      parent::__construct();
      $this->archive= $archive;
      $this->archive->isOpen() || $this->archive->open(ARCHIVE_READ);
    }

    /**
     * Creates a string representation
     *
     * @return  string
     */
    public function toString() {
      return (
        $this->getClassName().
        ($this->classpath ? '<'.rtrim($this->classpath, '.').'>' : '').
        "(search= [\n  ".$this->archive->file->getURI()."\n])"
      );
    }
    
    /**
     * Load class bytes
     *
     * @param   string name fully qualified class name
     * @return  string
     */
    public function loadClassBytes($name) {
      return $this->archive->extract(strtr($name, '.', '/').'.class.php');
    }
    
    /**
     * Load the class by the specified name
     *
     * @param   string class fully qualified class name io.File
     * @return  &lang.XPClass
     * @throws  lang.ClassNotFoundException in case the class can not be found
     */
    public function loadClass($class) {
      $name= xp::reflect($class);

      if (!class_exists($name)) {
        try {
          $src= $this->loadClassBytes($class);
        } catch (Exception $e) {
          throw(new ClassNotFoundException(sprintf(
            'Class "%s" not found: %s',
            $class,
            $e->getMessage()
          )));
        }

        if (FALSE === eval('?>'.$src)) {
          throw(new FormatException('Cannot define class "'.$class.'"'));
        }

        xp::registry('class.'.$name, $class);
        xp::registry('classloader.'.$class, $this);
        is_callable(array($name, '__static')) && call_user_func(array($name, '__static'));
      }

      $c= new XPClass($name);
      return $c;
    }
    
    /**
     * Loads a resource.
     *
     * @param   string string name of resource
     * @return  string
     * @throws  lang.ElementNotFoundException in case the resource cannot be found
     */
    public function getResource($string) {
      if (FALSE !== ($r= $this->archive->extract($string))) {
        return $r;
      }

      return raise('lang.ElementNotFoundException', 'Could not load resource '.$string);
    }
    
    /**
     * Retrieve a stream to the resource
     *
     * @param   string string name of resource
     * @return  &io.Stream
     * @throws  lang.ElementNotFoundException in case the resource cannot be found
     */
    public function getResourceAsStream($string) {
      if (FALSE !== ($s= $this->archive->getStream($string))) {
        return $s;
      }
    
      return raise('lang.ElementNotFoundException', 'Could not load resource '.$string);
    }
    
    /**
     * Checks whether this loader can provide the requested class
     *
     * @param   string class
     * @return  bool
     */
    public function providesClass($class) {
      return $this->archive->contains(strtr($class, '.', '/').'.class.php');
    }
    
    /**
     * Fetch instance of classloader by the path to the archive
     *
     * @param   string path
     * @return  &lang.archive.ArchiveClassLoader
     */
    public static function instanceFor($path) {
      static $pool= array();
      
      if (isset($pool[$path])) {
        return $pool[$path];
      }
      
      $instance= new ArchiveClassLoader(new ArchiveReader($path));
      $pool[$path]= $instance;
      return $instance;
    }
  }
?>
