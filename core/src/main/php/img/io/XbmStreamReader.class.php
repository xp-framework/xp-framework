<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('img.io.StreamReader', 'img.io.UriReader');

  /**
   * Reads XBM from an image
   *
   * @ext  gd
   * @see  php://imagecreatefromxbm
   * @see  xp://img.io.StreamReader
   */
  class XbmStreamReader extends StreamReader implements img·io·UriReader {

    /**
     * Read image
     *
     * @param   string uri
     * @return  resource
     * @throws  img.ImagingException
     */
    public function readImageFromUri($uri) {
      if (FALSE === ($r= imagecreatefromxbm($uri))) {
        $e= new ImagingException('Cannot read image from "'.$uri.'"');
        xp::gc(__FILE__);
        throw $e;
      }
      return $r;
    }
  }
?>
