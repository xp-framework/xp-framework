<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

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
