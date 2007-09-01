<?php
/* This class is part of the XP framework
 *
 * $Id: PngStreamReader.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace img::io;

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
     * @return  resource
     * @throws  img.ImagingException
     */    
    public function readFromStream() {
      return imagecreatefrompng($this->stream->getURI());
    }
  }
?>
