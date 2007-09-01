<?php
/* This class is part of the XP framework
 *
 * $Id: ImageFilter.class.php 10594 2007-06-11 10:04:54Z friebe $
 */

  namespace img::filter;

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
