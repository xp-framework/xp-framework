<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('io.streams.OutputStream', 'io.streams.Streams', 'security.checksum.CRC32Digest');

  /**
   * OuputStream that compresses content using GZIP encoding. Data
   * produced with this stream can be read with the "gunzip" command
   * line utility and its "zcat", "zgrep", ... list of friends.
   *
   * @ext      zlib
   * @see      rfc://1952
   * @purpose  OuputStream implementation
   */
  class GzCompressingOutputStream extends Object implements OutputStream {
    protected $out= NULL;
    protected $digest= NULL;
    protected $length= NULL;
    protected $filter= NULL;
    
    /**
     * Constructor
     *
     * @param   io.streams.OutputStream out
     * @param   int level default 6
     * @throws  lang.IllegalArgumentException if the level is not between 0 and 9
     */
    public function __construct(OutputStream $out, $level= 6) {
      if ($level < 0 || $level > 9) {
        throw new IllegalArgumentException('Level '.$level.' out of range [0..9]');
      }

      // Write GZIP format header:
      // * ID1, ID2 (Identification, \x1F, \x8B)
      // * CM       (Compression Method, 8 = deflate)
      // * FLG      (Flags, use 0)
      // * MTIME    (Modification time, Un*x timestamp)
      // * XFL      (Extra flags, 2 = compressor used maximum compression)
      // * OS       (Operating system, 255 = unknown)
      $out->write(pack('CCCCVCC', 0x1F, 0x8B, 8, 0, time(), 2, 255));
      
      // Now, convert stream to file handle and append deflating filter
      $this->out= Streams::writeableFd($out);
      if (!($this->filter= stream_filter_append($this->out, 'zlib.deflate', STREAM_FILTER_WRITE, $level))) {
        throw new IOException('Could not append stream filter');
      }
      $this->digest= new CRC32Digest();
    }
    
    /**
     * Write a string
     *
     * @param   mixed arg
     */
    public function write($arg) {
      fwrite($this->out, $arg);
      $this->length+= strlen($arg);
      $this->digest->update($arg);
    }

    /**
     * Flush this buffer
     *
     */
    public function flush() {
      fflush($this->out);
    }

    /**
     * Close this buffer. Flushes this buffer and then calls the close()
     * method on the underlying OuputStream.
     *
     */
    public function close() {
    
      // Remove deflating filter so we can continue writing "raw"
      stream_filter_remove($this->filter);
      
      // Write GZIP footer:
      // * CRC32    (CRC-32 checksum)
      // * ISIZE    (Input size)
      fwrite($this->out, pack('VV', $this->digest->digest()->asInt32(), $this->length));
      fclose($this->out);
    }
    
    /**
     * Destructor. Ensures output stream is closed.
     *
     */
    public function __destruct() {
      fclose($this->out);
    }
  }
?>
