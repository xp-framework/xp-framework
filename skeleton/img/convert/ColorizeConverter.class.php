<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('img.Image');

  /**
   * Converts an images colors
   * 
   * @ext      gd
   * @see      xp://img.convert.ImageConverter
   * @purpose  Converter
   */
  class ColorizeConverter extends Object {
    var
      $redFactor    = 0.0, 
      $greenFactor  = 0.0, 
      $blueFactor   = 0.0;
      
    /**
     * Constructor
     *
     * @access  public
     * @param   float redFactor
     * @param   float greenFactor
     * @param   float blueFactor
     */
    function __construct($redFactor, $greenFactor, $blueFactor) {
      $this->redFactor= $redFactor;
      $this->greenFactor= $greenFactor;
      $this->blueFactor= $blueFactor;
    }
      
    /**
     * Convert an image.
     *
     * @access  public
     * @param   &img.Image image
     * @return  bool
     * @throws  img.ImagingException
     */
    function convert(&$image) {
    
      // Create temporary variables as local variable access is faster 
      // than member variable access.
      $rf= $this->redFactor;
      $gf= $this->greenFactor;
      $bf= $this->blueFactor;
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
                $rf * (($rgb >> 16) & 0xFF) + 
                $gf * (($rgb >> 8) & 0xFF) +
                $bf * ($rgb & 0xFF)
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
          $g= $rf * $c['red'] + $gf * $c['green'] + $bf * $c['blue'];
          imagecolorset($handle, $i, $g, $g, $g);
        }
      }
    }

  } implements(__FILE__, 'img.convert.ImageConverter');
?>
