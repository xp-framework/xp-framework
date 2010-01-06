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
   * @purpose  InputStream implementation
   */
  class GzDecompressingInputStream extends Object implements InputStream {
    protected $in = NULL;
    
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
