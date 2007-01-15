<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Inline memory loader to define classes at runtime
   *
   * @see      xp://lang.ClassLoader::defineClass
   * @purpose  Inline loading of classes
   */
  class InlineLoader extends Object {
    protected
      $position = 0,
      $current  = '';
    
    protected static
      $bytes    = array();
    
    public static function __static() {
      stream_wrapper_register('inline', 'InlineLoader');
    }
    
    /**
     * Register new class' bytes
     *
     * @param   string fqcn
     * @param   string bytes
     */
    public static function setClassBytes($fqcn, $bytes) {
      self::$bytes[$fqcn]= '<?php '.$bytes.' ?>';
    }
    
    /**
     * Remove class' bytes after loading
     *
     * @param   string fqcn
     */
    public static function removeClassBytes($fqcn) {
      unset(self::$bytes[$fqcn]);  
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
      list($name)= sscanf($path, 'inline://%s');
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
      list($name)= sscanf($path, 'inline://%s');
      return array(
        'size'  => strlen(self::$bytes[$name])
      );
    }
  }
?>
