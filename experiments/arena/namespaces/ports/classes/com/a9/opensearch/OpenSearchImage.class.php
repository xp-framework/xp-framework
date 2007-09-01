<?php
/* This class is part of the XP framework
 *
 * $Id: OpenSearchImage.class.php 9789 2007-03-28 18:15:27Z friebe $ 
 */

  namespace com::a9::opensearch;

  /**
   * Represents an open search Image
   *
   * @see      xp://com.a9.opensearch.OpenSearchDescription
   * @purpose  purpose
   */
  #[@xmlns(s= 'http://a9.com/-/spec/opensearch/1.1/')]
  class OpenSearchImage extends lang::Object {
    protected
      $type       = NULL,
      $url        = NULL,
      $width      = 0,
      $height     = 0;

    /**
     * Constructor
     *
     * @param   string type default '' content type
     * @param   string url default ''
     * @param   int width default 0
     * @param   int height default 0
     */
    public function __construct($type= '', $url= '', $width= 0, $height= 0) {
      $this->type= $type;
      $this->url= $url;
      $this->width= $width;
      $this->height= $height;
    }

    /**
     * Set type
     *
     * @param   string type
     */
    #[@xmlmapping(element= '@type')]
    public function setType($type) {
      $this->type= $type;
    }

    /**
     * Get type
     *
     * @return  string
     */
    #[@xmlfactory(element= '@type')]
    public function getType() {
      return $this->type;
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
    #[@xmlfactory(element= '@width')]
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
    #[@xmlfactory(element= '@height')]
    public function getHeight() {
      return $this->height;
    }

    /**
     * Set url
     *
     * @param   string url
     */
    #[@xmlmapping(element= '.')]
    public function setUrl($url) {
      $this->url= $url;
    }

    /**
     * Get url
     *
     * @return  string
     */
    #[@xmlfactory(element= '.')]
    public function getUrl() {
      return $this->url;
    }
  }
?>
