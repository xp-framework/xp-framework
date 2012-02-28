<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'io.streams.OutputStream', 
    'io.archive.zip.ZipArchiveWriter',
    'io.archive.zip.Compression', 
    'io.streams.MemoryOutputStream',
    'security.checksum.CRC32'
  );

  /**
   * Output stream for files
   *
   * @see      xp://io.archive.zip.ZipArchiveWriter#addFile
   * @purpose  Stream
   */
  class CipheringZipFileOutputStream extends Object implements OutputStream {
    protected
      $writer      = NULL,
      $compression = NULL,
      $data        = NULL,
      $size        = 0,
      $md          = NULL;
      
    protected $cipher= NULL;
    
    /**
     * Constructor
     *
     * @param   io.archive.zip.ZipArchiveWriter writer
     * @param   io.archive.zip.ZipFileEntry file
     * @param   io.archive.zip.ZipCipher cipher
     */
    public function __construct(ZipArchiveWriter $writer, ZipFileEntry $file, ZipCipher $cipher) {
      $this->writer= $writer;
      $this->file= $file;
      $this->data= NULL;
      $this->md= CRC32::digest();
      $this->cipher= $cipher;
    }
    
    /**
     * Sets compression method
     *
     * @param   io.archive.zip.Compression compression
     * @param   int level default 6
     * @return  io.archive.zip.ZipFileOutputStream this
     */
    public function withCompression(Compression $compression, $level= 6) {
      $this->data= new MemoryOutputStream();
      $this->compression= array($compression, $level);
      return $this;
    }
    
    /**
     * Write a string
     *
     * @param   var arg
     */
    public function write($arg) {
      $this->size+= strlen($arg);
      $this->md->update($arg);
      $this->data->write($arg);
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

      // Calculate CRC32
      $crc32= create(new CRC32($this->md->digest()))->asInt32();

      // Create random bytes
      $rand= '';
      for ($i= 0; $i < 11; $i++) {
        $rand.= chr(mt_rand(0, 255));
      }
      $preamble= $this->cipher->cipher($rand.chr(($crc32 >> 24) & 0xFF));
      
      // Now cipher and the compress raw bytes
      $compressed= new MemoryOutputStream();
      $compression= $this->compression[0]->getCompressionStream($compressed, $this->compression[1]);
      $compression->write($this->cipher->cipher($this->data->getBytes()));
      $bytes= $compressed->getBytes();
      
      // Finally, write header, preamble and bytes
      $this->writer->writeFile(
        $this->file,
        $this->size, 
        strlen($bytes) + strlen($preamble),
        $crc32,
        1
      );
      $this->writer->streamWrite($preamble);
      $this->writer->streamWrite($bytes);
      
      delete($this->data);
    }
  }
?>
