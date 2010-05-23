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
     * Read image via imagecreatefromgif()
     *
     * @param   string uri
     * @return  resource
     * @throws  img.ImagingException
     */
    protected function readImage0($uri) {
      if (FALSE === ($r= imagecreatefromgif($uri))) {
        $e= new ImagingException('Cannot read image');
        xp::gc(__FILE__);
        throw $e;
      }
      return $r;
    }

    /**
     * Read an image
     *
     * @deprecated
     * @return  resource
     * @throws  img.ImagingException
     */    
    public function readFromStream() {
      return $this->readImage0($this->stream->getURI());
    }
    
    /**
     * Read an image
     *
     * @return  resource
     * @throws  img.ImagingException
     */    
    public function readImage() {
      return $this->readImage0(Streams::readableUri($this->stream));
    }
  }
?>
