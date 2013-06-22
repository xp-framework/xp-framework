<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('img.io.StreamReader', 'img.io.UriReader');

  /**
   * Reads GD2 from an image
   *
   * @ext  gd
   * @see  php://imagecreatefromgd2
   * @see  xp://img.io.StreamReader
   */
  class GD2StreamReader extends StreamReader implements img·io·UriReader {

    /**
     * Read image
     *
     * @param   string uri
     * @return  resource
     * @throws  img.ImagingException
     */
    public function readImageFromUri($uri) {
      if (FALSE === ($r= imagecreatefromgd2($uri))) {
        $e= new ImagingException('Cannot read image from "'.$uri.'"');
        xp::gc(__FILE__);
        throw $e;
      }
      return $r;
    }
  }
?>
