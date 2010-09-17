<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('io.IOException', 'io.FileNotFoundException');

  /**
   * Wraps I/O streams into PHP streams
   *
   * @test     xp://net.xp_framework.unittest.io.StreamWrappingTest
   * @see      php://streams
   * @purpose  Utility
   */
  abstract class Streams extends Object {
    protected static 
      $streams = array();
    
    public
      $context = NULL;

    protected 
      $length  = 0,
      $id      = NULL;
      
    static function __static() {
      stream_wrapper_register('iostrr', get_class(newinstance(__CLASS__, array(), '{
        static function __static() { }

        public function stream_open($path, $mode, $options, $opened_path) {
          parent::stream_open($path, $mode, $options, $opened_path);
          $this->length= parent::$streams[$this->id]->available();
          return TRUE;
        }

        public function stream_write($data) {
          throw new IOException("Cannot write to readable stream");
        }

        public function stream_read($count) {
          return parent::$streams[$this->id]->read($count);
        }

        public function stream_flush() {
          return TRUE;
        }

        public function stream_eof() {
          return 0 === parent::$streams[$this->id]->available();
        }
      }')));
      stream_wrapper_register('iostrw', get_class(newinstance(__CLASS__, array(), '{
        static function __static() { }

        public function stream_write($data) {
          parent::$streams[$this->id]->write($data);
          $written= strlen($data);
          $this->length+= $written;
          return $written;
        }

        public function stream_read($count) {
          throw new IOException("Cannot read from writeable stream");
        }

        public function stream_flush() {
          return self::$streams[$this->id]->flush();
        }

        public function stream_eof() {
          return FALSE;
        }
      }')));
    }

    /**
     * Open an input stream for reading
     *
     * @param   io.streams.InputStream s
     * @return  resource
     */
    public static function readableFd(InputStream $s) { 
      self::$streams[$s->hashCode()]= $s;
      return fopen('iostrr://'.$s->hashCode(), 'rb');
    }

    /**
     * Open an input stream for reading and return URI
     *
     * @param   io.streams.InputStream s
     * @return  string
     */
    public static function readableUri(InputStream $s) { 
      self::$streams[$s->hashCode()]= $s;
      return 'iostrr://'.$s->hashCode();
    }

    /**
     * Open an output stream for writing
     *
     * @param   io.streams.OutputStream s
     * @return  resource
     */
    public static function writeableFd(OutputStream $s) { 
      self::$streams[$s->hashCode()]= $s;
      return fopen('iostrw://'.$s->hashCode(), 'wb');
    }

    /**
     * Open an output stream for writing
     *
     * @param   io.streams.OutputStream s
     * @return  resource
     */
    public static function writeableUri(OutputStream $s) { 
      self::$streams[$s->hashCode()]= $s;
      return 'iostrw://'.$s->hashCode();
    }
    
    /**
     * Read an IOElements' contents completely into a buffer in a single call.
     *
     * @param   io.streams.InputStream s
     * @return  string
     * @throws  io.IOException
     */
    public static function readAll(InputStream $s) {
      $r= '';
      while ($s->available() > 0) $r.= $s->read();
      return new Bytes($r);
    }

    /**
     * Callback for fopen
     *
     * @param   string path
     * @param   string mode
     * @param   int options
     * @param   string opened_path
     * @throws  io.FileNotFoundException in case the given file cannot be found
     */
    public function stream_open($path, $mode, $options, $opened_path) {
      sscanf(urldecode($path), "iostr%c://%[^$]", $m, $this->id);
      if (!isset(self::$streams[$this->id])) {
        throw new FileNotFoundException('Cannot open stream "'.$this->id.'" mode '.$mode);
      }
      return TRUE;
    }

    /**
     * Callback for fclose
     *
     * @param   string path
     * @param   string mode
     * @param   int options
     * @param   string opened_path
     * @return  bool
     */
    public function stream_close() {
      if (!isset(self::$streams[$this->id])) return FALSE;

      self::$streams[$this->id]->close();
      unset(self::$streams[$this->id]);
      return TRUE;
    }

    /**
     * Callback for fseek
     *
     * @param   int offset
     * @param   int whence
     * @return  bool
     */
    public function stream_seek($offset, $whence) {
      if (!self::$streams[$this->id] instanceof Seekable) {
        throw new IOException('Underlying stream does not support seeking');
      }

      self::$streams[$this->id]->seek($offset, $whence);
      return TRUE;
    }

    /**
     * Callback for ftell
     *
     * @return  int position
     */
    public function stream_tell() {
      if (!self::$streams[$this->id] instanceof Seekable) {
        throw new IOException('Underlying stream does not support seeking');
      }
      return self::$streams[$this->id]->tell();
    }

    /**
     * Callback for fstat
     *
     * @return  array<string, var> stat
     */
    public function stream_stat() {
      return array('size' => $this->length);
    }

    /**
     * Callback for stat
     *
     * @see     php://streamwrapper.url-stat
     * @param   string path
     * @param   int flags
     * @return  array<string, var> stat
     */
    public function url_stat($path, $flags) {
      sscanf(urldecode($path), "iostr%c://%[^$]", $m, $id);
      if (!isset(self::$streams[$id])) {
        return FALSE;
      } else if ('r' === $m) {
        return array('size' => 0);
      } else if ('w' === $m) {
        return array('size' => 0);
      }
    }

    /**
     * Stream wrapper method stream_flush
     *
     * @return  bool
     */
    public abstract function stream_flush();

    /**
     * Callback for fwrite
     *
     * @param   string data
     * @return  int length
     */
    public abstract function stream_write($data);
    
    /**
     * Callback for fread
     *
     * @param   int count
     * @return  string
     */
    public abstract function stream_read($count);

    /**
     * Callback for feof
     *
     * @return  bool eof
     */
    public abstract function stream_eof();
  }
?>
