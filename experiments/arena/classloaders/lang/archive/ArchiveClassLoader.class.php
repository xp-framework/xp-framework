<?php
/* This class is part of the XP framework
 * 
 * $Id: ArchiveClassLoader.class.php 9834 2007-04-01 16:28:26Z kiesel $
 */
 
  /** 
   * Loads XP classes from a XAR (XP Archive)
   * 
   * Usage:
   * <code>
   *   $l= new ArchiveClassLoader(new Archive(new File('soap.xar')));
   *   try {
   *     $class= $l->loadClass($argv[1]);
   *   } catch (ClassNotFoundException $e) {
   *     $e->printStackTrace();
   *     exit(-1);
   *   }
   * 
   *   $obj= $class->newInstance();
   * </code>
   *
   * @test     xp://net.xp_framework.unittest.io.ArchiveClassLoaderTest
   * @purpose  Load classes from an archive
   * @see      xp://lang.ClassLoader
   * @see      xp://lang.archive.Archive
   * @ext      tokenize
   */
  class ArchiveClassLoader extends Object {
    public
      $archive  = NULL;
    
    /**
     * Constructor
     * 
     * @param   lang.archive.Archive archive
     */
    public function __construct($archive) {
      $this->archive= $archive;
      $this->archive->isOpen() || $this->archive->open(ARCHIVE_READ);
      
      // Add this XAR to the include-path (if not already in there), 
      // so further lookups will succeed.
      ini_set('include_path', implode(PATH_SEPARATOR, array_unique(array_merge(
        explode(PATH_SEPARATOR, ini_get('include_path')), 
        array($this->archive->getURI())
      ))));
    }

    /**
     * Creates a string representation
     *
     * @return  string
     */
    public function toString() {
      return $this->getClassName(). '<'.$this->archive->getURI().'>';
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
     * @return  lang.XPClass
     * @throws  lang.ClassNotFoundException in case the class can not be found
     * @throws  lang.FormatException in case the class file is malformed
     */
    public function loadClass($class) {
      return new XPClass($this->load($class));
    }

    public function load($class) {
      $name= xp::reflect($class);

      if (!class_exists($name) && !interface_exists($name)) {
        if (FALSE === $this->providesClass($class)) {
          throw new ClassNotFoundException(sprintf(
            'Class "%s" not found',
            $class
          ));
        }

        if (FALSE === include('xar://'.$this->archive->getURI().'?'.strtr($class, '.', '/').'.class.php')) {
          throw new FormatException('Cannot define class "'.$class.'"');
        }

        xp::$registry['class.'.$name]= $class;
        xp::$registry['classloader.'.$class]= __CLASS__.'://'.$this->archive->getURI();
        is_callable(array($name, '__static')) && call_user_func(array($name, '__static'));
      }

      return $name;
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
     * @return  io.Stream
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
     * @return  lang.archive.ArchiveClassLoader
     */
    public static function instanceFor($path) {
      static $pool= array();
      
      if (!isset($pool[$path])) {
        $pool[$path]= ClassLoader::registerLoader(new self(new ArchiveReader(realpath($path))));
      }
      
      return $pool[$path];
    }
  }
?>
