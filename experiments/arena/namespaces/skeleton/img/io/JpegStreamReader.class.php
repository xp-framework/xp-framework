<?php
/* This class is part of the XP framework
 *
 * $Id: JpegStreamReader.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace img::io;

  uses('img.io.StreamReader');

  /**
   * Reads JPEG from an image
   *
   * @ext      gd
   * @see      php://imagecreatefromjpeg
   * @see      xp://img.io.StreamReader
   * @purpose  Reader
   */
  class JpegStreamReader extends StreamReader {

    /**
     * Read an image
     *
     * @return  resource
     * @throws  img.ImagingException
     */    
    public function readFromStream() {
      return imagecreatefromjpeg($this->stream->getURI());
    }
  }
?>
