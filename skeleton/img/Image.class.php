<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'img.ImagingException',
    'img.Color',
    'lang.CloneNotSupportedException'
  );

  define('IMG_PALETTE',   0x0000);
  define('IMG_TRUECOLOR', 0x0001);

  /**
   * Base class for images
   *
   * Usage example: Creating an empty image:
   * <code>
   *   $palette_image= Image::create(640, 480);
   *   $truecolor_image= Image::create(640, 480, IMG_TRUECOLOR);
   * </code>
   *
   * Usage example: Loading an image from a file:
   * <code>
   *   $image= Image::loadFrom(new JpegStreamReader(new File('picture.jpg')));
   * </code>
   *
   * @ext gd
   * @see php://image
   */
  class Image extends Object {
    public
      $width    = 0,
      $height   = 0,
      $palette  = array(),
      $handle   = NULL;

    /**
     * Constructor
     *
     * @param   resource handle
     */
    protected function __construct($handle) {
      $this->handle= $handle;
      $this->width= imagesx($handle);
      $this->height= imagesy($handle);
    }

    /**
     * Destructor
     *
     */
    public function __destruct() {
      if (is_resource($this->handle)) imagedestroy($this->handle);
    }

    /**
     * Clone method
     *
     */
    public function __clone() {
      if (!is_resource($handle= (imageistruecolor($this->handle)
        ? imagecreatetruecolor($this->width, $this->height)
        : imagecreate($this->width, $this->height)
      ))) {
        throw new CloneNotSupportedException('Could not clone the image');
      }
      imagecopy($handle, $this->handle, 0, 0, 0, 0, $this->width, $this->height);
      $this->handle= $handle;
    }

    /**
     * Creates a new blank image in memory
     *
     * @param   int w width
     * @param   int h height
     * @param   int type default IMG_PALETTE either IMG_PALETTE or IMG_TRUECOLOR
     * @param   string class default __CLASS__ class to create, defaulting to "Image"
     * @return  img.Image
     * @throws  img.ImagingException in case the image could not be created
     */
    public static function create($w, $h, $type= IMG_PALETTE, $class= __CLASS__) {
      switch ($type) {
        case IMG_PALETTE:
          $handle= imagecreate($w, $h);
          break;

        case IMG_TRUECOLOR:
          $handle= imagecreatetruecolor($w, $h);
          break;

        default:
          throw new ImagingException('Unknown type '.$type);
      }

      if (!is_resource($handle)) {
        throw new ImagingException('Could not create image of type '.$type);
      }
      return new $class($handle);
    }

    /**
     * Loads an image from a reader
     *
     * @param   img.io.ImageReader reader
     * @return  img.Image
     */
    public static function loadFrom($reader) {
      return new Image($reader->getResource());
    }

    /**
     * Saves an image to a writer
     *
     * @param   img.io.ImageWriter writer
     */
    public function saveTo($writer) {
      $writer->setResource($this->handle);
    }

    /**
     * Convert this image using a given converter
     *
     * @param   img.convert.ImageConverter converter
     * @return  bool
     * @throws  lang.IllegalArgumentException if converter is not a img.convert.ImageConverter
     */
    public function convertTo(ImageConverter $converter) {
      return $converter->convert($this);
    }
    
    /**
     * Apply a given filter to this image
     *
     * @param   img.filter.ImageFilter filter
     * @return  bool
     * @throws  lang.IllegalArgumentException if filter is not a img.filter.ImageFilter
     */
    public function apply(ImageFilter $filter) {
      return $filter->applyOn($this);
    }

    /**
     * Returns width of image
     *
     * @return  int width
     */
    public function getWidth() {
      return $this->width;
    }

    /**
     * Returns height of image
     *
     * @return  int height
     */
    public function getHeight() {
      return $this->height;
    }

    /**
     * Returns dimensions of image
     *
     * @return  int[2] width, height
     */
    public function getDimensions() {
      return array($this->width, $this->height);
    }

    /**
     * Copies an area from another image into this image
     *
     * @param   img.Image img Image object
     * @param   int dst_x default 0 x coordinate within this image
     * @param   int dst_y default 0 y coordinate within this image
     * @param   int src_x default 0 x coordinate within the source image
     * @param   int src_y default 0 y coordinate within the source image
     * @param   int src_w default -1 width of the area to copy, -1 defaults to the source image's width
     * @param   int src_h default -1 height of the area to copy, -1 defaults to the source image's height
     * @return  bool
     */
    public function copyFrom(
      $img,
      $dst_x= 0,
      $dst_y= 0,
      $src_x= 0,
      $src_y= 0,
      $src_w= -1,
      $src_h= -1
    ) {
      return imagecopy(
        $this->handle,
        $img->handle,
        $dst_x,
        $dst_y,
        $src_x,
        $src_y,
        ($src_w < 0) ? $this->width : $src_w,
        ($src_h < 0) ? $this->height : $src_h
      );
    }

    /**
     * Copies an area from another image into this image, resizing it if necessary
     *
     * @see     php://imagecopyresized
     * @param   img.Image img Image object
     * @param   int dst_x default 0 x coordinate within this image
     * @param   int dst_y default 0 y coordinate within this image
     * @param   int src_x default 0 x coordinate within the source image
     * @param   int src_y default 0 y coordinate within the source image
     * @param   int dst_w default -1 width of the area to copy to, -1 defaults to this image's width
     * @param   int dst_h default -1 height of the area to copy to, -1 defaults to this image's height
     * @param   int src_w default -1 width of the area to copy, -1 defaults to the source image's width
     * @param   int src_h default -1 height of the area to copy, -1 defaults to the source image's height
     * @return  bool
     */
    public function resizeFrom(
      $img,
      $dst_x= 0,
      $dst_y= 0,
      $src_x= 0,
      $src_y= 0,
      $dst_w= -1,
      $dst_h= -1,
      $src_w= -1,
      $src_h= -1
    ) {
      return imagecopyresized(
        $this->handle,
        $img->handle,
        $dst_x,
        $dst_y,
        $src_x,
        $src_y,
        ($dst_w < 0) ? $this->width : $dst_w,
        ($dst_h < 0) ? $this->height : $dst_h,
        ($src_w < 0) ? $img->width : $src_w,
        ($src_h < 0) ? $img->height : $src_h
      );
    }

    /**
     * Copies an area from another image into this image, resizing it if necessary.
     *
     * @see     php://imagecopyresampled
     * @param   img.Image img Image object
     * @param   int dst_x default 0 x coordinate within this image
     * @param   int dst_y default 0 y coordinate within this image
     * @param   int src_x default 0 x coordinate within the source image
     * @param   int src_y default 0 y coordinate within the source image
     * @param   int dst_w default -1 width of the area to copy to, -1 defaults to this image's width
     * @param   int dst_h default -1 height of the area to copy to, -1 defaults to this image's height
     * @param   int src_w default -1 width of the area to copy, -1 defaults to the source image's width
     * @param   int src_h default -1 height of the area to copy, -1 defaults to the source image's height
     * @return  bool
     */
    public function resampleFrom(
      $img,
      $dst_x= 0,
      $dst_y= 0,
      $src_x= 0,
      $src_y= 0,
      $dst_w= -1,
      $dst_h= -1,
      $src_w= -1,
      $src_h= -1
    ) {
      return imagecopyresampled(
        $this->handle,
        $img->handle,
        $dst_x,
        $dst_y,
        $src_x,
        $src_y,
        ($dst_w < 0) ? $this->width : $dst_w,
        ($dst_h < 0) ? $this->height : $dst_h,
        ($src_w < 0) ? $img->width : $src_w,
        ($src_h < 0) ? $img->height : $src_h
      );
    }

    /**
     * Copies an area from another image into this image
     * The two images will be merged according to pct which can range from 0 to 100. When pct = 0,
     * no action is taken, when 100 this function behaves identically to copy()
     *
     * @see     xp://img.Image#copyFrom
     * @param   img.Image img Image object
     * @param   int pct default 50 percentage of merge
     * @param   int dst_x default 0 x coordinate within this image
     * @param   int dst_y default 0 y coordinate within this image
     * @param   int src_x default 0 x coordinate within the source image
     * @param   int src_y default 0 y coordinate within the source image
     * @param   int src_w default -1 width of the area to copy, -1 defaults to the source image's width
     * @param   int src_h default -1 height of the area to copy, -1 defaults to the source image's height
     * @return  bool
     */
    public function mergeFrom(
      $img,
      $pct= 50,
      $dst_x= 0,
      $dst_y= 0,
      $src_x= 0,
      $src_y= 0,
      $src_w= -1,
      $src_h= -1
    ) {
      return imagecopymerge(
        $this->handle,
        $img->handle,
        $dst_x,
        $dst_y,
        $src_x,
        $src_y,
        ($src_w < 0) ? $img->width : $src_w,
        ($src_h < 0) ? $img->height : $src_h,
        $pct
      );
    }

    /**
     * Allocate a color
     *
     * @param   img.Color color
     * @param   int alpha default -1 alpha value (0= opaque - 127= transparent)
     * @return  img.Color color the color put in
     */
    public function allocate($color, $alpha= -1) {
      if ($alpha > -1) {
        $color->handle= imagecolorallocatealpha(
          $this->handle,
          $color->red,
          $color->green,
          $color->blue,
          $alpha
        );
      } else {
        $color->handle= imagecolorallocate(
          $this->handle,
          $color->red,
          $color->green,
          $color->blue
        );
      }
      $this->palette[$color->handle]= $color;
      return $color;
    }

    /**
     * Sets a style
     *
     * @see     xp://img.ImgStyle
     * @param   img.ImgStyle style
     * @return  img.ImgStyle the new style object
     * @throws  lang.IllegalArgumentException if style is not an ImgStyle object
     */
    public function setStyle(ImgStyle $style) {
      imagesetstyle($this->handle, $style->getPixels());
      return $style;
    }

    /**
     * Sets a brush
     *
     * @see     xp://img.ImgBrush
     * @param   img.ImgBrush brush
     * @return  img.ImgBrush the new style object
     * @throws  lang.IllegalArgumentException if style is not an ImgBrush object
     */
    public function setBrush(ImgBrush $brush) {
      if (NULL !== $brush->style) {
        imagesetstyle($this->handle, $brush->style->getPixels());
      }
      imagesetbrush($this->handle, $brush->image->handle);
      return $brush;
    }

    /**
     * Get color index by x, y
     *
     * @param   int x
     * @param   int y
     * @return  img.Color color object
     */
    public function colorAt($x, $y) {
      if (FALSE === ($idx= imagecolorat($this->handle, $x, $y))) return NULL;

      // See if we have this in our palette
      if (!isset($this->palette[$idx])) {
        if (imageistruecolor($this->handle)) {
          $this->palette[$idx]= new Color(
            ($idx >> 16) & 0xFF,
            ($idx >> 8) & 0xFF,
            $idx & 0xFF
          );
        } else {
          $i= imagecolorsforindex($this->handle, $idx);
          $this->palette[$idx]= new Color(
            $i['red'],
            $i['green'],
            $i['blue']
          );
        }
      }
      return $this->palette[$idx];
    }

    /**
     * Apply gamma correction to this image
     *
     * @param   float in
     * @param   float out
     * @return  bool success
     */
    public function correctGamma($in, $out) {
      return imagegammacorrect($this->handle, $in, $out);
    }

    /**
     * Fills the image with a specified color at the coordinates
     * defined by x and y
     *
     * @param   mixed col (either an img.Color[] consisting of the flood color and the
     *          border color) or a simple img.Color defining the flood color
     * @param   int x default 0
     * @param   int y default 0
     * @see     php://imagefill
     * @see     php://imagefilltoborder
     */
    public function fill($col, $x= 0, $y= 0) {
      if (is_array($col)) {
        imagefilltoborder($this->handle, $x, $y, $col[1]->handle, $col[0]->handle);
      } else {
        imagefill($this->handle, $x, $y, $col->handle);
      }
    }

    /**
     * Sets interlacing on or off.
     *
     * If the interlace bit is set and the image is used as a JPEG image, the image
     * is created as a progressive JPEG.
     *
     * @param   bool on interlace on (TRUE) or off (FALSE)
     * @return  bool success
     */
    public function setInterlace($on) {
      return imageinterlace($this->handle, $on);
    }

    /**
     * Define a color as transparent
     *
     * The transparent color is a property of the image, transparency is not a
     * property of the color. Once you have a set a color to be the transparent
     * color, any regions of the image in that color that were drawn previously
     * will be transparent.
     *
     * @param   img.Color color
     */
    public function setTransparency($col) {
      imagecolortransparent($this->handle, $col->handle);
    }

    /**
     * Retrieve the color which is defined as transparent
     *
     * @return  img.Color color
     */
    public function getTransparency() {
      if (-1 == ($t= imagecolortransparent($this->handle))) return NULL;
      return $this->palette[$t];
    }

    /**
     * Draws a drawable object onto this image
     *
     * @param   img.Drawable obj
     * @return  mixed the return value of obj's draw function
     */
    public function draw($drawable) {
      return $drawable->draw($this);
    }

    /**
     * Returns a hashcode for this connection
     *
     * Example:
     * <pre>
     *   gd #38
     * </pre>
     *
     * @return  string
     */
    public function hashCode() {
      return get_resource_type($this->handle).' #'.(int)$this->handle;
    }

    /**
     * Retrieve string representation
     *
     * @return  string
     */
    public function toString() {
      return sprintf(
        "%s(%dx%d) {\n  #colors = %s\n}",
        $this->getClassName(),
        $this->width,
        $this->height,
        imageistruecolor($this->handle) ? '(truecolor)' : imagecolorstotal($this->handle)
      );
    }
  }
?>
