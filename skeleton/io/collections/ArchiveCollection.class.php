<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('io.collections.ArchiveElement', 'io.collections.IOCollection');

  /**
   * Archive collection
   *
   * @test     xp://net.xp_framework.unittest.io.collections.ArchiveCollectionTest
   * @see      xp://io.collections.IOCollection
   * @purpose  IOCollection implementation
   */
  class ArchiveCollection extends Object implements IOCollection {
    protected
      $archive = NULL,
      $origin  = NULL,
      $base    = '',
      $_dirs   = array();
      
    /**
     * Constructor
     *
     * @param   lang.archive.Archive archive
     * @param   string base
     */
    public function __construct(Archive $archive, $base= '') {
      $this->archive= $archive;
      $this->base= $base;
    }
    
    /**
     * Returns this element's URI
     *
     * @return  string
     */
    public function getURI() {
      return 'xar://'.$this->archive->getURI().'?'.$this->base;
    }
    
    /**
     * Open this collection
     *
     */
    public function open() { 
      $this->archive->isOpen() || $this->archive->open(ARCHIVE_READ);
      reset($this->archive->_index);
      $this->_dirs= array();
    }

    /**
     * Rewind this collection (reset internal pointer to beginning of list)
     *
     */
    public function rewind() { 
      reset($this->archive->_index);
      $this->_dirs= array();
    }
  
    /**
     * Retrieve next element in collection. Return NULL if no more entries
     * are available
     *
     * @return  io.collection.IOElement
     */
    public function next() {
      $baselen= strlen($this->base);
      $next= NULL;
      do {
        if (NULL === ($name= key($this->archive->_index))) return NULL;
        next($this->archive->_index);
        if (0 !== strncmp($this->base, $name, $baselen)) continue;
        $entry= substr($name, $baselen+ 1);
        if (FALSE !== ($p= strpos($entry, '/'))) {
          $dir= substr($name, 0, $baselen+ 1+ $p);
          if (isset($this->_dirs[$dir])) continue;
          $this->_dirs[$dir]= TRUE;
          $next= new ArchiveCollection($this->archive, $dir);
        } else {
          $next= new ArchiveElement($this->archive, $name);
        }
      } while (!$next);
      
      $next->setOrigin($this);
      return $next;
    }

    /**
     * Close this collection
     *
     */
    public function close() { 
      $this->archive->close();
      $this->_dirs= array();
    }

    /**
     * Retrieve this element's size in bytes
     *
     * @return  int
     */
    public function getSize() { 
      return 0;
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
      return $this->getClassName().'('.$this->archive->toString().'?'.$this->base.')';
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
