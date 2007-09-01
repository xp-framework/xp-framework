<?php
/* This class is part of the XP framework
 *
 * $Id: FlickrPhotoSize.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace com::flickr;

  ::uses('peer.URL');

  /**
   * Class containing information about sizes
   * of a FlickrPhoto
   *
   * @purpose  Represent dimension information
   */
  class FlickrPhotoSize extends lang::Object {
    public
      $label    = '',
      $width    = 0,
      $height   = 0,
      $url      = NULL,
      $source   = NULL;

    /**
     * Set Label
     *
     * @param   string label
     */
    #[@xmlmapping(element= '@label')]
    public function setLabel($label) {
      $this->label= $label;
    }

    /**
     * Get Label
     *
     * @return  string
     */
    public function getLabel() {
      return $this->label;
    }

    /**
     * Set Width
     *
     * @param   int width
     */
    #[@xmlmapping(element= '@width', type= 'int')]
    public function setWidth($width) {
      $this->width= $width;
    }

    /**
     * Get Width
     *
     * @return  int
     */
    public function getWidth() {
      return $this->width;
    }

    /**
     * Set Height
     *
     * @param   int height
     */
    #[@xmlmapping(element= '@height', type= 'int')]
    public function setHeight($height) {
      $this->height= $height;
    }

    /**
     * Get Height
     *
     * @return  int
     */
    public function getHeight() {
      return $this->height;
    }

    /**
     * Set Url
     *
     * @param   &lang.Object url
     */
    #[@xmlmapping(element= '@url')]
    public function setUrl($url) {
      $this->url= new peer::URL($url);
    }

    /**
     * Get Url
     *
     * @return  &lang.Object
     */
    public function getUrl() {
      return $this->url;
    }

    /**
     * Set Source
     *
     * @param   &lang.Object source
     */
    #[@xmlmapping(element= '@source')]
    public function setSource($source) {
      $this->source= new peer::URL($source);
    }

    /**
     * Get Source
     *
     * @return  &lang.Object
     */
    public function getSource() {
      return $this->source;
    }

    /**
     * Builds the string representation of this object
     *
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
