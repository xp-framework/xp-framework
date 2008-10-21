<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('img.io.StreamReader');

  /**
   * Reads GIF from an image
   *
   * @ext      gd
   * @see      php://imagecreatefromgif
   * @see      xp://img.io.StreamReader
   * @purpose  Reader
   */
  class GifStreamReader extends StreamReader {

    /**
     * Read an image
     *
     * @deprecated
     * @return  resource
     * @throws  img.ImagingException
     */    
    public function readFromStream() {
      return imagecreatefromgif($this->stream->getURI());
    }
    
    /**
     * Read an image
     *
     * @return  resource
     * @throws  img.ImagingException
     */    
    public function readImage() {
      return imagecreatefromgif(Streams::readableUri($this->stream));
    }
  }
?>
