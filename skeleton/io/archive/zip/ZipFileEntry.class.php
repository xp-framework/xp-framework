<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('io.archive.zip.ZipEntry');

  /**
   * Represents a file entry in a zip archive
   *
   * @see      xp://io.archive.zip.ZipEntry
   * @purpose  Interface
   */
  class ZipFileEntry extends Object implements ZipEntry {
    protected 
      $name = '', 
      $mod  = NULL;
    
    public
      $is   = NULL,
      $os   = NULL;
        
    /**
     * Constructor
     *
     * @param   string name
     */
    public function __construct($name) {
      $this->name= str_replace('\\', '/', $name);
      $this->mod= Date::now();
      $this->compression= Compression::$NONE;
    }
    
    /**
     * Gets a zip entry's name
     *
     * @return  string
     */
    public function getName() {
      return $this->name;
    }
    
    /**
     * Gets a zip entry's last modification time
     *
     * @return  util.Date
     */
    public function getLastModified() {
      return $this->mod;
    }

    /**
     * Sets a zip entry's last modification time
     *
     * @param   util.Date lastModified
     */
    public function setLastModified(Date $lastModified) {
      $this->mod= $lastModified;
    }

    /**
     * Returns which compression was used
     *
     * @return  io.archive.zip.Compression
     */
    public function getCompression() {
      return $this->compression;
    }

    /**
     * Use a given compression
     *
     * @param   io.archive.zip.Compression compression
     */
    public function setCompression(Compression $compression) {
      $this->compression= $compression;
    }

    /**
     * Gets a zip entry's size
     *
     * @return  int
     */
    public function getSize() {
      return $this->size;
    }

    /**
     * Sets a zip entry's size
     *
     * @param   int size
     */
    public function setSize($size) {
      $this->size= $size;
    }

    /**
     * Returns whether this entry is a directory
     *
     * @return  bool
     */
    public function isDirectory() {
      return FALSE;
    }

    /**
     * Returns an input stream for reading from this entry
     *
     * @return  io.streams.InputStream
     */
    public function getInputStream() {
      return $this->compression->getDecompressionStream($this->is);
    }

    /**
     * Returns an output stream for writing to this entry
     *
     * @return  io.streams.OutputStream
     */
    public function getOutputStream() {
      return $this->os->withCompression($this->compression);
    }
    
    /**
     * Creates a string representation of this object
     *
     * @return  string
     */
    public function toString() {
      return sprintf(
        "%s(%s)@{\n".
        "  [lastModified] %s\n".
        "  [compression ] %s\n".
        "  [size        ] %d\n".
        "}",
        $this->getClassName(),
        $this->name,
        xp::stringOf($this->mod),
        $this->compression->name(),
        $this->size
      );
    }
  }
?>
