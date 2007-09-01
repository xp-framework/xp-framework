<?php
/* This class is part of the XP framework
 *
 * $Id: BitmapFont.class.php 10594 2007-06-11 10:04:54Z friebe $
 */

  namespace img::fonts;

  /**
   * Bitmap font. The font-IDs 1, 2, 3, 4 and 5 resemble builtin fonts.
   *
   * @see      php://imagestring
   * @see      xp://img.shapes.Text
   * @purpose  Font
   */
  class BitmapFont extends lang::Object {
    public
      $id= 0;
      
    /**
     * Constructor
     *
     * @param   int id
     */ 
    public function __construct($id) {
      $this->id= $id;
    }
    
    /**
     * Draw function
     *
     * @param   resource hdl an image resource
     * @param   img.Color col
     * @param   string text
     * @param   int x
     * @param   int y
     */
    public function drawtext($hdl, $col, $text, $x, $y) {
      return imagestring(
        $hdl,
        $this->id,
        $x,
        $y,
        $text,
        $col->handle
      );
    }
  }
?>
