<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Provides a resource through a scheme.
   *
   * @see       lang.ClassLoader
   * @test      xp://net.xp_framework.unittest.core.ResourceProviderTest
   * @purpose   Provide schemes that resolve resources
   */
  class ResourceProvider extends Object {
    protected static
      $instance   = NULL;

    protected
      $resource   = NULL;

    public
      $context    = NULL;

    static function __static() {
      stream_wrapper_register('res', __CLASS__);
      self::$instance= new self();
    }

    /**
     * Retrieve instance
     *
     */
    public static function getInstance() {
      return self::$instance;
    }

    /**
     * Opens new stream
     *
     * @param   string path
     * @param   string mode
     * @param   int options
     * @param   &string opened_path
     * @return  bool
     */
    public function stream_open($path, $mode, $options, &$opened_path) {
      if ($mode !== 'r' && $mode !== 'rb') return FALSE;

      $this->resource= $this->getLoader()->getResourceAsStream(self::$instance->translatePath($path));
      $this->resource->open(FILE_MODE_READ);

      return TRUE;
    }
    
    /**
     * Retrieve associated loader
     *
     * @return  lang.IClassLoader
     */
    protected function getLoader() {
      return ClassLoader::getDefault();
    }

    /**
     * Translate module name into path
     *
     * @param   string path
     * @return  string
     */
    public function translatePath($path) {

      // Shortcut
      if (1 === sscanf($path, 'res://%s', $file)) return $file;
      throw new IllegalArgumentException('Invalid resource expression: "'.$path.'"');
    }
    
    /**
     * Close stream
     *
     */
    public function stream_close() {
      $this->resource->close();
      $this->resource= NULL;
    }
    
    /**
     * Read from stream
     *
     * @param   int count
     * @return  string
     */
    public function stream_read($count) {
      return $this->resource->read($count);
    }
    
    /**
     * Write to stream. Unsupported
     *
     * @param   string data
     * @return  int
     */
    public function stream_write($data) {
      raise('lang.MethodNotImplementedException', 'Not writeable.', __METHOD__);
    }
    
    /**
     * Checks for end-of-file
     *
     * @return  bool
     */
    public function stream_eof() {
      return $this->resource->eof();
    }
    
    /**
     * Retrieve current file pointer position
     *
     * @return  int
     */
    public function stream_tell() {
      return $this->resource->tell();
    }
    
    /**
     * Seek to given offset
     *
     * @param   int offset
     * @param   int whence
     */
    public function stream_seek($offset, $whence) {
      $this->resource->seek($offset);
    }
    
    /**
     * Flush stream
     *
     */
    public function stream_flush() {
      // NOOP
    }

    /**
     * Callback for fstat() requests
     *
     * @return  [:int]
     */
    public function stream_stat($path) {
      return array(
        'dev'   => 0,
        'ino'   => 0,
        'mode'  => 0444,
        'nlink' => 0,
        'uid'   => 1,
        'gid'   => 1,
        'rdev'  => 0,
        'size'  => $this->resource->size(),
        'atime' => 0,
        'mtime' => 0,
        'ctime' => 0,
      );
    }
    
    /**
     * Callback for stat() requests
     *
     * @param   string path
     * @param   int flags
     * @return  <string,int>[]
     */
    public function url_stat($path, $flags) {
      if (!self::$instance->getLoader()->providesResource(self::$instance->translatePath($path))) {
        return FALSE;
      }

      $hdl= self::$instance->getLoader()->getResourceAsStream(self::$instance->translatePath($path));
      return array(
        'dev'   => 0,
        'ino'   => 0,
        'mode'  => 0444,
        'nlink' => 0,
        'uid'   => 1,
        'gid'   => 1,
        'rdev'  => 0,
        'size'  => $hdl->size(),
        'atime' => 0,
        'mtime' => 0,
        'ctime' => 0,
      );
    }                            
  }
?>
