<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  /**
   * Brush class
   *
   * @see xp://img.Image#setBrush
   */
  class ImgBrush extends Object {
    public
      $image    = NULL,
      $style    = NULL;
      
    public
      $handle     = IMG_COLOR_STYLEDBRUSHED;
    
    /**
     * Constructor
     *
     * @param   img.Image an image object
     * @param   img.ImgStyle a style object
     */
    public function __construct($i, $s) {
      $this->image= $i;
      $this->style= $s;
      
    }
  }
?>
