<?php
/* This class is part of the XP framework
 *
 * $Id: JpegStreamWriter.class.php 10594 2007-06-11 10:04:54Z friebe $ 
 */

  namespace img::io;

  uses('img.io.StreamWriter');

  /**
   * Writes JPEG to a stream
   *
   * @ext      gd
   * @see      php://imagejpeg
   * @see      xp://img.io.StreamWriter
   * @purpose  Writer
   */
  class JpegStreamWriter extends StreamWriter {
    public
      $quality  = 0;
    
    /**
     * Constructor
     *
     * @param   io.Stream stream
     * @param   int quality default 75
     */
    public function __construct($stream, $quality= 75) {
      parent::__construct($stream);
      $this->quality= $quality;
    }

    /**
     * Output an image
     *
     * @param   resource handle
     * @return  bool
     */    
    protected function output($handle) {
      return imagejpeg($handle, '', $this->quality);
    }
  }
?>
