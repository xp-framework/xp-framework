<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('img.io.StreamReader');

  /**
   * Reads WBMP from an image
   *
   * @ext      gd
   * @see      php://imagecreatefromwbmp
   * @see      xp://img.io.StreamReader
   * @purpose  Reader
   */
  class WBmpStreamReader extends StreamReader {

    /**
     * Read an image
     *
     * @deprecated
     * @return  resource
     * @throws  img.ImagingException
     */    
    public function readFromStream() {
      return imagecreatefromwbmp($this->stream->getURI());
    }
    
    /**
     * Read an image
     *
     * @return  resource
     * @throws  img.ImagingException
     */    
    public function readImage() {
      return imagecreatefromwbmp(Streams::readableUri($this->stream));
    }
  }
?>
