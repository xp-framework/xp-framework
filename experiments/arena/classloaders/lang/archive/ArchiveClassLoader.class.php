<?php
/* This class is part of the XP framework
 * 
 * $Id: ArchiveClassLoader.class.php 9834 2007-04-01 16:28:26Z kiesel $
 */

  uses('lang.IClassLoader');

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
  class ArchiveClassLoader extends Object implements IClassLoader {
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
      return new XPClass($this->loadClass0($class));
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

      if (!isset(xp::$registry['classloader.'.$class])) {
        xp::$registry['classloader.'.$class]= __CLASS__.'://'.$this->archive->getURI();
        if (FALSE === include('xar://'.$this->archive->getURI().'?'.strtr($class, '.', '/').'.class.php')) {
          unset(xp::$registry['classloader.'.$class]);
          throw new FormatException('Cannot define class "'.$class.'"');
        }

        xp::$registry['class.'.$name]= $class;
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
     * Checks whether this loader can provide the requested resource
     *
     * @param   string filename
     * @return  bool
     */
    public function providesResource($filename) {
      return $this->archive->contains($filename);
    }

    /**
     * Checks whether this loader can provide the requested package
     *
     * @param   string package
     * @return  bool
     */
    public function providesPackage($package) {
      return $this->archive->contains(strtr($package, '.', '/'));
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
        $pool[$path]= new self(new ArchiveReader(realpath($path)));
      }
      
      return $pool[$path];
    }

    /**
     * Get package contents
     *
     * @param   string package
     * @return  string[] filenames
     */
    public function packageContents($package) {
      $contents= array();
      for (
        $cmps= strtr($package, '.', '/'), 
        $cmpl= strlen($cmps),
        $this->archive->rewind(); 
        $e= $this->archive->getEntry(); 
      ) {
        if (strncmp($cmps, $e, $cmpl) != 0) continue;
        $entry= substr($e, $cmpl+ 1);
        
        // Check to see if we're getting something in a subpackage. Imagine the 
        // following structure:
        //
        // archive.xar
        // - tests/ClassOne.class.php
        // - tests/classes/RecursionTest.class.php
        // - tests/classes/ng/NextGenerationRecursionTest.class.php
        //
        // When this method is invoked with "tests" as name, "ClassOne.class.php"
        // and "classes/" should be returned (but neither any of the subdirectories
        // nor their contents)
        if (FALSE !== ($p= strpos($entry, '/'))) {
          $entry= substr($entry, 0, $p);
          if (strstr($entry, '/')) continue;
        }
        $contents[$entry]= NULL;
      }
      return array_keys($contents);
    }
  }
?>
