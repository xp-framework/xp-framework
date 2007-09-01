<?php
/* This class is part of the XP framework
 *
 * $Id: GifStreamReader.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace img::io;

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
     * @return  resource
     * @throws  img.ImagingException
     */    
    public function readFromStream() {
      return imagecreatefromgif($this->stream->getURI());
    }
  }
?>
