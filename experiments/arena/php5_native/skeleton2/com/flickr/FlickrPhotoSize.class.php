<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('peer.URL');

  /**
   * Class containing information about sizes
   * of a FlickrPhoto
   *
   * @purpose  Represent dimension information
   */
  class FlickrPhotoSize extends Object {
    public
      $label    = '',
      $width    = 0,
      $height   = 0,
      $url      = NULL,
      $source   = NULL;

    /**
     * Set Label
     *
     * @access  public
     * @param   string label
     */
    #[@xmlmapping(element= '@label')]
    public function setLabel($label) {
      $this->label= $label;
    }

    /**
     * Get Label
     *
     * @access  public
     * @return  string
     */
    public function getLabel() {
      return $this->label;
    }

    /**
     * Set Width
     *
     * @access  public
     * @param   int width
     */
    #[@xmlmapping(element= '@width', type= 'int')]
    public function setWidth($width) {
      $this->width= $width;
    }

    /**
     * Get Width
     *
     * @access  public
     * @return  int
     */
    public function getWidth() {
      return $this->width;
    }

    /**
     * Set Height
     *
     * @access  public
     * @param   int height
     */
    #[@xmlmapping(element= '@height', type= 'int')]
    public function setHeight($height) {
      $this->height= $height;
    }

    /**
     * Get Height
     *
     * @access  public
     * @return  int
     */
    public function getHeight() {
      return $this->height;
    }

    /**
     * Set Url
     *
     * @access  public
     * @param   &lang.Object url
     */
    #[@xmlmapping(element= '@url')]
    public function setUrl(&$url) {
      $this->url= new URL($url);
    }

    /**
     * Get Url
     *
     * @access  public
     * @return  &lang.Object
     */
    public function &getUrl() {
      return $this->url;
    }

    /**
     * Set Source
     *
     * @access  public
     * @param   &lang.Object source
     */
    #[@xmlmapping(element= '@source')]
    public function setSource(&$source) {
      $this->source= new URL($source);
    }

    /**
     * Get Source
     *
     * @access  public
     * @return  &lang.Object
     */
    public function &getSource() {
      return $this->source;
    }

    /**
     * Builds the string representation of this object
     *
     * @access  public
     * @return  string
     */
    public function toString() {
      return sprintf('%s (%dx%d) @ %s',
        $this->label,
        $this->width,
        $this->height,
        $this->source->getURL()
      );
    }
  }
?>
