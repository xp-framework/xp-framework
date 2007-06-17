<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('img.filter.Kernel', 'img.filter.ImageFilter');

  /**
   * A filter which applies a convolution kernel to an image. 
   *
   * @see      http://www.eas.asu.edu/~karam/2dconvolution/
   * @see      php://imageconvolution  
   * @purpose  Filter implementation
   */
  class ConvolveFilter extends Object implements ImageFilter {
    public
      $kernel   = NULL,
      $divisor  = 0.0,
      $offset   = 0.0;
    
    /**
     * Constructor
     *
     * @param   img.filter.Kernel kernel
     * @param   float divisor
     * @param   float offset
     */
    public function __construct($kernel, $divisor, $offset) {
      $this->kernel= $kernel;
      $this->divisor= $divisor;
      $this->offset= $offset;
    }
    
    /**
     * Apply this filter on a given image. Note: This changes the given image!
     *
     * @param   img.Image image
     * @return  bool
     * @throws  img.ImagingException
     */
    public function applyOn($image) { 

      // Use builtin function (exists as of 5.1.0)
      if (function_exists('imageconvolution')) return imageconvolution(
        $image->handle, 
        $this->kernel->getMatrix(), 
        $this->divisor, 
        $this->offset
      );

      $clone= clone $image;

      // Create local variables for faster access
      $chandle= $clone->handle;
      $ihandle= $image->handle;
      $matrix= $this->kernel->getMatrix();
      $divisor= $this->divisor;
      $offset= $this->offset;
      $w= $image->getWidth();
      $h= $image->getHeight();
      
      for ($y= 0; $y < $h; $y++) {
        for ($x= 0; $x < $w; $x++) {
          $nr= $ng= $nb= 0;

          // Apply matrix
          for ($j= 0; $j < 3; $j++) {
            $max= $y - 1 + $j;
            $min= $max < 0 ? 0 : $max;
            $yv= $min >= $h ? $h - 1 : $min;
            
            for ($i= 0; $i < 3; $i++) {
              $max= $x - 1 + $i;
              $min= $max < 0 ? 0 : $max;
              $xv= $min >= $w ? $w - 1 : $min;
              
              $rgb= imagecolorat($chandle, $xv, $yv);

              $m= $matrix[$j][$i];
              $nr+= (($rgb >> 16) & 0xFF) * $m;
              $ng+= (($rgb >> 8) & 0xFF) * $m;
              $nb+= ($rgb & 0xFF) * $m;
            }
          }
          
          // Apply divisor and offset
          $nr= ($nr / $divisor) + $offset;
          $ng= ($ng / $divisor) + $offset;
          $nb= ($nb / $divisor) + $offset;
          
          // Normalize
          $nr= ($nr > 255.0) ? 255.0 : ($nr < 0.0 ? 0.0 : $nr);
          $ng= ($ng > 255.0) ? 255.0 : ($ng < 0.0 ? 0.0 : $ng);
          $nb= ($nb > 255.0) ? 255.0 : ($nb < 0.0 ? 0.0 : $nb);
          
          imagesetpixel(
            $ihandle, 
            $x, 
            $y, 
            imagecolorallocate($ihandle, $nr, $ng, $nb)
          );
        }
      }
      
      delete($clone);
    }
  
  } 
?>
