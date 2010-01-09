<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('io.streams.InputStream', 'io.streams.Streams');

  /**
   * InputStream that decompresses data compressed using GZIP encoding.
   *
   * @ext      zlib
   * @see      rfc://1952
   * @test     xp://net.xp_framework.unittest.io.streams.GzDecompressingInputStreamTest
   * @purpose  InputStream implementation
   */
  class GzDecompressingInputStream extends Object implements InputStream {
    protected $in= NULL;
    public static $wrapped= array();

    static function __static() {
      stream_wrapper_register('zlib.bounded', get_class(newinstance('lang.Object', array(), '{
        protected $id, $st= NULL;
        protected $buffer= "";
        
        public function stream_open($path, $mode, $options, $opened_path) {
          $this->st= GzDecompressingInputStream::$wrapped[$path];
          $this->id= $path;
          return TRUE;
        }

        public function stream_read($count) {

          // Ensure we have at least 9 bytes
          $l= strlen($this->buffer);
          while ($l < 9 && $this->st->available() > 0) {
            $chunk= $this->st->read($count);
            $l+= strlen($chunk);
            $this->buffer.= $chunk;
          }
          
          // Now return the everything except the last 8 bytes
          $read= substr($this->buffer, 0, -8);
          $this->buffer= substr($this->buffer, -8);
          return $read;
        }

        public function stream_eof() {
          return 0 === $this->st->available();
        }

        public function stream_flush() {
          return TRUE;
        }
        
        public function stream_close() {
          $this->st->close();
          unset(GzDecompressingInputStream::$wrapped[$this->id]);
        }
      }')));
    }
    
    /**
     * Constructor
     *
     * @param   io.streams.InputStream in
     */
    public function __construct(InputStream $in) {
      
      // Read GZIP format header
      // * ID1, ID2 (Identification, \x1F, \x8B)
      // * CM       (Compression Method, 8 = deflate)
      // * FLG      (Flags)
      // * MTIME    (Modification time, Un*x timestamp)
      // * XFL      (Extra flags)
      // * OS       (Operating system)
      $header= unpack('a2id/Cmethod/Cflags/Vtime/Cextra/Cos', $in->read(10));
      if ("\x1F\x8B" != $header['id']) {
        throw new IOException('Invalid format, expected \037\213, have '.addcslashes($header['id'], "\0..\377"));
      }
      if (8 != $header['method']) {
        throw new IOException('Unknown compression method #'.$header['method']);
      }

      // Now, convert stream to file handle and append inflating filter
      $wri= 'zlib.bounded://'.$in->hashCode();
      self::$wrapped[$wri]= $in;
      $this->in= fopen($wri, 'r');
      if (!stream_filter_append($this->in, 'zlib.inflate', STREAM_FILTER_READ)) {
        throw new IOException('Could not append stream filter');
      }
    }

    /**
     * Read a string
     *
     * @param   int limit default 8192
     * @return  string
     */
    public function read($limit= 8192) {
      return fread($this->in, $limit);
    }

    /**
     * Returns the number of bytes that can be read from this stream 
     * without blocking.
     *
     */
    public function available() {
      return feof($this->in) ? 0 : 1;
    }

    /**
     * Close this buffer.
     *
     */
    public function close() {
      fclose($this->in);
      $this->in= NULL;
    }
    
    /**
     * Destructor. Ensures output stream is closed.
     *
     */
    public function __destruct() {
      if (!$this->in) return;
      fclose($this->in);
      $this->in= NULL;
    }
  }
?>
