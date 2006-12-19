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
     * @access  protected
     * @return  resource
     * @throws  img.ImagingException
     */    
    public function readFromStream() {
      return imagecreatefromxbm($this->stream->getURI());
    }
  }
?>
