<?php
/* This class is part of the XP framework
 *
 * $Id: GifStreamWriter.class.php 10594 2007-06-11 10:04:54Z friebe $ 
 */

  namespace img::io;

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
    public
      $dither   = FALSE,
      $ncolors  = 0;

    /**
     * Constructor
     *
     * @see     php://imagetruecolortopalette
     * @param   io.Stream stream
     * @param   bool dither default FALSE indicates if the image should be dithered
     * @param   int ncolors default 256 maximum # of colors retained in the palette
     */
    public function __construct($stream, $dither= , $ncolors= 256) {
      parent::__construct($stream);
      $this->dither= $dither;
      $this->ncolors= $ncolors;
    }

    /**
     * Output an image. If the image is true-color, it will be converted
     * to a paletted image first using imagetruecolortopalette().
     *
     * @param   resource handle
     * @return  bool
     */    
    protected function output($handle) {
      if (imageistruecolor($handle)) {
        imagetruecolortopalette($handle, $this->dither, $this->ncolors);
      }
      return imagegif($handle);
    }
  }
?>
