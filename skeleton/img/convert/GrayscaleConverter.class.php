<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('img.convert.ImageConverter');

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
   *   $img= Image::loadFrom(new JpegStreamReader(new File('colored.jpg')));
   *   $img->convertTo(new GrayscaleConverter());
   *   $img->saveTo(new JpegStreamWriter(new File('gray.jpg')));
   * </code>
   *
   * @ext      gd
   * @see      xp://img.convert.ImageConverter
   * @purpose  Converter
   */
  class GrayscaleConverter extends Object implements ImageConverter {

    /**
     * Convert an image.
     *
     * @param   img.Image image
     * @return  bool
     * @throws  img.ImagingException
     */
    public function convert($image) {
    
      // Create temporary variable as local variable access is faster 
      // than member variable access.
      $handle= $image->handle;
      
      if (imageistruecolor($handle)) {
        $l= array();
        $h= $image->getHeight();
        $w= $image->getWidth();
        for ($y= 0; $y < $h; $y++) {
          for ($x= 0; $x < $w; $x++) {
            $rgb= imagecolorat($handle, $x, $y);
            if (!isset($l[$rgb])) {
              $g= (
                .299 * (($rgb >> 16) & 0xFF) + 
                .587 * (($rgb >> 8) & 0xFF) +
                .114 * ($rgb & 0xFF)
              );
              $l[$rgb]= imagecolorallocate($handle, $g, $g, $g);
            }
            imagesetpixel($handle, $x, $y, $l[$rgb]);
          }
        }
        unset($l);    
      } else {
        for ($i= 0, $t= imagecolorstotal($handle); $i < $t; $i++) {   
          $c= imagecolorsforindex($handle, $i);
          $g= .299 * $c['red'] + .587 * $c['green'] + .114 * $c['blue'];
          imagecolorset($handle, $i, $g, $g, $g);
        }
      }
    }
  } 
?>
