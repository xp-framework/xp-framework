<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses('img.Image');
  
  /**
   * Class wrapper for GIF images
   *
   * @see img.Image
   */
  class GifImage extends Image {
    
    /**
     * Private function which produces the image
     *
     * @see     img.Image#_out
     */
    function _out($filename= '') {
      return imagegif($this->_hdl, $filename);
    }
  }
?>
