<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

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
     * @deprecated
     * @return  resource
     * @throws  img.ImagingException
     */    
    public function readFromStream() {
      return imagecreatefromjpeg($this->stream->getURI());
    }

    /**
     * Read an image
     *
     * @return  resource
     * @throws  img.ImagingException
     */    
    public function readImage() {
      return imagecreatefromjpeg(Streams::readableUri($this->stream));
    }
  }
?>
