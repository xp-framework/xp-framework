<?php
/* This class is part of the XP framework
 *
 * $Id: ImgStyle.class.php 8971 2006-12-27 15:27:10Z friebe $
 */

  namespace img;
 
  /**
   * Style class
   *
   * @see   xp://img.Image#setStyle
   */
  class ImgStyle extends lang::Object {
    public
      $colors   = array(),
      $pixels   = array();
      
    public
      $handle     = IMG_COLOR_STYLED;
    
    /**
     * Constructor
     *
     * @param   img.Color[] colors an array of pixels
     */
    public function __construct($colors) {
      $this->colors= $colors;
      for ($i= 0, $s= sizeof($this->colors); $i < $s; $i++) {
        $this->pixels[]= $this->colors[$i]->handle;
      }

      
    }
    
    /**
     * Retrieves the style array as used for the second argument
     * int imagesetstyle()
     *
     * @return  int[] array of color indices
     * @see     php://imagesetstyle
     */
    public function getPixels() {
      return $this->pixels;
    }
  }
?>
