<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('io.archive.zip.RandomAccessZipReaderImpl', 'io.archive.zip.SequentialZipReaderImpl', 'io.archive.zip.ZipEntries');

  /**
   * Read from a zip file
   *
   * @see      xp://io.archive.zip.ZipArchive#open
   * @purpose  Write to a zip archive
   */
  class ZipArchiveReader extends Object {

    /**
     * Creation constructor
     *
     * @param   io.streams.InputStream stream
     */
    public function __construct(InputStream $stream) {
      if ($stream instanceof Seekable) {
        $this->impl= new RandomAccessZipReaderImpl($stream);
      } else {
        $this->impl= new SequentialZipReaderImpl($stream);
      }
    }

    /**
     * Returns a list of all entries in this zip file
     *
     * @return  io.archive.zip.ZipEntries
     */
    public function entries() {
      return new ZipEntries($this->impl);
    }
  }
?>
