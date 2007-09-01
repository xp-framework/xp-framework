<?php
/* This class is part of the XP framework
 *
 * $Id: Drawable.class.php 10594 2007-06-11 10:04:54Z friebe $ 
 */

  namespace img;

  /**
   * Denotes a drawable object
   *
   * @see      xp://img.Image#draw
   * @purpose  Interface
   */
  interface Drawable {
  
    /**
     * Draws this object onto an image
     *
     * @param   img.Image image
     * @return  mixed
     */
    public function draw($image);
  
  }
?>
