<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('img.Image');

  /**
   * Class wrapper for GIF images
   *
   * @see xp://img.Image
   */
  class GifImage extends Image {
    
    /**
     * Private function which produces the image
     *
     * @see xp://img.Image#_out
     */
    protected function _out($filename= '') {
      return imagegif($this->_hdl, $filename);
    }
  }
?>
