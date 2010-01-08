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
    protected static $workaround= FALSE;
    
    static function __static() {
      self::$workaround= version_compare(phpversion(), '5.2.1', 'lt');
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

      // Workaround endless loop in zlib filter - PHP Bug #40189 - by
      // reading the entire content into memory. This bug occurs in PHP
      // 5.2.0 only (not in 5.2.1, for example).
      if (self::$workaround) {
        $mem= XPClass::forName('io.streams.MemoryInputStream');
        $this->in= Streams::readableFd($mem->newInstance(gzinflate(Streams::readAll($in))));
        return;
      }
      
      // Now, convert stream to file handle and append inflating filter
      $this->in= Streams::readableFd($in);
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
    }
  }
?>
