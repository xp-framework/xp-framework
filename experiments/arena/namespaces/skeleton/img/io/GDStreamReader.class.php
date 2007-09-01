<?php
/* This class is part of the XP framework
 *
 * $Id: GDStreamReader.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace img::io;

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
     * @return  resource
     * @throws  img.ImagingException
     */    
    public function readFromStream() {
      return imagecreatefromgd($this->stream->getURI());
    }
  }
?>
