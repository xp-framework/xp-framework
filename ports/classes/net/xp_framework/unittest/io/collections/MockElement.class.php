<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('io.collections.IOElement', 'io.streams.MemoryInputStream', 'io.streams.MemoryOutputStream');

  /**
   * Represents a Mock element
   *
   * @see      xp://net.xp_framework.unittest.io.collections.MockCollection
   * @purpose  Mock object
   */
  class MockElement extends Object implements IOElement {
    protected
      $uri    = '',
      $size   = 0,
      $adate  = NULL,
      $mdate  = NULL,
      $cdate  = NULL,
      $origin = NULL;

    /**
     * Constructor
     *
     * @param   string uri
     * @param   int size default 0
     * @param   util.Date adate default NULL
     * @param   util.Date adate default NULL
     * @param   util.Date cdate default NULL
     */
    public function __construct($uri, $size= 0, $adate= NULL, $mdate= NULL, $cdate= NULL) {
      $this->uri= $uri;
      $this->size= $size;
      $this->adate= $adate;
      $this->mdate= $mdate;
      $this->cdate= $cdate;
    }

    /**
     * Returns this element's URI
     *
     * @return  string
     */
    public function getURI() { 
      return $this->uri;
    }

    /**
     * Retrieve this element's size in bytes
     *
     * @return  int
     */
    public function getSize() { 
      return $this->size;
    }

    /**
     * Retrieve this element's created date and time
     *
     * @return  util.Date
     */
    public function createdAt() {
      return $this->cdate;
    }

    /**
     * Retrieve this element's last-accessed date and time
     *
     * @return  util.Date
     */
    public function lastAccessed() {
      return $this->adate;
    }

    /**
     * Retrieve this element's last-modified date and time
     *
     * @return  util.Date
     */
    public function lastModified() {
      return $this->mdate;
    }

    /**
     * Creates a string representation of this object
     *
     * @return  string
     */
    public function toString() { 
      return $this->getClassName().'('.$this->uri.')';
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
      return new MemoryInputStream('File contents of {'.$this->uri.'}');
    }

    /**
     * Gets output stream to read from this element
     *
     * @return  io.streams.OutputStream
     * @throws  io.IOException
     */
    public function getOutputStream() {
      return new MemoryOutputStream();
    }
  } 
?>
