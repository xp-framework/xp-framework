<?php
/* This class is part of the XP framework
 * 
 * $Id$
 */

  uses('lang.AbstractClassLoader');

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
   * @test  xp://net.xp_framework.unittest.core.ArchiveClassLoaderTest
   * @test  xp://net.xp_framework.unittest.reflection.ClassFromArchiveTest
   * @see   xp://lang.ClassLoader
   * @see   xp://lang.archive.Archive
   */
  class ArchiveClassLoader extends AbstractClassLoader {
    protected $archive= NULL;
    
    /**
     * Constructor
     * 
     * @param   var archive either a string or a lang.archive.Archive instance
     */
    public function __construct($archive) {
      $this->path= $archive instanceof Archive ? $archive->getURI() : $archive;

      // Archive within an archive
      if (0 === strncmp('xar://', $this->path, 6)) {
        $this->path= urlencode($this->path);
      }
      $this->archive= 'xar://'.$this->path.'?';
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
     * Returns URI suitable for include() given a class name
     *
     * @param   string class
     * @return  string
     */
    protected function classUri($class) {
      return $this->archive.strtr($class, '.', '/').xp::CLASS_FILE_EXT;
    }

    /**
     * Return a class at the given URI
     *
     * @param   string uri
     * @return  string fully qualified class name, or NULL
     */
    protected function classAtUri($uri) {
      if (0 !== substr_compare($uri, xp::CLASS_FILE_EXT, -strlen(xp::CLASS_FILE_EXT))) return NULL;

      // Absolute URIs have the form "xar://containing.xar?the/classes/Name.class.php"
      if ((DIRECTORY_SEPARATOR === $uri{0} || (':' === $uri{1} && '\\' === $uri{2}))) {
        return NULL;
      } else if (FALSE !== ($p= strpos($uri, '?'))) {
        $archive= substr($uri, 0, $p + 1);
        if ($archive !== $this->archive) return NULL;
        $uri= substr($uri, $p + 1);
      } else {
        $archive= $this->archive;
      }

      // Normalize path: Force forward slashes, strip out "." and empty elements,
      // interpret ".." by backing up until last forward slash is found.
      $path= '';
      foreach (explode('/', strtr($uri, DIRECTORY_SEPARATOR, '/')) as $element) {
        if ('' === $element || '.' === $element) {
          // NOOP
        } else if ('..' === $element) {
          $path= substr($path, 0, strrpos($path, '/'));
        } else {
          $path.= '/'.$element;
        }
      }

      return is_file($archive.substr($path, 1))
        ? strtr(substr($path, 1, -strlen(xp::CLASS_FILE_EXT)), '/', '.')
        : NULL
      ;
    }

    /**
     * Loads a resource.
     *
     * @param   string string name of resource
     * @return  string
     * @throws  lang.ElementNotFoundException in case the resource cannot be found
     */
    public function getResource($string) {
      if (FALSE !== ($r= file_get_contents($this->archive.$string))) return $r;
      xp::gc(__FILE__);
      raise('lang.ElementNotFoundException', 'Could not load resource '.$string);
    }
    
    /**
     * Retrieve a stream to the resource
     *
     * @param   string string name of resource
     * @return  io.Stream
     * @throws  lang.ElementNotFoundException in case the resource cannot be found
     */
    public function getResourceAsStream($string) {
      if (!file_exists($fn= $this->archive.$string)) {
        return raise('lang.ElementNotFoundException', 'Could not load resource '.$string);
      }
      return new File($fn);
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
      
      $path= $expand && 0 !== strncmp('xar://', urldecode($path), 6) ? realpath($path) : $path;
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
