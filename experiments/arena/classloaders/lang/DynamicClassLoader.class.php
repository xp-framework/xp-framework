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
      $current  = '',
      $class    = '';
    
    protected static
      $instance = NULL,
      $bytes    = array();
    
    static function __static() {
      stream_wrapper_register('dyn', __CLASS__);
      self::$instance= new self();
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
     * Load class bytes
     *
     * @param   string name fully qualified class name
     * @return  string
     */
    public function loadClassBytes($name) {
      return self::$bytes[$fqcn];
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

      if (!class_exists($name) && !interface_exists($name)) {
        if (!isset(self::$bytes[$class])) {
          throw new ClassNotFoundException('Unknown class "'.$class.'"');
        }

        if (FALSE === include('dyn://'.$class)) {
          throw new FormatException('Cannot define class "'.$class.'"');
        }

        xp::$registry['class.'.$name]= $class;
        xp::$registry['classloader.'.$class]= __CLASS__.'://dyn';
        is_callable(array($name, '__static')) && call_user_func(array($name, '__static'));
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
     * @param   string path
     * @return  lang.IClassLoader
     */
    public static function instanceFor($path) {
      return self::$instance;
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
