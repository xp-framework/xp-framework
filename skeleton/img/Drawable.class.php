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
  class Drawable extends Interface {
  
    /**
     * Draws this object onto an image
     *
     * @access  public
     * @param   &img.Image image
     * @return  mixed
     */
    function draw(&$image) { }
  
  }
?>
