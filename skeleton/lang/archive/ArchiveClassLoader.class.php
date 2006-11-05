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
   * @see      xp://lang.cca.Archive
   * @ext      tokenize
   */
  class ArchiveClassLoader extends ClassLoader {
    var
      $archive  = NULL;
    
    /**
     * Constructor
     * 
     * @access  public
     * @param   &lang.cca.Archive archive
     */
    function __construct(&$archive) {
      parent::__construct();
      $this->archive= &$archive;
      $this->archive->isOpen() || $this->archive->open(ARCHIVE_READ);
    }
    
    /**
     * Load class bytes
     *
     * @access  public
     * @param   string name fully qualified class name
     * @return  string
     */
    function loadClassBytes($name) {
      return $this->archive->extract(strtr($name, '.', '/').'.class.php');
    }

    /**
     * Creates a string representation
     *
     * @access  public
     * @return  string
     */
    function toString() {
      return (
        $this->getClassName().
        ($this->classpath ? '<'.rtrim($this->classpath, '.').'>' : '').
        "(search= [\n  ".$this->archive->file->getURI()."\n])"
      );
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
      $name= xp::reflect($class);

      if (!class_exists($name)) {
        try(); {
          $src= $this->loadClassBytes($class);
        } if (catch('Exception', $e)) {
          return throw(new ClassNotFoundException(sprintf(
            'Class "%s" not found: %s',
            $class,
            $e->getMessage()
          )));
        }

        if (FALSE === eval('?>'.$src)) {
          return throw(new FormatException('Cannot define class "'.$class.'"'));
        }

        xp::registry('class.'.$name, $class);
        xp::registry('classloader.'.$class, $this);
        is_callable(array($name, '__static')) && call_user_func(array($name, '__static'));
      }

      $c= &new XPClass($name);
      return $c;
    }
    
    /**
     * Loads a resource.
     *
     * @access  public
     * @param   string string name of resource
     * @return  string
     */
    function getResource($string) {
      return $this->archive->extract($string);
    }
    
    /**
     * Retrieve a stream to the resource
     *
     * @access  public
     * @param   string string name of resource
     * @return  &io.Stream
     */
    function getResourceAsStream($string) {
      return $this->archive->getStream($string);
    }
    
    /**
     * Checks whether this loader can provide the requested class
     *
     * @access  public
     * @param   string fqcn
     * @return  bool
     */
    function providesClass($class) {
      return $this->archive->contains(strtr($class, '.', '/').'.class.php');
    }
    
    /**
     * Fetch instance of classloader by the path to the archive
     *
     * @model   static
     * @access  public
     * @param   string path
     * @return  &lang.archive.ArchiveClassLoader
     */
    function &instanceFor($path) {
      static $pool= array();
      
      if (isset($pool[$path])) {
        return $pool[$path];
      }
      
      $instance= &new ArchiveClassLoader(new ArchiveReader($path));
      $pool[$path]= $instance;
      return $instance;
    }
  }
?>
