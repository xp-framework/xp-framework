<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  // Image types
  define('IMAGE_GIF',           1);
  define('IMAGE_JPEG',          2);
  define('IMAGE_PNG',           3);
  define('IMAGE_SWF',           4);
  define('IMAGE_PSD',           5);
  define('IMAGE_BMP',           6);
  define('IMAGE_TIFF_INTEL',    7);
  define('IMAGE_TIFF_MOTOROLA', 8);
  define('IMAGE_JPC',           9);
  define('IMAGE_JP2',          10);
  define('IMAGE_JPX',          11);
  define('IMAGE_JB2',          12);
  define('IMAGE_SWC',          13);
  define('IMAGE_IFF',          14);
 
  /**
   * Image geometry for local and remote files
   *
   * <code>
   *   $i= &new ImageGeometry('image.jpg');
   *   try(); {
   *     list($width, $height)= $i->getDimensions();
   *   } if (catch('FormatException', $e)) {
   *     $e->printStackTrace();
   *     exit();
   *   }
   *   var_dump($i->image, $width, $height);
   * </code>
   *
   * @see php://getimagesize
   */
  class ImageGeometry extends Object {
    var
      $image= '',
      $_info= array();
      
    /**
     * Constructor
     *
     * @access  public
     * @param   string image default NULL the image URL
     */
    function __construct($image= NULL) {
      if (NULL !== $image) $this->setImage($image);
      parent::__construct();
    }
    
    /**
     * Sets image URL
     *
     * @access  public
     * @param   string image image URL
     */
    function setImage($image) {
      $this->image= $image;
      $this->_info= array();
    }
    
    /**
     * Private helper which calculates image information
     *
     * @access  private
     * @return  bool success
     * @throws  IllegalStateException when no image is defined
     * @throws  FormatException when retreiving image information fails
     */
    function _calc() {
      if (FALSE === $this->_info) return FALSE;
      if (empty($this->image)) {
        throw(new IllegalStateException('No image defined'));
        return FALSE;
      }
      if (FALSE === ($this->_info= getimagesize($this->image))) {
        throw(new FormatException('Unable to retreive image information for '.$this->image));
        return FALSE;
      }
      return TRUE;
    }
    
    /**
     * Retreives image's width
     *
     * @access  public
     * @return  int width or FALSE in case of an error
     */
    function getWidth() {
      if (empty($this->_info) && !$this->_calc()) return FALSE;
      return $this->_info[0];
    }
    
    /**
     * Retreives image's height
     *
     * @access  public
     * @return  int width or FALSE in case of an error
     */
    function getHeight() {
      if (empty($this->_info) && !$this->_calc()) return FALSE;
      return $this->_info[1];
    }
    
    /**
     * Retreives image's dimensions [x, y]
     *
     * @access  public
     * @return  int[] width, height or FALSE in case of an error
     */
    function getDimensions() {
      if (empty($this->_info) && !$this->_calc()) return FALSE;
      return array_slice($this->_info, 0, 2);
    }
    
    /**
     * Retreives image's type
     *
     * @access  public
     * @return  int type one of the IMAGE_* constants or FALSE in case of an error
     */
    function getImageType() {
      if (empty($this->_info) && !$this->_calc()) return FALSE;
      return $this->_info[2];    
    }
    
    /**
     * Retreives extended data about the image. These may contain, dependant on
     * the image type, different fields.
     *
     * @access  public
     * @return  mixed[] extended data
     */
    function getExData() {
      if (empty($this->_info) && !$this->_calc()) return FALSE;
      $i= $this->_info;
      unset($i[0], $i[1], $i[2], $i[3]);
      return $i; 
    }
  }
?>
