<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Converter interface
   *
   * @see      xp://img.Image#convertTo
   * @purpose  Interface
   */
  class ImageConverter extends Interface {
  
    /**
     * Convert an image. Note: This changes the given image!
     *
     * @access  public
     * @param   &img.Image image
     * @return  bool
     */
    function convert(&$image) { }
  }
?>
