<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'util.Date',
    'img.util.ExifData'
  );

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class MonoPicture extends Object {
    var
      $filename         = '',
      $exif             = NULL,
      $width            = 0,
      $height           = 0,
      $publishedAt      = NULL;

    /**
     * Set Filename
     *
     * @access  public
     * @param   string filename
     */
    function setFilename($filename) {
      $this->filename= $filename;
    }

    /**
     * Get Filename
     *
     * @access  public
     * @return  string
     */
    function getFilename() {
      return $this->filename;
    }

    /**
     * Set Exif
     *
     * @access  public
     * @param   &lang.Object exif
     */
    function setExif(&$exif) {
      $this->exif= &$exif;
    }

    /**
     * Get Exif
     *
     * @access  public
     * @return  &lang.Object
     */
    function &getExif() {
      return $this->exif;
    }

    /**
     * Set Width
     *
     * @access  public
     * @param   int width
     */
    function setWidth($width) {
      $this->width= $width;
    }

    /**
     * Get Width
     *
     * @access  public
     * @return  int
     */
    function getWidth() {
      return $this->width;
    }

    /**
     * Set Height
     *
     * @access  public
     * @param   int height
     */
    function setHeight($height) {
      $this->height= $height;
    }

    /**
     * Get Height
     *
     * @access  public
     * @return  int
     */
    function getHeight() {
      return $this->height;
    }

    /**
     * Set PublishedAt
     *
     * @access  public
     * @param   &lang.Object publishedAt
     */
    function setPublishedAt(&$publishedAt) {
      $this->publishedAt= &$publishedAt;
    }

    /**
     * Get PublishedAt
     *
     * @access  public
     * @return  &lang.Object
     */
    function &getPublishedAt() {
      return $this->publishedAt;
    }

  }
?>
