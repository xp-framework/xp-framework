<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('lang.IClassLoader');

  /**
   * Dynamic class loader to define classes at runtime
   *
   * @see      xp://lang.ClassLoader::defineClass
   * @purpose  Dynamuc loading of classes
   */
  class DynamicClassLoader extends Object implements IClassLoader {
    protected
      $position = 0,
      $current  = '';

    public
      $context  = '';
    
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
      $this->context= $context;
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
     * Loads a class
     *
     * @param   string class fully qualified class name
     * @return  string class name of class loaded
     * @throws  lang.ClassNotFoundException in case the class can not be found
     * @throws  lang.ClassFormatException in case the class format is invalid
     */
    public function loadClass0($class) {
      if (isset(xp::$registry['classloader.'.$class])) return xp::reflect($class);

      if (!isset(self::$bytes[$class])) {
        throw new ClassNotFoundException($class, array($this));
      }
      
      // Load class
      $package= NULL;
      xp::$registry['classloader.'.$class]= 'lang.DynamicClassLoader://'.$this->context;
      xp::$registry['cl.level']++;
      try {
        $r= include('dyn://'.$class);
      } catch (ClassLoadingException $e) {
        xp::$registry['cl.level']--;

        // Determine PHP's name of the class
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
     * Load the class by the specified name
     *
     * @param   string class fully qualified class name io.File
     * @return  lang.XPClass
     * @throws  lang.ClassNotFoundException in case the class can not be found
     */
    public function loadClass($class) {
      return new XPClass($this->loadClass0($class));
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
      return array(
        'size'  => strlen(self::$bytes[$this->current])
      );
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
      return array(
        'size'  => strlen(self::$bytes[$name])
      );
    }
  }
?>
