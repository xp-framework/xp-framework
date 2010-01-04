<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('io.streams.OutputStream', 'io.archive.zip.Compression', 'io.streams.MemoryOutputStream');

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
      $crc32       = 0;
    
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
      $this->original= '';
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
      $this->compression->write($arg);
      
      // HACK: Needed because we cannot calculate crc32 incrementally atm
      $this->original.= $arg;
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

      $this->compression->close();
      $bytes= $this->data->getBytes();
      $this->writer->writeFile(
        $this->file,
        $this->size, 
        strlen($bytes),
        crc32($this->original), // $this->crc32,  HACK
        $bytes
      );
      delete($this->data);
    }
  }
?>
