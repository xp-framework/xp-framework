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
     * @access  protected
     * @return  resource
     * @throws  img.ImagingException
     */    
    public function readFromStream() {
      return imagecreatefromwbmp($this->stream->getURI());
    }
  }
?>
