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
    var
      $image    = NULL,
      $style    = NULL;
      
    var
      $_hdl     = IMG_COLOR_STYLEDBRUSHED;
    
    /**
     * Constructor
     *
     * @access  public
     * @param   img.Image an image object
     * @param   img.ImgStyle a style object
     */
    function __construct(&$i, &$s) {
      $this->image= &$i;
      $this->style= &$s;
      
    }
  }
?>
