<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses('img.Image');
  
  /**
   * Class wrapper for JPEG images
   *
   * @see img.Image
   */
  class JpegImage extends Image {
    var $quality= 75.0;
    
    /**
     * Private function which produces the image
     *
     * @see     img.Image#_out
     */
    function _out($filename= '') {
      return imagejpeg($this->_hdl, $filename, $this->quality);
    }
  }
?>
