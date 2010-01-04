<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('util.Date', 'io.archive.zip.Compression');

  /**
   * Represents an entry in a zip archive
   *
   * @see      xp://io.archive.zip.ZipArchive
   * @purpose  Interface
   */
  interface ZipEntry {
    
    /**
     * Gets a zip entry's name
     *
     * @return  string
     */
    public function getName();

    /**
     * Gets a zip entry's last modification time
     *
     * @return  util.Date
     */
    public function getLastModified();

    /**
     * Sets a zip entry's last modification time
     *
     * @param   util.Date lastModified
     */
    public function setLastModified(Date $lastModified);

    /**
     * Returns which compression was used
     *
     * @return  io.archive.zip.Compression
     */
    public function getCompression();

    /**
     * Use a given compression
     *
     * @param   io.archive.zip.Compression compression
     */
    public function setCompression(Compression $compression);

    /**
     * Gets a zip entry's size
     *
     * @return  int
     */
    public function getSize();

    /**
     * Sets a zip entry's size
     *
     * @param   int size
     */
    public function setSize($size);

    /**
     * Returns whether this entry is a directory
     *
     * @return  bool
     */
    public function isDirectory();
  }
?>
