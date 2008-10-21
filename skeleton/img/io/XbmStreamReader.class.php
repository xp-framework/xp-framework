<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('img.io.StreamReader');

  /**
   * Reads XBM from an image
   *
   * @ext      gd
   * @see      php://imagecreatefromxbm
   * @see      xp://img.io.StreamReader
   * @purpose  Reader
   */
  class XbmStreamReader extends StreamReader {

    /**
     * Read an image
     *
     * @deprecated
     * @return  resource
     * @throws  img.ImagingException
     */    
    public function readFromStream() {
      return imagecreatefromxbm($this->stream->getURI());
    }
    
    /**
     * Read an image
     *
     * @return  resource
     * @throws  img.ImagingException
     */    
    public function readImage() {
      return imagecreatefromxbm(Streams::readableUri($this->stream));
    }
  }
?>
