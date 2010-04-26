<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('lang.AbstractClassLoader');

  /**
   * Dynamic class loader to define classes at runtime
   *
   * @see   xp://lang.ClassLoader::defineClass
   * @test  xp://net.xp_framework.unittest.reflection.RuntimeClassDefinitionTest
   */
  class DynamicClassLoader extends AbstractClassLoader {
    protected
      $position = 0,
      $current  = '',
      $context  = NULL;   // Used by PHP internally for stream support

    protected static
      $bytes    = array();
    
    static function __static() {
      stream_wrapper_register('dyn', __CLASS__);
    }

    /**
     * Constructor. 
     *
     * @param   string context
     */
    public function __construct($context= NULL) {
      $this->path= $this->context= $context;
    }
    
    /**
     * Register new class' bytes
     *
     * @param   string fqcn
     * @param   string bytes
     */
    public function setClassBytes($fqcn, $bytes) {
      self::$bytes[$fqcn]= '<?php '.$bytes.' ?>';
    }

    /**
     * Checks whether this loader can provide the requested class
     *
     * @param   string class
     * @return  bool
     */
    public function providesClass($class) {
      return isset(self::$bytes[$class]);
    }

    /**
     * Checks whether this loader can provide the requested resource
     *
     * @param   string filename
     * @return  bool
     */
    public function providesResource($filename) {
      return FALSE;
    }

    /**
     * Checks whether this loader can provide the requested package
     *
     * @param   string package
     * @return  bool
     */
    public function providesPackage($package) {
      return FALSE;
    }

    /**
     * Load class bytes
     *
     * @param   string name fully qualified class name
     * @return  string
     */
    public function loadClassBytes($name) {
      return self::$bytes[$name];
    }
    
    /**
     * Returns URI suitable for include() given a class name
     *
     * @param   string class
     * @return  string
     */
    protected function classUri($class) {
      return 'dyn://'.$class;
    }
    
    /**
     * Fetch instance of classloader by path
     *
     * @param   string path the identifier
     * @return  lang.IClassLoader
     */
    public static function instanceFor($path) {
      static $pool= array();
      
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
      return array();
    }

    /**
     * Loads a resource.
     *
     * @param   string filename name of resource
     * @return  string
     * @throws  lang.ElementNotFoundException in case the resource cannot be found
     */
    public function getResource($filename) {
      raise('lang.ElementNotFoundException', 'Could not load resource '.$filename);
    }
    
    /**
     * Retrieve a stream to the resource
     *
     * @param   string filename name of resource
     * @return  io.File
     * @throws  lang.ElementNotFoundException in case the resource cannot be found
     */
    public function getResourceAsStream($filename) {
      raise('lang.ElementNotFoundException', 'Could not load resource '.$filename);
    }
    
    /**
     * Stream wrapper method stream_open
     *
     * @param   string path
     * @param   int mode
     * @param   int options
     * @param   string opened_path
     * @return  bool
     */
    public function stream_open($path, $mode, $options, $opened_path) {
      sscanf($path, 'dyn://%[^$]', $this->current);
      if (!isset(self::$bytes[$this->current])) {
        raise('lang.ElementNotFoundException', 'Could not load '.$this->current);
      }
      return TRUE;
    }
    
    /**
     * Stream wrapper method stream_read
     *
     * @param   int count
     * @return  string
     */
    public function stream_read($count) {
      $bytes= substr(self::$bytes[$this->current], $this->position, $count);
      $this->position+= strlen($bytes);
      return $bytes;
    }
    
    /**
     * Stream wrapper method stream_eof
     *
     * @return  bool
     */
    public function stream_eof() {
      // Leave function body empty to optimize speed
      // See http://bugs.php.net/40047
      // 
      // return $this->position >= strlen(self::$bytes[$this->current]);
    }
    
    /**
     * Stream wrapper method stream_stat
     *
     * @return  array<string, string>
     */
    public function stream_stat() {
      return array('size' => strlen(self::$bytes[$this->current]));
    }

    /**
     * Stream wrapper method stream_seek
     *
     * @param   int offset
     * @param   int whence
     * @return  bool
     */
    public function stream_seek($offset, $whence) {
      switch ($whence) {
        case SEEK_SET: $this->position= $offset; break;
        case SEEK_CUR: $this->position+= $offset; break;
        case SEEK_END: $this->position= strlen(self::$bytes[$this->current]); break;
      }
      return TRUE;
    }

    /**
     * Stream wrapper method stream_tell
     *
     * @return  int offset
     */
    public function stream_tell() {
      return $this->position;
    }
    
    /**
     * Stream wrapper method stream_flush
     *
     * @return  bool
     */
    public function stream_flush() {
      return TRUE;
    }

    /**
     * Stream wrapper method stream_close
     *
     * @return  bool
     */
    public function stream_close() {
      return TRUE;
    }
    
    /**
     * Stream wrapper method url_stat
     *
     * @param   string path
     * @return  array<string, string>
     */
    public function url_stat($path) {
      list($name)= sscanf($path, 'dyn://%s');
      return array('size'  => strlen(self::$bytes[$name]));
    }
  }
?>
