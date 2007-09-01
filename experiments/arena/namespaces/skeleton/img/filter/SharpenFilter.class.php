<?php
/* This class is part of the XP framework
 *
 * $Id: SharpenFilter.class.php 10594 2007-06-11 10:04:54Z friebe $ 
 */

  namespace img::filter;

  uses('img.filter.ImageFilter');

  /**
   * A filter which sharpens an image
   *
   * @purpose  Filter implementation
   */
  class SharpenFilter extends lang::Object implements ImageFilter {
    
    /**
     * Apply this filter on a given image. Note: This changes the given image!
     *
     * @param   img.Image image
     * @return  bool
     * @throws  img.ImagingException
     */
    public function applyOn($image) {
      $clone= clone $image;

      // Create local variables for faster access
      $chandle= $clone->handle;
      $ihandle= $image->handle;      
      $w= $image->getWidth() - 1;
      $h= $image->getHeight() - 1;
      
      for ($y= 1; $y < $h; ++$y) {
        $rgb_y0= imagecolorat($chandle, 0, $y);
        
        $rd= ($rgb_y0 >> 0x10);
        $gd= ($rgb_y0 >> 0x08 & 0xFF);
        $bd= ($rgb_y0 & 0xFF);

        for ($yd= $y - 1, $yi= $y + 1, $x= 1; $x < $w; ++$x) {
          $rgb_xy= imagecolorat($chandle, $x, $y);
          $rgb_xyi= imagecolorat($chandle, $x, $yi);
          $rgb_xiy= imagecolorat($chandle, $x+ 1, $y);
          $rgb_xyd= imagecolorat($chandle, $x, $yd);
          
          $nr= -(($rgb_xyd >> 0x10) + ($rgb_xyi >> 0x10) + $rd + ($rgb_xiy >> 0x10)) / 4;
          $ng= -(($rgb_xyd >> 0x08 & 0xFF) + ($rgb_xyi >> 0x08 & 0xFF) + $gd + ($rgb_xiy >> 0x08 & 0xFF)) / 4;
          $nb= -(($rgb_xyd & 0xFF) + ($rgb_xyi & 0xFF) + $bd + ($rgb_xiy & 0xFF)) / 4;

          $nr+= 2 * ($rd= ($rgb_xy >> 0x10));
          $ng+= 2 * ($gd= ($rgb_xy >> 0x08 & 0xFF));
          $nb+= 2 * ($bd= ($rgb_xy & 0xFF));

          // Normalize          
          $nr= ($nr > 255.0) ? 255.0 : ($nr < 0.0 ? 0.0 : $nr);
          $ng= ($ng > 255.0) ? 255.0 : ($ng < 0.0 ? 0.0 : $ng);
          $nb= ($nb > 255.0) ? 255.0 : ($nb < 0.0 ? 0.0 : $nb);

          imagesetpixel($ihandle, $x, $y, $nr << 0x10 | $ng << 0x08 | $nb);
        }       
      }
      
      delete($clone);
    }
  
  } 
?>
