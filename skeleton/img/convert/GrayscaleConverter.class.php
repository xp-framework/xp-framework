<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('img.convert.ColorizeConverter');

  /**
   * Converts an image to grayscale. 
   * 
   * Hint: If you don't care about losing truecolor characteristics,
   * convert the image to a paletted image first - this converter 
   * will run a lot faster then!
   *
   * Example:
   * <code>
   *   uses(
   *     'io.File',
   *     'img.convert.GrayscaleConverter',
   *     'img.io.JpegStreamReader',
   *     'img.io.JpegStreamWriter'
   *   );
   *   
   *   $img= &Image::loadFrom(new JpegStreamReader(new File('colored.jpg')));
   *   $img->convertTo(new GrayscaleConverter());
   *   $img->saveTo(new JpegStreamWriter(new File('gray.jpg')));
   * </code>
   *
   * @ext      gd
   * @see      xp://img.convert.ImageConverter
   * @purpose  Converter
   */
  class GrayscaleConverter extends ColorizeConverter {

    /**
     * Constructor
     *
     * @access  public
     */
    function __construct() {
      parent::__construct(.299, .587, .114);
    }
  }
?>
