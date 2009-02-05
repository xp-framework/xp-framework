<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'peer.ftp.FtpDir',
    'peer.ftp.collections.FtpElement', 
    'io.collections.IOCollection'
  );

  /**
   * FTP collection
   *
   * @test     xp://net.xp_framework.unittest.peer.ftp.FtpCollectionsTest
   * @purpose  IOCollection implementation
   */
  class FtpCollection extends Object implements IOCollection {
    protected 
      $dir    = NULL,
      $origin = NULL;

    private 
      $it     = NULL;
      
    /**
     * Constructor
     *
     * @param   peer.ftp.FtpDir dir
     */
    public function __construct(FtpDir $dir) {
      $this->dir= $dir;
      $this->it= xp::null();
    }
    
    /**
     * Returns this element's URI
     *
     * @return  string
     */
    public function getURI() {
      return $this->dir->getName();
    }
    
    /**
     * Open this collection
     *
     */
    public function open() { 
      $this->it= $this->dir->entries()->getIterator();
    }

    /**
     * Rewind this collection (reset internal pointer to beginning of list)
     *
     */
    public function rewind() { 
      $this->it->rewind();
    }
  
    /**
     * Retrieve next element in collection. Return NULL if no more entries
     * are available
     *
     * @return  io.collection.IOElement
     */
    public function next() {
      if (!$this->it->valid()) return NULL;

      $entry= $this->it->current();
      if ($entry instanceof FtpDir) {
        $next= new FtpCollection($entry);
      } else {
        $next= new FtpElement($entry);
      }
      $next->setOrigin($this);
      return $next;
    }

    /**
     * Close this collection
     *
     */
    public function close() { 
      $this->it= xp::null();
    }

    /**
     * Retrieve this element's size in bytes
     *
     * @return  int
     */
    public function getSize() { 
      return $this->dir->getSize();
    }

    /**
     * Retrieve this element's created date and time
     *
     * @return  util.Date
     */
    public function createdAt() {
      return $this->dir->getDate();
    }

    /**
     * Retrieve this element's last-accessed date and time
     *
     * @return  util.Date
     */
    public function lastAccessed() {
      return $this->dir->getDate();
    }

    /**
     * Retrieve this element's last-modified date and time
     *
     * @return  util.Date
     */
    public function lastModified() {
      return $this->dir->lastModified();
    }

    /**
     * Creates a string representation of this object
     *
     * @return  string
     */
    public function toString() { 
      return $this->getClassName().'(->'.$this->dir->toString().')';
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
      throw new IOException('Cannot read from a directory');
    }

    /**
     * Gets output stream to read from this element
     *
     * @return  io.streams.OutputStream
     * @throws  io.IOException
     */
    public function getOutputStream() {
      throw new IOException('Cannot write to a directory');
    }
  } 
?>
