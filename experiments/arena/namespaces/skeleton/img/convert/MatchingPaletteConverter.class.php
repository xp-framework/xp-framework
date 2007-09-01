<?php
/* This class is part of the XP framework
 *
 * $Id: MatchingPaletteConverter.class.php 10594 2007-06-11 10:04:54Z friebe $ 
 */

  namespace img::convert;

  uses('img.convert.PaletteConverter', 'img.Image');
 
  /**
   * Converts a truecolor image to a paletted image. Uses 
   * imagecolormatch() to get a better result
   *
   * @ext      gd
   * @see      php://imagecolormatch
   * @see      xp://img.convert.PaletteConverter
   * @purpose  Converter
   */
  class MatchingPaletteConverter extends PaletteConverter {
  
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
      
      try {
        $tmp= img::Image::create($image->getWidth(), $image->getHeight(), IMG_TRUECOLOR);
        $tmp->copyFrom($image);
        imagetruecolortopalette(
          $image->handle, 
          $this->dither, 
          $this->ncolors
        );
        imagecolormatch($tmp->handle, $image->handle);
        delete($tmp);
      } catch (img::ImagingException $e) {
        throw($e);
      }
      return TRUE;
    }
  }
?>
