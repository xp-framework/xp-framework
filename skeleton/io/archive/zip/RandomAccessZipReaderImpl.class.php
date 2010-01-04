<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('io.archive.zip.AbstractZipReaderImpl', 'io.streams.Seekable');

  /**
   * Zip archive reader that works on Seekable input streams.
   *
   */
  class RandomAccessZipReaderImpl extends AbstractZipReaderImpl {

    /**
     * Creation constructor
     *
     * @param   io.streams.InputStream stream
     */
    public function __construct(InputStream $stream) {
      parent::__construct(cast($stream, 'io.streams.Seekable'));
    }
    
    /**
     * Get first entry
     *
     * @return  io.archive.zip.ZipEntry
     */
    public function firstEntry() {
      $this->stream->seek(0, SEEK_SET);
      return $this->currentEntry();
    }
    
    /**
     * Get next entry
     *
     * @return  io.archive.zip.ZipEntry
     */
    public function nextEntry() {
      $this->skip && $this->stream->seek($this->skip, SEEK_CUR);
      return $this->currentEntry();
    }
  }
?>
