<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('img.ImagingException');

  /**
   * Base class for images
   *
   * @ext gd
   * @see php://image
   */
  class Image extends Object {
    public
      $width    = 0,
      $height   = 0,
      $palette  = array();
    
    protected
      $_hdl     = NULL;
    
    /**
     * Constructor
     *
     * @access  public
     * @param   $width
     * @param   $height
     */
    public function __construct($width= -1, $height= -1) {
      $this->width= $width;
      $this->height= $height;
    }
    
    /**
     * Destructor
     *
     * @access  public
     */
    public function __destruct() {
      if (is_resource($this->_hdl)) imagedestroy($this->_hdl);
    }
    
    /**
     * Creates a new blank image in memory
     *
     * @access  public
     * @param   bool truecolor default FALSE
     */
    public function create($truecolor= FALSE) {
      if ($truecolor) {
        $this->_hdl= imagecreatetruecolor($this->width, $this->height);
      } else {
        $this->_hdl= imagecreate($this->width, $this->height);
      }
    }
    
    /**
     * Creates a new image in memory from the image stream in the
     * string
     *
     * @access  public
     * @param   string str
     * @return  bool success
     * @throws  ImagingException in case the url is not valid or the picture cannot be created
     */
    public function fromString($str) {
      if (FALSE === ($this->_hdl= imagecreatefromstring($url))) {
        throw (new ImagingException('Cannot create image from specified data ['.strlen($str).' bytes]'));
      }
      return TRUE;
    }

    /**
     * Creates a new image in memory from the url specified. Do this if you're
     * not sure about the format
     *
     * @access  public
     * @param   string str
     * @throws  FormatException in case the format is unknown or unsupported
     * @throws  ImagingException in case the url is not valid or the picture cannot be created
     */
    public function fromFile($url) {
      if (FALSE === ($i= getimagesize($url))) {
        throw (new FormatException('Unable to retrieve image information for '.$this->image));
      }
      switch ($i[2]) {
        case 1: return self::fromGif($url);
        case 2: return self::fromJpeg($url);
        case 3: return self::fromPng($url);
      }
      
      throw (new ImagingException('Cannot create images from '.image_type_to_mime_type($i[2])));
    }
    
    /**
     * Creates a new image in memory from an existing URL defining
     * the location of a PNG file
     *
     * @access  public
     * @param   string url
     * @return  bool success
     * @throws  ImagingException in case the url is not valid or the picture cannot be created
     */
    public function fromPng($url) {
      if (FALSE === ($this->_hdl= imagecreatefrompng($url))) {
        throw (new ImagingException('Cannot create image from '.$url));
      }
      return TRUE;
    }
    
    /**
     * Creates a new image in memory from an existing URL defining
     * the location of a GIF file
     *
     * @access  public
     * @param   string url
     * @return  bool success
     * @throws  ImagingException in case the url is not valid or the picture cannot be created
     */
    public function fromGif($url) {
      if (FALSE === ($this->_hdl= imagecreatefromgif($url))) {
        throw (new ImagingException('Cannot create image from '.$url));
      }
      return TRUE;
    }
    
    /**
     * Creates a new image in memory from an existing URL defining
     * the location of a JPEG file
     *
     * @access  public
     * @param   string url
     * @return  bool success
     * @throws  ImagingException in case the url is not valid or the picture cannot be created
     */
    public function fromJpeg($url) {
      if (FALSE === ($this->_hdl= imagecreatefromjpeg($url))) {
        throw (new ImagingException('Cannot create image from '.$url));
      }
      return TRUE;
    }
    
    /**
     * Returns width of image
     *
     * @access  public
     * @return  int width
     */
    public function getWidth() {
      return imagesx($this->_hdl);
    }
    
    /**
     * Returns height of image
     *
     * @access  public
     * @return  int height
     */
    public function getHeight() {
      return imagesy($this->_hdl);
    }
    
    /**
     * Returns dimensions of image
     *
     * @access  public
     * @return  int[] width, height
     */
    public function getDimensions() {
      return array(imagesx($this->_hdl), imagesy($this->_hdl));
    }

    /**
     * Copies an area from another image into this image
     *
     * @access  public
     * @param   &img.Image img Image object
     * @param   int dst_x default 0 x coordinate within this image
     * @param   int dst_y default 0 y coordinate within this image
     * @param   int src_x default 0 x coordinate within the source image
     * @param   int src_y default 0 y coordinate within the source image
     * @param   int src_w default -1 width of the area to copy, -1 defaults to the source image's width
     * @param   int src_h default -1 height of the area to copy, -1 defaults to the source image's height
     * @param   int dst_w default -1 width of the area to copy to, -1 defaults to the source image's width
     * @param   int dst_h default -1 height of the area to copy to, -1 defaults to the source image's height
     */
    public function copyFrom(
      Image $img, 
      $dst_x= 0, 
      $dst_y= 0, 
      $src_x= 0, 
      $src_y= 0, 
      $src_w= -1, 
      $src_h= -1, 
      $dst_w= -1, 
      $dst_h= -1
    ) {
      if (-1 == $src_w) $src_w= $img->getWidth();
      if (-1 == $src_h) $src_h= $img->getHeight();
      if (-1 != $dst_w || -1 != $dst_h) {
        imagecopyresized(
          $this->_hdl, 
          $img->_hdl, 
          $dst_x, 
          $dst_y, 
          $src_x, 
          $src_y, 
          $dst_w,
          $dst_h,
          $src_w, 
          $src_h
        );
      } else {
        imagecopy(
          $this->_hdl, 
          $img->_hdl, 
          $dst_x, 
          $dst_y, 
          $src_x, 
          $src_y, 
          $src_w, 
          $src_h
        );
      }
    }
    
    /**
     * Copies an area from another image into this image
     * The two images will be merged according to pct which can range from 0 to 100. When pct = 0, 
     * no action is taken, when 100 this function behaves identically to copy()
     *
     * @see     xp://img.Image#copyFrom
     * @access  public
     * @param   &img.Image img Image object
     * @param   int pct default 50 percentage of merge
     * @param   int dst_x default 0 x coordinate within this image
     * @param   int dst_y default 0 y coordinate within this image
     * @param   int src_x default 0 x coordinate within the source image
     * @param   int src_y default 0 y coordinate within the source image
     * @param   int src_w default -1 width of the area to copy, -1 defaults to the source image's width
     * @param   int src_h default -1 height of the area to copy, -1 defaults to the source image's height
     */
    public function mergeFrom(
      Image $img, 
      $pct= 50, 
      $dst_x= 0, 
      $dst_y= 0, 
      $src_x= 0, 
      $src_y= 0, 
      $src_w= -1, 
      $src_h= -1
    ) {
      if (-1 == $src_w) $src_w= $img->getWidth();
      if (-1 == $src_h) $src_h= $img->getHeight();
      imagecopymerge(
        $this->_hdl, 
        $img->_hdl, 
        $dst_x, 
        $dst_y, 
        $src_x, 
        $src_y, 
        $src_w, 
        $src_h, 
        $pct
      );
    }
    
    /**
     * Allocate a color
     *
     * @access  public
     * @param   &img.Color color
     * @return  &img.Color color the color put in
     */
    public function allocate(Color $color) {
      $color->_hdl= imagecolorallocate(
        $this->_hdl, 
        $color->red,
        $color->green,
        $color->blue
      );
      $this->palette[$color->_hdl]= $color;
      return $color;
    }
    
    /**
     * Sets a style
     *
     * @see     xp://img.ImgStyle
     * @access  public
     * @param   &img.ImgStyle style
     * @return  &img.ImgStyle the new style object
     * @throws  IllegalArgumentException if style is not an ImgStyle object
     */
    public function setStyle(ImgStyle $style) {
      if (!is_a($style, 'ImgStyle')) {
        throw (new IllegalArgumentException('style parameter is not an ImgStyle object'));
      }
      imagesetstyle($this->_hdl, $style->getPixels());
      return $style;
    }

    /**
     * Sets a brush
     *
     * @see     xp://img.ImgBrush
     * @access  public
     * @param   &img.ImgBrush brush
     * @return  &img.ImgBrush the new style object
     * @throws  IllegalArgumentException if style is not an ImgBrush object
     */
    public function setBrush(ImgBrush $brush) {
      if (!is_a($brush, 'ImgBrush')) {
        throw (new IllegalArgumentException('brush parameter is not an ImgBrush object'));
      }
      if (NULL !== $brush->style) {
        imagesetstyle($this->_hdl, $brush->style->getPixels());
      }
      imagesetbrush($this->_hdl, $brush->image->_hdl);
      return $brush;
    }
    
    /**
     * Get color index by x, y
     *
     * @access  public
     * @param   int x
     * @param   int y
     * @return  &img.Color color object
     */
    public function colorAt($x, $y) {
      return $this->palette[imagecolorat($this->_hdl, $x, $y)];
    }
    
    /**
     * Apply gamma correction to this image
     *
     * @access  public
     * @param   float in
     * @param   float out
     * @return  bool success
     */
    public function correctGamma($in, $out) {
      return imagegammacorrect($this->_hdl, $in, $out);
    }
    
    /**
     * Fills the image with a specified color at the coordinates
     * defined by x and y
     *
     * @access  public
     * @param   &mixed col (either an img.Color[] consisting of the flood color and the 
     *          border color) or a simple img.Color defining the flood color
     * @param   int x default 0
     * @param   int y default 0
     * @see     php://imagefill
     * @see     php://imagefilltoborder
     */
    public function fill($col, $x= 0, $y= 0) {
      if (is_array($col)) {
        imagefilltoborder($this->_hdl, $x, $y, $col[1]->_hdl, $col[0]->_hdl);
      } else {
        imagefill($this->_hdl, $x, $y, $col->_hdl);
      }
    }
    
    /**
     * Sets interlacing on or off.
     *
     * If the interlace bit is set and the image is used as a JPEG image, the image 
     * is created as a progressive JPEG. 
     *
     * @access  public
     * @param   bool on interlace on (TRUE) or off (FALSE)
     * @return  bool success
     */
    public function setInterlace($on) {
      return imageinterlace($this->_hdl, $on);
    }
    
    /**
     * Define a color as transparent
     *
     * The transparent color is a property of the image, transparency is not a 
     * property of the color. Once you have a set a color to be the transparent 
     * color, any regions of the image in that color that were drawn previously 
     * will be transparent. 
     *
     * @access  public
     * @param   &img.Color color
     */
    public function setTransparency($col) {
      imagecolortransparent($this->_hdl, $col->_hdl);
    }
    
    /**
     * Retrieve the color which is defined as transparent
     *
     * @access  public
     * @return  &img.Color color
     */
    public function getTransparency() {
      return $this->palette[imagecolortransparent($this->_hdl)];
    }
    
    /**
     * Draws an object
     *
     * @access  public
     * @param   img.DrawableObject obj
     * @return  mixed the return value of obj's draw function
     */
    public function draw(DrawableObject $obj) {
      return $obj->draw($this->_hdl);
    }
    
    /**
     * Private function which produces the image. Overwrite this in
     * extended classes
     *
     * @see     xp://img.JpegImage
     * @access  private
     * @param   string filename default '' filename to save to or '' for memory
     * @return  bool success
     */
    private function _out($filename= '') {
      return imagegd($this->_hdl, $filename);
    }
    
    /**
     * Retrieve image data as a string
     *
     * @access  public
     * @return  string image date
     * @throws  ImagingException if an error occurs
     */
    public function toString() {
      ob_start();
      if (FALSE === self::_out()) {
        ob_end_clean();
        throw (new ImagingException('Could not create image'));
      }
      $str= ob_get_contents();
      ob_end_clean();
      return $str;
    }
  
    /**
     * Retrieve image data as a file
     *
     * @access  public
     * @return  io.File file object
     * @throws  ImagingException if an error occurs
     * @throws  IllegalArgumentException if parameter is not a file object
     */  
    public function toFile($file) {
      if (!is_a($file, 'File')) {
        throw (new IllegalArgumentException('given file is not a file object'));
      }
      if (FALSE === self::_out($file->uri)) {
        throw (new ImagingException('Could not create image'));
      }
      return $file;
    }
  }
?>
