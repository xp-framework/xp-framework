<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses('img.Image');
  
  /**
   * Class wrapper for PNG images
   *
   * @see img.Image
   */
  class PngImage extends Image {
    
    /**
     * Private function which produces the image
     *
     * @see     img.Image#_out
     */
    function _out($filename= '') {
      return imagepng($this->_hdl, $filename);
    }
  }
?>
