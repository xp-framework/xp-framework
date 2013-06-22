<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('img.io.StreamWriter');

  /**
   * Writes PNG to a stream
   *
   * @ext   gd
   * @see   php://imagepng
   * @see   xp://img.io.StreamWriter
   * @test  xp://net.xp_framework.unittest.img.PngImageWriterTest
   */
  class PngStreamWriter extends StreamWriter {

    /**
     * Output an image
     *
     * @param   resource handle
     * @return  bool
     */    
    public function output($handle) {
      return imagepng($handle);
    }
  }
?>
