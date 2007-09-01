<?php
/* This class is part of the XP framework
 *
 * $Id: XbmStreamWriter.class.php 10594 2007-06-11 10:04:54Z friebe $ 
 */

  namespace img::io;

  uses('img.io.StreamWriter');

  /**
   * Writes XBM to a stream
   *
   * @ext      gd
   * @see      php://imagexbm
   * @see      xp://img.io.StreamWriter
   * @purpose  Writer
   */
  class XbmStreamWriter extends StreamWriter {
    public
      $foreground  = 0;
    
    /**
     * Constructor
     *
     * @param   io.Stream stream
     * @param   int foreground default 0
     */
    public function __construct($stream, $foreground= 0) {
      parent::__construct($stream);
      $this->foreground= $foreground;
    }

    /**
     * Output an image
     *
     * @param   resource handle
     * @return  bool
     */    
    protected function output($handle) {
      return imagexbm($handle, '', $this->foreground);
    }
  }
?>
