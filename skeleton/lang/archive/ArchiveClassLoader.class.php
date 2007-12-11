<?php
/* This class is part of the XP framework
 * 
 * $Id$
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
    protected
      $archive  = NULL;
    
    /**
     * Constructor
     * 
     * @param   mixed archive either a string or a lang.archive.Archive instance
     */
    public function __construct($archive) {
      if ($archive instanceof Archive) {
        $this->archive= 'xar://'.$archive->getURI().'?';
      } else {
        $this->archive= 'xar://'.$archive.'?';
      }
    }

    /**
     * Creates a string representation
     *
     * @return  string
     */
    public function toString() {
      return $this->getClassName(). '<'.$this->archive.'>';
    }
    
    /**
     * Load class bytes
     *
     * @param   string name fully qualified class name
     * @return  string
     */
    public function loadClassBytes($name) {
      return file_get_contents($this->archive.strtr($name, '.', '/').xp::CLASS_FILE_EXT);
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
      if (isset(xp::$registry['classloader.'.$class])) {
        return substr(array_search($class, xp::$registry), 6);
      }

      xp::$registry['classloader.'.$class]= 'lang.archive.ArchiveClassLoader://'.substr($this->archive, 6, -1);
      $package= NULL;
      if (FALSE === include($this->archive.strtr($class, '.', '/').xp::CLASS_FILE_EXT)) {
        unset(xp::$registry['classloader.'.$class]);
        throw new FormatException('Cannot define class "'.$class.'"');
      }

      $name= ($package ? strtr($package, '.', '·').'·' : '').xp::reflect($class);
      xp::$registry['class.'.$name]= $class;
      is_callable(array($name, '__static')) && call_user_func(array($name, '__static'));

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
      if (FALSE !== ($r= file_get_contents($this->archive.$string))) {
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
      if (!file_exists($this->archive.$string)) {
        return raise('lang.ElementNotFoundException', 'Could not load resource '.$string);
      }
      return new File($this->archive.$string);
    }
    
    /**
     * Checks whether this loader can provide the requested class
     *
     * @param   string class
     * @return  bool
     */
    public function providesClass($class) {
      return file_exists($this->archive.strtr($class, '.', '/').xp::CLASS_FILE_EXT);
    }

    /**
     * Checks whether this loader can provide the requested resource
     *
     * @param   string filename
     * @return  bool
     */
    public function providesResource($filename) {
      return file_exists($this->archive.$filename);
    }

    /**
     * Checks whether this loader can provide the requested package
     *
     * @param   string package
     * @return  bool
     */
    public function providesPackage($package) {
      $acquired= xarloader::acquire(substr($this->archive, 6, -1));
      $cmps= strtr($package, '.', '/');
      $cmpl= strlen($cmps);
      
      foreach (array_keys($acquired['index']) as $e) {
        if (strncmp($cmps, $e, $cmpl) === 0) return TRUE;
      }
      return FALSE;
    }
    
    /**
     * Fetch instance of classloader by the path to the archive
     *
     * @param   string path
     * @param   bool expand default TRUE whether to expand the path using realpath
     * @return  lang.archive.ArchiveClassLoader
     */
    public static function instanceFor($path, $expand= TRUE) {
      static $pool= array();
      
      if (!isset($pool[$path])) {
        $pool[$path]= new self($expand ? realpath($path) : $path);
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
      $acquired= xarloader::acquire(substr($this->archive, 6, -1));
      $cmps= strtr($package, '.', '/');
      $cmpl= strlen($cmps);
      
      foreach (array_keys($acquired['index']) as $e) {
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
