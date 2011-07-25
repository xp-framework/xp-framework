<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('io.archive.zip.ZipEntry');

  /**
   * Represents a file entry in a zip archive
   *
   * @test     xp://net.xp_framework.unittest.io.archive.ZipEntryTest
   * @see      xp://io.archive.zip.ZipEntry
   * @purpose  Interface
   */
  class ZipFileEntry extends Object implements ZipEntry {
    protected 
      $name         = '',
      $size         = 0,
      $mod          = NULL,
      $compression  = NULL;
    
    public
      $is   = NULL,
      $os   = NULL;
        
    /**
     * Constructor
     *
     * @param   var... parts
     */
    public function __construct() {
      $this->name= '';
      $args= func_get_args();
      foreach ($args as $part) {
        if ($part instanceof ZipDirEntry) {
          $this->name.= $part->getName();
        } else {
          $this->name.= strtr($part, '\\', '/').'/';
        }
      }
      $this->name= rtrim($this->name, '/');
      $this->mod= Date::now();
      $this->compression= array(Compression::$NONE, 6);
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
      return $this->compression[0];
    }

    /**
     * Use a given compression
     *
     * @param   int level default 6
     * @param   io.archive.zip.Compression compression
     */
    public function setCompression(Compression $compression, $level= 6) {
      $this->compression= array($compression, $level);
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
      return $this->compression[0]->getDecompressionStream($this->is);
    }

    /**
     * Returns an output stream for writing to this entry
     *
     * @return  io.streams.OutputStream
     */
    public function getOutputStream() {
      return $this->os->withCompression($this->compression[0],  $this->compression[1]);
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
        "  [compression ] %s level %d\n".
        "  [size        ] %d\n".
        "}",
        $this->getClassName(),
        $this->name,
        xp::stringOf($this->mod),
        xp::stringOf($this->compression[0]),
        $this->compression[1],
        $this->size
      );
    }
  }
?>
