<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('img.convert.PaletteConverter');
 
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
     * @access  public
     * @param   &img.Image image
     * @return  bool
     * @throws  img.ImagingException
     */
    function convert(&$image) { 
      if (!imageistruecolor($image->handle)) return FALSE;
      
      try(); {
        $tmp= &Image::create($image->getWidth(), $image->getHeight(), IMG_TRUECOLOR);
        $tmp->copyFrom($image);
        parent::convert($image);
        imagecolormatch($tmp->handle, $image->handle);
        delete($tmp);
      } if (catch('ImagingException', $e)) {
        return throw($e);
      }
      return TRUE;
    }
  }
?>
