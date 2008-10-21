<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('img.io.StreamReader');

  /**
   * Reads GD2 from an image
   *
   * @ext      gd
   * @see      php://imagecreatefromgd2
   * @see      xp://img.io.StreamReader
   * @purpose  Reader
   */
  class GD2StreamReader extends StreamReader {

    /**
     * Read an image
     *
     * @deprecated
     * @return  resource
     * @throws  img.ImagingException
     */    
    public function readFromStream() {
      return imagecreatefromgd2($this->stream->getURI());
    }

    /**
     * Read an image
     *
     * @return  resource
     * @throws  img.ImagingException
     */    
    public function readImage() {
      return imagecreatefromgd2(Streams::readableUri($this->stream));
    }
  }
?>
