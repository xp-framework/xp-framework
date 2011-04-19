<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('io.archive.zip.AbstractZipReaderImpl', 'io.streams.Seekable');

  /**
   * Zip archive reader that works on any input stream.
   *
   */
  class SequentialZipReaderImpl extends AbstractZipReaderImpl {
    private $initial= TRUE;

    /**
     * Get first entry
     *
     * @return  io.archive.zip.ZipEntry
     */
    public function firstEntry() {
      if (!$this->initial) {
        throw new IllegalStateException('Stream not rewindable');
      }
      $this->initial= FALSE;
      return $this->currentEntry();
    }
    
    /**
     * Get next entry
     *
     * @return  io.archive.zip.ZipEntry
     */
    public function nextEntry() {
      $this->skip && $this->streamRead($this->skip);
      return $this->currentEntry();
    }

    /**
     * Seeks a stream
     *
     * @param   int offset absolute offset
     * @param   int whence
     */
    protected function streamSeek($offset, $whence) {
      throw new IllegalArgumentException('Stream not seekable');
    }
  }
?>
