<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  /**
   * Image geometry for local and remote files
   *
   * <code>
   *   $i= new ImageGeometry('image.jpg');
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
    const
      IMAGE_GIF = 1,
      IMAGE_JPEG = 2,
      IMAGE_PNG = 3,
      IMAGE_SWF = 4,
      IMAGE_PSD = 5,
      IMAGE_BMP = 6,
      IMAGE_TIFF_INTEL = 7,
      IMAGE_TIFF_MOTOROLA = 8,
      IMAGE_JPC = 9,
      IMAGE_JP2 = 10,
      IMAGE_JPX = 11,
      IMAGE_JB2 = 12,
      IMAGE_SWC = 13,
      IMAGE_IFF = 14;

    public
      $image= '',
      $_info= array();
      
    /**
     * Constructor
     *
     * @access  public
     * @param   string image default NULL the image URL
     */
    public function __construct($image= NULL) {
      if (NULL !== $image) self::setImage($image);
      
    }
    
    /**
     * Sets image URL
     *
     * @access  public
     * @param   string image image URL
     */
    public function setImage($image) {
      $this->image= $image;
      $this->_info= array();
    }
    
    /**
     * Private helper which calculates image information
     *
     * @access  private
     * @return  bool success
     * @throws  IllegalStateException when no image is defined
     * @throws  FormatException when retrieving image information fails
     */
    private function _calc() {
      if (FALSE === $this->_info) return FALSE;
      if (empty($this->image)) {
        throw (new IllegalStateException('No image defined'));
        return FALSE;
      }
      if (FALSE === ($this->_info= getimagesize($this->image))) {
        throw (new FormatException('Unable to retrieve image information for '.$this->image));
        return FALSE;
      }
      return TRUE;
    }
    
    /**
     * Retrieves image's width
     *
     * @access  public
     * @return  int width or FALSE in case of an error
     */
    public function getWidth() {
      if (empty($this->_info) && !self::_calc()) return FALSE;
      return $this->_info[0];
    }
    
    /**
     * Retrieves image's height
     *
     * @access  public
     * @return  int width or FALSE in case of an error
     */
    public function getHeight() {
      if (empty($this->_info) && !self::_calc()) return FALSE;
      return $this->_info[1];
    }
    
    /**
     * Retrieves image's dimensions [x, y]
     *
     * @access  public
     * @return  int[] width, height or FALSE in case of an error
     */
    public function getDimensions() {
      if (empty($this->_info) && !self::_calc()) return FALSE;
      return array_slice($this->_info, 0, 2);
    }
    
    /**
     * Retrieves image's type
     *
     * @access  public
     * @return  int type one of the IMAGE_* constants or FALSE in case of an error
     */
    public function getImageType() {
      if (empty($this->_info) && !self::_calc()) return FALSE;
      return $this->_info[2];    
    }
    
    /**
     * Retrieves extended data about the image. These may contain, dependant on
     * the image type, different fields.
     *
     * @access  public
     * @return  mixed[] extended data
     */
    public function getExData() {
      if (empty($this->_info) && !self::_calc()) return FALSE;
      $i= $this->_info;
      unset($i[0], $i[1], $i[2], $i[3]);
      return $i; 
    }
  }
?>
