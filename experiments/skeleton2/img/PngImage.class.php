<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('img.Image');

  /**
   * Class wrapper for PNG images
   *
   * @see xp://img.Image
   */
  class PngImage extends Image {
    
    /**
     * Private function which produces the image
     *
     * @see     xp://img.Image#_out
     */
    protected function _out($filename= '') {
      return imagepng($this->_hdl, $filename);
    }
  }
?>
