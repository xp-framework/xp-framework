<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('img.io.StreamWriter');

  /**
   * Writes GIF to a stream
   *
   * @ext      gd
   * @see      php://imagegif
   * @see      xp://img.io.StreamWriter
   * @purpose  Writer
   */
  class GifStreamWriter extends StreamWriter {

    /**
     * Output an image
     *
     * @access  protected
     * @param   resource handle
     * @return  bool
     */    
    function output($handle) {
      return imagepng($handle);
    }
  }
?>
