<?php
/* This class is part of the XP framework
 *
 * $Id: ImageConverter.class.php 10594 2007-06-11 10:04:54Z friebe $ 
 */

  namespace img::convert;

  uses('img.ImagingException');

  /**
   * Converter interface
   *
   * @see      xp://img.Image#convertTo
   * @purpose  Interface
   */
  interface ImageConverter {
  
    /**
     * Convert an image. Note: This changes the given image!
     *
     * @param   img.Image image
     * @return  bool
     * @throws  img.ImagingException
     */
    public function convert($image);
  }
?>
