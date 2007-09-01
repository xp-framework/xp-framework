<?php
/* This class is part of the XP framework
 *
 * $Id: PngStreamWriter.class.php 8974 2006-12-27 17:29:09Z friebe $ 
 */

  namespace img::io;

  uses('img.io.StreamWriter');

  /**
   * Writes PNG to a stream
   *
   * @ext      gd
   * @see      php://imagepng
   * @see      xp://img.io.StreamWriter
   * @purpose  Writer
   */
  class PngStreamWriter extends StreamWriter {

    /**
     * Output an image
     *
     * @param   resource handle
     * @return  bool
     */    
    protected function output($handle) {
      return imagepng($handle);
    }
  }
?>
