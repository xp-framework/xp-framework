<?php
/* This class is part of the XP framework
 *
 * $Id: PaletteConverter.class.php 10594 2007-06-11 10:04:54Z friebe $ 
 */

  namespace img::convert;

  uses('img.convert.ImageConverter');

  /**
   * Converts a truecolor image to a paletted image
   *
   * @ext      gd
   * @see      xp://img.convert.ImageConverter
   * @purpose  Converter
   */
  class PaletteConverter extends lang::Object implements ImageConverter {
    public
      $dither   = FALSE,
      $ncolors  = 0;

    /**
     * Constructor
     *
     * @see     php://imagetruecolortopalette
     * @param   bool dither default FALSE indicates if the image should be dithered
     * @param   int ncolors default 256 maximum # of colors retained in the palette
     */
    public function __construct($dither= , $ncolors= 256) {
      $this->dither= $dither;
      $this->ncolors= $ncolors;
    }
  
    /**
     * Convert an image. Returns TRUE when successfull, FALSE if image is
     * not a truecolor image.
     *
     * @param   img.Image image
     * @return  bool
     * @throws  img.ImagingException
     */
    public function convert($image) { 
      if (!imageistruecolor($image->handle)) return FALSE;

      return imagetruecolortopalette(
        $image->handle, 
        $this->dither, 
        $this->ncolors
      );
    }

  } 
?>
