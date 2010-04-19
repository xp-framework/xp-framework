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
   * @test     xp://net.xp_framework.unittest.core.ArchiveClassLoaderTest
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
     * @param   var archive either a string or a lang.archive.Archive instance
     */
    public function __construct($archive) {
      $uri= $archive instanceof Archive ? $archive->getURI() : $archive;

      // Archive within an archive
      if (0 === strncmp('xar://', $uri, 6)) {
        $this->archive= 'xar://'.urlencode($uri).'?';
      } else {
        $this->archive= 'xar://'.$uri.'?';
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
     * @throws  lang.ClassFormatException in case the class format is invalid
     */
    public function loadClass0($class) {
      if (isset(xp::$registry['classloader.'.$class])) return xp::reflect($class);

      // Load class
      $package= NULL;
      xp::$registry['classloader.'.$class]= 'lang.archive.ArchiveClassLoader://'.substr($this->archive, 6, -1);
      xp::$registry['cl.level']++;
      try {
        $r= include($this->archive.strtr($class, '.', '/').xp::CLASS_FILE_EXT);
      } catch (ClassLoadingException $e) {
        xp::$registry['cl.level']--;

        $decl= (NULL === $package
          ? substr($class, (FALSE === ($p= strrpos($class, '.')) ? 0 : $p + 1))
          : strtr($class, '.', '·')
        );

        // If class was declared, but loading threw an exception it means
        // a "soft" dependency, one that is only required at runtime, was
        // not loaded, the class itself has been declared.
        if (class_exists($decl, FALSE) || interface_exists($decl, FALSE)) {
          raise('lang.ClassDependencyException', $class, array($this), $e);
        }

        // If otherwise, a "hard" dependency could not be loaded, eg. the
        // base class or a required interface and thus the class could not
        // be declared.
        raise('lang.ClassLinkageException', $class, array($this), $e);
      }

      xp::$registry['cl.level']--;
      if (FALSE === $r) {
        unset(xp::$registry['classloader.'.$class]);
        throw new ClassNotFoundException($class, array($this));
      }

      // Register it
      $name= ($package ? strtr($package, '.', '·').'·' : '').substr($class, (FALSE === ($p= strrpos($class, '.')) ? 0 : $p + 1));
      if (!class_exists($name, FALSE) && !interface_exists($name, FALSE)) {
        unset(xp::$registry['classloader.'.$class]);
        raise('lang.ClassFormatException', 'Class "'.$name.'" not declared in loaded file');
      }
      xp::$registry['class.'.$name]= $class;
      method_exists($name, '__static') && xp::$registry['cl.inv'][]= array($name, '__static');
      if (0 == xp::$registry['cl.level']) {
        $invocations= xp::$registry['cl.inv'];
        xp::$registry['cl.inv']= array();
        foreach ($invocations as $inv) call_user_func($inv);
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
      $acquired= xarloader::acquire(urldecode(substr($this->archive, 6, -1)));
      $cmps= strtr($package, '.', '/').'/';
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
      
      $path= $expand && 0 !== strncmp('xar%3A%2F%2F', $path, 12) ? realpath($path) : $path;
      if (!isset($pool[$path])) {
        $pool[$path]= new self($path);
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
      $acquired= xarloader::acquire(urldecode(substr($this->archive, 6, -1)));
      $cmps= strtr($package, '.', '/');
      $cmpl= strlen($cmps);
      
      foreach (array_keys($acquired['index']) as $e) {
        if (strncmp($cmps, $e, $cmpl) != 0) continue;
        $entry= 0 != $cmpl ? substr($e, $cmpl+ 1) : $e;
        
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
          $entry.= '/';
        }
        $contents[$entry]= NULL;
      }
      return array_keys($contents);
    }
  }
?>
