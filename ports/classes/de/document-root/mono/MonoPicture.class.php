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
      $title            = '',
      $description      = '';

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
     * Set Title
     *
     * @access  public
     * @param   string title
     */
    function setTitle($title) {
      $this->title= $title;
    }

    /**
     * Get Title
     *
     * @access  public
     * @return  string
     */
    function getTitle() {
      return $this->title;
    }

    /**
     * Set Description
     *
     * @access  public
     * @param   string description
     */
    function setDescription($description) {
      $this->description= $description;
    }

    /**
     * Get Description
     *
     * @access  public
     * @return  string
     */
    function getDescription() {
      return $this->description;
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
     * Build XML representation of this object.
     *
     * @access  public
     * @return  &xml.Node
     */
    function toXML() {
      with ($n= &new Node('picture')); {
        $n->addChild(new Node('width', $this->width));
        $n->addChild(new Node('height', $this->height));
        $n->addChild(new Node('filename', $this->filename));
        $n->addChild(Node::fromObject($this->exif, 'exif'));
        $n->addChild(new Node('title', $this->title));
        $n->addChild(new Node('description', new PCData($this->description)));
      }
      return $n;
    }    
  }
?>
