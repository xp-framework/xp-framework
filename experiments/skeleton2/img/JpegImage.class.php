<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('img.Image');

  /**
   * Class wrapper for JPEG images
   *
   * @see xp://img.Image
   */
  class JpegImage extends Image {
    public
      $quality= 75.0;
    
    /**
     * Private function which produces the image
     *
     * @see     xp://img.Image#_out
     */
    protected function _out($filename= '') {
      return imagejpeg($this->_hdl, $filename, $this->quality);
    }
  }
?>
