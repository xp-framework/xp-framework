<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('img.io.StreamReader');

  /**
   * Reads PNG from an image
   *
   * @ext      gd
   * @see      php://imagecreatefrompng
   * @see      xp://img.io.StreamReader
   * @purpose  Reader
   */
  class PngStreamReader extends StreamReader {

    /**
     * Read an image
     *
     * @access  protected
     * @return  resource
     * @throws  img.ImagingException
     */    
    public function readFromStream() {
      return imagecreatefrompng($this->stream->getURI());
    }
  }
?>
