<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('img.ImagingException');

  /**
   * Filter interface
   *
   * @see      xp://img.Image#apply
   * @purpose  Interface
   */
  class ImageFilter extends Interface {
  
    /**
     * Apply this filter on a given image. Note: This changes the given image!
     *
     * @access  public
     * @param   &img.Image image
     * @return  bool
     * @throws  img.ImagingException
     */
    function applyOn(&$image) { }
  }
?>
