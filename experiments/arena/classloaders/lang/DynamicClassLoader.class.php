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
     */
    public function loadClass0($class) {
      if (isset(xp::$registry['classloader.'.$class])) {
        return substr(array_search($class, xp::$registry), 6);
      }

      xp::$registry['classloader.'.$class]= __CLASS__.'://'.$this->context;
      if (!isset(self::$bytes[$class])) {
        unset(xp::$registry['classloader.'.$class]);
        throw new ClassNotFoundException('Unknown class "'.$class.'"');
      }
      $package= NULL;
      if (FALSE === include('dyn://'.$class)) {
        throw new FormatException('Cannot define class "'.$class.'"');
      }

      $name= ($package ? strtr($package, '.', '·').'·' : '').xp::reflect($class);
      xp::$registry['class.'.$name]= $class;
      is_callable(array($name, '__static')) && call_user_func(array($name, '__static'));
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
      return raise('lang.ElementNotFoundException', 'Could not load resource '.$filename);
    }
    
    /**
     * Retrieve a stream to the resource
     *
     * @param   string filename name of resource
     * @return  io.File
     * @throws  lang.ElementNotFoundException in case the resource cannot be found
     */
    public function getResourceAsStream($filename) {
      return raise('lang.ElementNotFoundException', 'Could not load resource '.$filename);
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
      list($name)= sscanf($path, 'dyn://%s');
      $this->current= $name;
      return isset(self::$bytes[$this->current]);
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
     * @return  <string,string>
     */
    public function stream_stat() {
      return array(
        'size'  => strlen(self::$bytes[$this->current])
      );
    }
    
    /**
     * Stream wrapper method url_stat
     *
     * @param   string path
     * @return  <string,string>
     */
    public function url_stat($path) {
      list($name)= sscanf($path, 'dyn://%s');
      return array(
        'size'  => strlen(self::$bytes[$name])
      );
    }
  }
?>
