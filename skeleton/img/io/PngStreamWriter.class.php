<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

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
     * @access  protected
     * @param   resource handle
     * @return  bool
     */    
    public function output($handle) {
      return imagepng($handle);
    }
  }
?>
