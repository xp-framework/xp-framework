<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('img.util.ExifData', 'img.util.IptcData');

  /**
   * Represents a single image within an album.
   *
   * @see      xp://de.thekid.dialog.Album
   * @purpose  Value object
   */
  class AlbumImage extends Object {
    public
      $name       = '',
      $exifData   = NULL,
      $iptcData   = NULL;

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
     * @param   img.util.ExifData exifData default NULL
     */
    public function setExifData(ExifData $exifData= NULL) {
      $this->exifData= $exifData;
    }

    /**
     * Get exifData
     *
     * @return  img.util.ExifData
     */
    public function getExifData() {
      return $this->exifData;
    }

    /**
     * Set IptcData
     *
     * @param   img.util.IptcData iptcData default NULL
     */
    public function setIptcData(IptcData $iptcData= NULL) {
      $this->iptcData= $iptcData;
    }

    /**
     * Get IptcData
     *
     * @return  img.util.IptcData
     */
    public function getIptcData() {
      return $this->iptcData;
    }
    
    /**
     * Retrieve a string representation
     *
     * @return  string
     */
    public function toString() {
      return sprintf(
        '%s(%s) <%s/%s>',
        $this->getClassName(),
        $this->name,
        str_replace("\n", "\n  ", xp::stringOf($this->exifData)),
        str_replace("\n", "\n  ", xp::stringOf($this->iptcData))
      );
    }
  }
?>
