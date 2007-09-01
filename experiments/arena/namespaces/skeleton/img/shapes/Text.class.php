<?php
/* This class is part of the XP framework
 *
 * $Id: Text.class.php 10594 2007-06-11 10:04:54Z friebe $
 */

  namespace img::shapes;

  uses('img.Drawable');

  /**
   * Shape class representing a text
   *
   * @see xp://img.Image
   */
  class Text extends lang::Object implements img::Drawable {
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
     * @return  mixed
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
