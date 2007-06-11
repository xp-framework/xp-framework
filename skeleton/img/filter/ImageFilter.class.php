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
  interface ImageFilter {
  
    /**
     * Apply this filter on a given image. Note: This changes the given image!
     *
     * @param   img.Image image
     * @return  bool
     * @throws  img.ImagingException
     */
    public function applyOn($image);
  }
?>
