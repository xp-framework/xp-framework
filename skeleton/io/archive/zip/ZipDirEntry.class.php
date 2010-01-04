<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('io.archive.zip.ZipEntry');

  /**
   * Represents a Dir entry in a zip archive
   *
   * @see      xp://io.archive.zip.ZipEntry
   * @purpose  Interface
   */
  class ZipDirEntry extends Object implements ZipEntry {
    protected 
      $name        = '', 
      $mod         = NULL,
      $compression = NULL;
        
    /**
     * Constructor
     *
     * @param   string name
     */
    public function __construct($name) {
      $this->name= rtrim(str_replace('\\', '/', $name), '/').'/';
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
      return 0;
    }

    /**
     * Sets a zip entry's size
     *
     * @param   int size
     */
    public function setSize($size) {
      // NOOP
    }

    /**
     * Returns whether this entry is a directory
     *
     * @return  bool
     */
    public function isDirectory() {
      return TRUE;
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
        "}",
        $this->getClassName(),
        $this->name,
        xp::stringOf($this->mod)
      );
    }
  }
?>
