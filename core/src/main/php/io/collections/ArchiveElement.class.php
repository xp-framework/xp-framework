<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('io.collections.IOElement', 'lang.archive.Archive', 'io.streams.MemoryInputStream');

  /**
   * Represents a file element
   *
   * @see      xp://io.collections.ArchiveCollection
   * @purpose  Interface
   */
  class ArchiveElement extends Object implements IOElement {
    protected
      $archive = NULL,
      $name    = '',
      $origin  = NULL;

    /**
     * Constructor
     *
     * @param   lang.archive.Archive archive
     * @param   string name
     */
    public function __construct(Archive $archive, $name) {
      $archive->isOpen() || $archive->open(ARCHIVE_READ);
      $this->archive= $archive;
      $this->name= $name;
    }

    /**
     * Returns this element's URI
     *
     * @return  string
     */
    public function getURI() { 
      return 'xar://'.$this->archive->getURI().'?'.$this->name;
    }

    /**
     * Retrieve this element's size in bytes
     *
     * @return  int
     */
    public function getSize() { 
      return $this->archive->_index[$this->name][0];
    }

    /**
     * Retrieve this element's created date and time
     *
     * @return  util.Date
     */
    public function createdAt() {
      return NULL;
    }

    /**
     * Retrieve this element's last-accessed date and time
     *
     * @return  util.Date
     */
    public function lastAccessed() {
      return NULL;
    }

    /**
     * Retrieve this element's last-modified date and time
     *
     * @return  util.Date
     */
    public function lastModified() {
      return NULL;
    }
    
    /**
     * Creates a string representation of this object
     *
     * @return  string
     */
    public function toString() { 
      return $this->getClassName().'('.$this->archive->toString().'?'.$this->name.')';
    }

    /**
     * Checks whether a given element is equal to this element
     *
     * @param   lang.Generic cmp
     * @return  bool
     */
    public function equals($cmp) { 
      return $cmp instanceof self && $cmp->getURI() === $this->getURI();
    }

    /**
     * Gets origin of this element
     *
     * @return  io.collections.IOCollection
     */
    public function getOrigin() {
      return $this->origin;
    }

    /**
     * Sets origin of this element
     *
     * @param   io.collections.IOCollection
     */
    public function setOrigin(IOCollection $origin) {
      $this->origin= $origin;
    }

    /**
     * Gets input stream to read from this element
     *
     * @return  io.streams.InputStream
     * @throws  io.IOException
     */
    public function getInputStream() {
      return new MemoryInputStream($this->archive->extract($this->name));
    }

    /**
     * Gets output stream to read from this element
     *
     * @return  io.streams.OutputStream
     * @throws  io.IOException
     */
    public function getOutputStream() {
      throw new IOException('Cannot write to an archive');
    }
  } 
?>
