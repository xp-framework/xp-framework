<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('img.util.ExifData');

  /**
   * Represents a single image within an album.
   *
   * @see      xp://de.thekid.dialog.Album
   * @purpose  Value object
   */
  class AlbumImage extends Object {
    var
      $name       = '',
      $exifData   = NULL;

    /**
     * Constructor
     *
     * @access  public
     * @param   string name
     */
    function __construct($name) {
      $this->name= $name;
    }

    /**
     * Set name
     *
     * @access  public
     * @param   string name
     */
    function setName($name) {
      $this->name= $name;
    }

    /**
     * Get name
     *
     * @access  public
     * @return  string
     */
    function getName() {
      return $this->name;
    }

    /**
     * Set exifData
     *
     * @access  public
     * @param   &img.util.ExifData exifData
     */
    function setExifData(&$exifData) {
      $this->exifData= &$exifData;
    }

    /**
     * Get exifData
     *
     * @access  public
     * @return  &img.util.ExifData
     */
    function &getExifData() {
      return $this->exifData;
    }
    
    /**
     * Retrieve a string representation
     *
     * @access  public
     * @return  string
     */
    function toString() {
      return sprintf(
        '%s(%s) <%s>',
        $this->getClassName(),
        $this->name,
        str_replace("\n", "\n  ", xp::stringOf($this->exifData))
      );
    }
  }
?>
