<?php
/* This class is part of the XP framework
 *
 * $Id: GD2StreamReader.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace img::io;

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
     * @return  resource
     * @throws  img.ImagingException
     */    
    public function readFromStream() {
      return imagecreatefromgd2($this->stream->getURI());
    }
  }
?>
