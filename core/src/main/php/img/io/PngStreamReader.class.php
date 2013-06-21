<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('img.io.StreamReader', 'img.io.UriReader');

  /**
   * Reads PNG from an image
   *
   * @ext   gd
   * @see   php://imagecreatefrompng
   * @see   xp://img.io.StreamReader
   * @test  xp://net.xp_framework.unittest.img.PngImageReaderTest
   */
  class PngStreamReader extends StreamReader implements img·io·UriReader {

    /**
     * Read image
     *
     * @param   string uri
     * @return  resource
     * @throws  img.ImagingException
     */
    public function readImageFromUri($uri) {
      if (FALSE === ($r= imagecreatefrompng($uri))) {
        $e= new ImagingException('Cannot read image from "'.$uri.'"');
        xp::gc(__FILE__);
        throw $e;
      }
      return $r;
    }
  }
?>
