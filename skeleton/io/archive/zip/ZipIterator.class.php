<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('io.archive.zip.ZipEntry', 'util.XPIterator');

  /**
   * Iterates on ZIP archive entries
   *
   * @test    xp://net.xp_framework.unittest.io.archive.ZipFileIteratorTest
   */
  class ZipIterator extends Object implements XPIterator {
    protected $impl= NULL;
    protected $entry= NULL;
    protected $more= TRUE;
    
    /**
     * Constructor
     *
     * @param   io.archive.zip.AbstractZipReaderImpl impl
     */
    public function __construct($impl) {
      $this->impl= $impl;
      $this->entry= $this->impl->firstEntry();
      $this->more= NULL !== $this->entry;
    }
    
    /**
     * Returns whether there are more entries, forwarding to the next
     * one if necessary.
     *
     * @return  bool
     */
    protected function nextEntry() {
      if ($this->more && NULL === $this->entry) {
        if (NULL === ($this->entry= $this->impl->nextEntry())) {
          $this->more= FALSE;
        }
      }
      return $this->more;
    }

    /**
     * Returns whether there are more entries in the zip file
     *
     * @return  bool
     */
    public function hasNext() {
      return $this->nextEntry();
    }
    
    /**
     * Returns the next entry in the zip file
     *
     * @return  io.archive.zip.ZipEntry
     * @throws  util.NoSuchElementException when there are no more elements
     */
    public function next() {
      if (!$this->nextEntry()) {
        throw new NoSuchElementException('No more entries in ZIP file');
      }

      $entry= $this->entry;
      $this->entry= NULL;
      return $entry;
    }
  }
?>
