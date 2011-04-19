<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('img.Drawable');

  /**
   * Shape class representing a text
   *
   * @see xp://img.Image
   */
  class Text extends Object implements Drawable {
    public
      $font=    NULL,
      $col=     NULL,
      $text=    '',
      $x=       0,
      $y=       0;
      
    /**
     * Constructor
     *
     * @param   fonts.Font col color
     * @param   string text
     * @param   int x
     * @param   int y
     */ 
    public function __construct($col, $font, $text, $x, $y) {
      $this->col= $col;
      $this->font= $font;
      $this->text= $text;
      $this->x= $x;
      $this->y= $y;
    }
    
    /**
     * Draws this object onto an image
     *
     * @param   img.Image image
     * @return  var
     */
    public function draw($image) {
      return $this->font->drawtext(
        $image->handle, 
        $this->col, 
        $this->text, 
        $this->x, 
        $this->y
      );
    }

  } 
?>
