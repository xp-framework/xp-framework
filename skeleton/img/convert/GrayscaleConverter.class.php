<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('img.Image');

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
  class GrayscaleConverter extends Object {
  
    /**
     * Convert an image.
     *
     * @access  public
     * @param   &img.Image image
     * @return  bool
     * @throws  img.ImagingException
     */
    function convert(&$image) {
      if (imageistruecolor($image->handle)) {
        $l= array();
        for ($y= 0, $h= $image->getHeight(); $y < $h; $y++) {
          for ($x= 0, $w= $image->getWidth(); $x < $w; $x++) {
            $rgb= imagecolorat($image->handle, $x, $y);
            if (!isset($l[$rgb])) {
              $g= (
                .299 * (($rgb >> 16) & 0xFF) + 
                .587 * (($rgb >> 8) & 0xFF) +
                .114 * ($rgb & 0xFF)
              );
              $l[$rgb]= imagecolorallocate($image->handle, $g, $g, $g);
            }
            imagesetpixel($image->handle, $x, $y, $l[$rgb]);
          }
        }
        unset($l);    
      } else {
        for ($i= 0, $t= imagecolorstotal($image->handle); $i < $t; $i++) {   
          $c= imagecolorsforindex($image->handle, $i);
          $g= .299 * $c['red'] + .587 * $c['green']+ .114 * $c['blue'];
          imagecolorset($image->handle, $i, $g, $g, $g);
        }
      }
    }

  } implements(__FILE__, 'img.convert.ImageConverter');
?>
