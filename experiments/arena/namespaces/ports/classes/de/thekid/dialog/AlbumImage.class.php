<?php
/* This class is part of the XP framework
 *
 * $Id: AlbumImage.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace de::thekid::dialog;

  ::uses('img.util.ExifData');

  /**
   * Represents a single image within an album.
   *
   * @see      xp://de.thekid.dialog.Album
   * @purpose  Value object
   */
  class AlbumImage extends lang::Object {
    public
      $name       = '',
      $exifData   = NULL;

    /**
     * Constructor
     *
     * @param   string name
     */
    public function __construct($name) {
      $this->name= $name;
    }

    /**
     * Set name
     *
     * @param   string name
     */
    public function setName($name) {
      $this->name= $name;
    }

    /**
     * Get name
     *
     * @return  string
     */
    public function getName() {
      return $this->name;
    }

    /**
     * Set exifData
     *
     * @param   &img.util.ExifData exifData
     */
    public function setExifData($exifData) {
      $this->exifData= $exifData;
    }

    /**
     * Get exifData
     *
     * @return  &img.util.ExifData
     */
    public function getExifData() {
      return $this->exifData;
    }
    
    /**
     * Retrieve a string representation
     *
     * @return  string
     */
    public function toString() {
      return sprintf(
        '%s(%s) <%s>',
        $this->getClassName(),
        $this->name,
        str_replace("\n", "\n  ", ::xp::stringOf($this->exifData))
      );
    }
  }
?>
