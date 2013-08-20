<?php
/* This class is part of the XP framework
 * 
 * $Id$
 */
 
  uses('lang.AbstractClassLoader');
  
  /** 
   * Loads a class from the filesystem
   * 
   * @test  xp://net.xp_framework.unittest.reflection.ClassLoaderTest
   * @test  xp://net.xp_framework.unittest.reflection.ClassFromFileSystemTest
   * @see   xp://lang.XPClass#forName
   */
  class FileSystemClassLoader extends AbstractClassLoader {
    
    /**
     * Constructor. 
     *
     * The path argument is optional and lets you define where to search for
     * classes (it will be prefixed to the class name)
     *
     * @param   string path default '' file system path
     */
    public function __construct($path= '') {
      $this->path= rtrim($path, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
    }
    
    /**
     * Load class bytes
     *
     * @param   string name fully qualified class name
     * @return  string
     */
    public function loadClassBytes($name) {
      return file_get_contents($this->path.strtr($name, '.', DIRECTORY_SEPARATOR).xp::CLASS_FILE_EXT);
    }
    
    /**
     * Checks whether this loader can provide the requested class
     *
     * @param   string class
     * @return  bool
     */
    public function providesClass($class) {
      $f= $this->path.strtr($class, '.', DIRECTORY_SEPARATOR).xp::CLASS_FILE_EXT;
      return $f === realpath($f);
    }
    
    /**
     * Checks whether this loader can provide the requested resource
     *
     * @param   string filename
     * @return  bool
     */
    public function providesResource($filename) {
      return is_file($this->path.$filename);
    }

    /**
     * Checks whether this loader can provide the requested package
     *
     * @param   string package
     * @return  bool
     */
    public function providesPackage($package) {
      return is_dir($this->path.strtr($package, '.', DIRECTORY_SEPARATOR));
    }
    
    /**
     * Returns URI suitable for include() given a class name
     *
     * @param   string class
     * @return  string
     */
    protected function classUri($class) {
      return $this->path.strtr($class, '.', DIRECTORY_SEPARATOR).xp::CLASS_FILE_EXT;
    }

    /**
     * Return a class at the given URI
     *
     * @param   string uri
     * @return  string fully qualified class name, or NULL
     */
    protected function classAtUri($uri) {
      if (0 !== substr_compare($uri, xp::CLASS_FILE_EXT, -strlen(xp::CLASS_FILE_EXT))) return NULL;

      // Resolve path if not absolute
      if ((DIRECTORY_SEPARATOR === $uri{0} || (':' === $uri{1} && '\\' === $uri{2}))) {
        $absolute= realpath($uri);
      } else {
        $absolute= realpath($this->path.DIRECTORY_SEPARATOR.$uri);
      }

      // Verify path is inside this path, exists and is a file
      $l= strlen($this->path);
      if (FALSE === $absolute || 0 !== strncmp($absolute, $this->path, $l) || !is_file($absolute)) return NULL;

      return strtr(
        substr($absolute, $l, -strlen(xp::CLASS_FILE_EXT)),
        '/'.DIRECTORY_SEPARATOR,
        '..'
      );
    }

    /**
     * Loads a resource.
     *
     * @param   string filename name of resource
     * @return  string
     * @throws  lang.ElementNotFoundException in case the resource cannot be found
     */
    public function getResource($filename) {
      if (!is_file($fn= $this->path.strtr($filename, '/', DIRECTORY_SEPARATOR))) {
        return raise('lang.ElementNotFoundException', 'Could not load resource '.$filename);
      }
      return file_get_contents($fn);
    }
    
    /**
     * Retrieve a stream to the resource
     *
     * @param   string filename name of resource
     * @return  io.File
     * @throws  lang.ElementNotFoundException in case the resource cannot be found
     */
    public function getResourceAsStream($filename) {
      if (!is_file($fn= $this->path.strtr($filename, '/', DIRECTORY_SEPARATOR))) {
        return raise('lang.ElementNotFoundException', 'Could not load resource '.$filename);
      }
      return new File($fn);
    }

    /**
     * Fetch instance of classloader by the path to the archive
     *
     * @param   string path
     * @param   bool expand default TRUE whether to expand the path using realpath
     * @return  lang.FileSystemClassLoader
     */
    public static function instanceFor($path, $expand= TRUE) {
      static $pool= array();
      
      $path= $expand ? realpath($path) : $path;
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
      if ($d= @dir($this->path.strtr($package, '.', DIRECTORY_SEPARATOR))) {
        while ($e= $d->read()) {
          if ('.' != $e{0}) $contents[]= $e.(is_dir($d->path.DIRECTORY_SEPARATOR.$e) ? '/' : '');
        }
        $d->close();
      }
      return $contents;
    }
  }
?>
