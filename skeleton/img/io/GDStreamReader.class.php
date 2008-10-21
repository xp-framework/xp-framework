<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('img.io.StreamReader');

  /**
   * Reads GD from an image
   *
   * @ext      gd
   * @see      php://imagecreatefromgd
   * @see      xp://img.io.StreamReader
   * @purpose  Reader
   */
  class GDStreamReader extends StreamReader {

    /**
     * Read an image
     *
     * @deprecated
     * @return  resource
     * @throws  img.ImagingException
     */    
    public function readFromStream() {
      return imagecreatefromgd($this->stream->getURI());
    }
    
    /**
     * Read an image
     *
     * @return  resource
     * @throws  img.ImagingException
     */    
    public function readImage() {
      return imagecreatefromgd(Streams::readableUri($this->stream));
    }
  }
?>
