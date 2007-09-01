<?php
/* This class is part of the XP framework
 *
 * $Id: GDStreamWriter.class.php 8974 2006-12-27 17:29:09Z friebe $ 
 */

  namespace img::io;

  uses('img.io.StreamWriter');

  /**
   * Writes GD to a stream
   *
   * @ext      gd
   * @see      php://imagegd
   * @see      xp://img.io.StreamWriter
   * @purpose  Writer
   */
  class GDStreamWriter extends StreamWriter {
    
    /**
     * Output an image
     *
     * @param   resource handle
     * @return  bool
     */    
    protected function output($handle) {
      return imagegd($handle);
    }
  }
?>
