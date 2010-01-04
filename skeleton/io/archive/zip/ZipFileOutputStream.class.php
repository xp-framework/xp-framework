<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'io.streams.OutputStream', 
    'io.archive.zip.Compression', 
    'io.streams.MemoryOutputStream',
    'security.checksum.CRC32Digest'
  );

  /**
   * Output stream for files
   *
   * @see      xp://io.archive.zip.ZipArchiveWriter#addEntry
   * @purpose  Stream
   */
  class ZipFileOutputStream extends Object implements OutputStream {
    protected
      $writer      = NULL,
      $compression = NULL,
      $data        = NULL,
      $size        = 0,
      $crc32       = NULL;
    
    /**
     * Constructor
     *
     * @param   io.archive.zip.ZipArchiveWriter writer
     * @param   io.archive.zip.ZipFileEntry file
     */
    public function __construct(ZipArchiveWriter $writer, ZipFileEntry $file) {
      $this->writer= $writer;
      $this->file= $file;
      $this->data= NULL;
      $this->crc32= new CRC32Digest();
    }
    
    /**
     * Sets compression method
     *
     * @param   io.archive.zip.Compression compression
     * @return  io.archive.zip.ZipFileOutputStream this
     */
    public function withCompression(Compression $compression) {
      $this->data= new MemoryOutputStream();
      $this->compression= $compression->getCompressionStream($this->data);
      return $this;
    }
    
    /**
     * Write a string
     *
     * @param   mixed arg
     */
    public function write($arg) {
      $this->size+= strlen($arg);
      $this->crc32->update($arg);
      $this->compression->write($arg);
    }

    /**
     * Flush this buffer
     *
     */
    public function flush() {
      // NOOP
    }

    /**
     * Close this buffer
     *
     */
    public function close() {
      if (NULL === $this->data) return;     // Already written
      
      $crc= $this->crc32->digest()->getValue();
      if ($crc > 2147483647) {              // Convert from uin32 to int32
        $crc= intval($crc - 4294967296);
      }
      
      $this->compression->close();
      $bytes= $this->data->getBytes();
      $this->writer->writeFile(
        $this->file,
        $this->size, 
        strlen($bytes),
        $crc,
        $bytes
      );
      delete($this->data);
    }
  }
?>
