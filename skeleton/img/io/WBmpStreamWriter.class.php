<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('img.io.StreamWriter');

  /**
   * Writes WBMP to a stream
   *
   * @ext      gd
   * @see      php://imagewbmp
   * @see      xp://img.io.StreamWriter
   * @purpose  Writer
   */
  class WBmpStreamWriter extends StreamWriter {

    /**
     * Output an image
     *
     * @access  protected
     * @param   resource handle
     * @return  bool
     */    
    function output($handle) {
      return imagewbmp($handle);
    }
  }
?>
