<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('img.io.StreamWriter');

  /**
   * Writes GD2 to a stream
   *
   * @ext      gd
   * @see      php://imagegd2
   * @see      http://www.boutell.com/gd/manual2.0.11.html#gdImageGd2 
   * @see      xp://img.io.StreamWriter
   * @purpose  Writer
   */
  class GD2StreamWriter extends StreamWriter {
    public
      $format  = IMG_GD2_RAW;
    
    /**
     * Constructor
     *
     * @param   io.Stream stream
     * @param   int format default IMG_GD2_RAW one of the IMG_GD2_* constants
     */
    public function __construct($stream, $format= IMG_GD2_RAW) {
      parent::__construct($stream);
      $this->format= $format;
    }

    /**
     * Output an image
     *
     * @param   resource handle
     * @return  bool
     */    
    protected function output($handle) {
      return imagegd2($handle, '', 0, $this->format);
    }
  }
?>
