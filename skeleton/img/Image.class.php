<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses('img.ImagingException');
  
  /**
   * Base class for images
   *
   * Usage example: Creating an empty image:
   * <code>
   *   $palette_image= &Image::create(640, 480);
   *   $truecolor_image= &Image::create(640, 480, TRUE);
   * </code>
   *
   * Usage example: Loading an image from a file:
   * <code>
   *   $image= &Image::loadFrom(new JpegStreamReader(new File('picture.jpg')));
   * </code>
   *
   * @ext gd
   * @see php://image
   */
  class Image extends Object {
    var 
      $width    = 0,
      $height   = 0,
      $palette  = array();
    
    var
      $_hdl     = NULL;
    
    /**
     * Constructor
     *
     * @access  protected
     * @param   int width default 
     * @param   int height
     */
    function __construct($handle) {
      $this->_hdl= $handle;
      $this->width= imagesx($handle);
      $this->height= imagesy($handle);
    }
    
    /**
     * Destructor
     *
     * @access  public
     */
    function __destruct() {
      if (is_resource($this->_hdl)) imagedestroy($this->_hdl);
      parent::__destruct();
    }
    
    /**
     * Creates a new blank image in memory
     *
     * @model   static
     * @access  public
     * @param   int w width
     * @param   int h height
     * @param   bool truecolor default FALSE
     */
    function &create($w, $h, $truecolor= FALSE) {
      if ($truecolor) {
        return new Image(imagecreatetruecolor($w, $h));
      } else {
        return new Image(imagecreate($w, $h));
      }
    }

    /**
     * Loads an image from a reader
     *
     * @model   static
     * @access  public
     * @param   &img.io.ImageReader
     * @return  &img.Image
     */
    function &loadFrom(&$reader) {
      return new Image($reader->getResource());
    }
    
    /**
     * Saves an image to a reader
     *
     * @model   static
     * @access  public
     * @param   &img.io.ImageReader
     * @return  &img.Image
     */
    function &saveTo(&$writer) {
      $writer->setResource($this->_hdl);
    }

    /**
     * Returns width of image
     *
     * @access  public
     * @return  int width
     */
    function getWidth() {
      return imagesx($this->_hdl);
    }
    
    /**
     * Returns height of image
     *
     * @access  public
     * @return  int height
     */
    function getHeight() {
      return imagesy($this->_hdl);
    }
    
    /**
     * Returns dimensions of image
     *
     * @access  public
     * @return  int[] width, height
     */
    function getDimensions() {
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
    function copyFrom(
      &$img, 
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
    function mergeFrom(
      &$img, 
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
    function &allocate(&$color) {
      $color->_hdl= imagecolorallocate(
        $this->_hdl, 
        $color->red,
        $color->green,
        $color->blue
      );
      $this->palette[$color->_hdl]= &$color;
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
    function &setStyle(&$style) {
      if (!is_a($style, 'ImgStyle')) {
        return throw(new IllegalArgumentException('style parameter is not an ImgStyle object'));
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
    function &setBrush(&$brush) {
      if (!is_a($brush, 'ImgBrush')) {
        return throw(new IllegalArgumentException('brush parameter is not an ImgBrush object'));
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
    function &colorAt($x, $y) {
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
    function correctGamma($in, $out) {
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
    function fill(&$col, $x= 0, $y= 0) {
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
    function setInterlace($on) {
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
    function setTransparency(&$col) {
      imagecolortransparent($this->_hdl, $col->_hdl);
    }
    
    /**
     * Retrieve the color which is defined as transparent
     *
     * @access  public
     * @return  &img.Color color
     */
    function &getTransparency() {
      return $this->palette[imagecolortransparent($this->_hdl)];
    }
    
    /**
     * Draws an object
     *
     * @access  public
     * @param   img.DrawableObject obj
     * @return  mixed the return value of obj's draw function
     */
    function draw(&$obj) {
      return $obj->draw($this->_hdl);
    }
    
    /**
     * Returns a hashcode for this connection
     *
     * Example:
     * <pre>
     *   gd #38
     * </pre>
     *
     * @access  public
     * @return  string
     */
    function hashCode() {
      return get_resource_type($this->_hdl).' #'.(int)$this->_hdl;
    }
    
    /**
     * Retrieve string representation
     *
     * @access  public
     * @return  string
     */
    function toString() {
      return sprintf(
        '%s(%dx%d)',
        $this->getClassName(),
        $this->width,
        $this->height
      );
    }
  }
?>
