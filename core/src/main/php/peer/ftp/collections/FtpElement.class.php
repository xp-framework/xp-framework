<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('io.collections.IOElement', 'peer.ftp.FtpInputStream', 'peer.ftp.FtpOutputStream');

  /**
   * Represents an FTP element
   *
   * @test     xp://net.xp_framework.unittest.peer.ftp.FtpCollectionsTest
   * @purpose  Interface
   */
  class FtpElement extends Object implements IOElement {
    protected 
      $file   = NULL,
      $origin = NULL;

    /**
     * Constructor
     *
     * @param   peer.ftp.FtpFile file
     */
    public function __construct($file) {
      $this->file= $file;
    }

    /**
     * Returns this element's URI
     *
     * @return  string
     */
    public function getURI() { 
      return $this->file->getName();
    }

    /**
     * Retrieve this element's size in bytes
     *
     * @return  int
     */
    public function getSize() { 
      return $this->file->getSize();
    }

    /**
     * Retrieve this element's created date and time
     *
     * @return  util.Date
     */
    public function createdAt() {
      return $this->file->getDate();
    }

    /**
     * Retrieve this element's last-accessed date and time
     *
     * @return  util.Date
     */
    public function lastAccessed() {
      return $this->file->getDate();
    }

    /**
     * Retrieve this element's last-modified date and time
     *
     * @return  util.Date
     */
    public function lastModified() {
      return $this->file->lastModified();
    }

    /**
     * Creates a string representation of this object
     *
     * @return  string
     */
    public function toString() { 
      return $this->getClassName().'(->'.$this->file->toString().')';
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
      return new FtpInputStream($this->file);
    }

    /**
     * Gets output stream to read from this element
     *
     * @return  io.streams.OutputStream
     * @throws  io.IOException
     */
    public function getOutputStream() {
      return new FtpOutputStream($this->file);
    }
  } 
?>
