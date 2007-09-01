<?php
/* This class is part of the XP framework
 *
 * $Id: XbmStreamReader.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace img::io;

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
     * @return  resource
     * @throws  img.ImagingException
     */    
    public function readFromStream() {
      return imagecreatefromxbm($this->stream->getURI());
    }
  }
?>
