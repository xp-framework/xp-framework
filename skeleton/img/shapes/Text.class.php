<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  /**
   * Shape class representing a text
   *
   * @see xp://img.Image
   */
  class Text extends Object {
    var
      $font=    NULL,
      $col=     NULL,
      $text=    '',
      $x=       0,
      $y=       0;
      
    /**
     * Constructor
     *
     * @access  public
     * @param   &fonts.Font col color
     * @param   string text
     * @param   int x
     * @param   int y
     */ 
    function __construct(&$col, &$font, $text, $x, $y) {
      $this->col= &$col;
      $this->font= &$font;
      $this->text= $text;
      $this->x= $x;
      $this->y= $y;
    }
    
    /**
     * Draws this object onto an image
     *
     * @access  public
     * @param   &img.Image image
     * @return  mixed
     */
    function draw(&$image) {
      return $this->font->drawtext(
        $image->handle, 
        $this->col, 
        $this->text, 
        $this->x, 
        $this->y
      );
    }

  } implements(__FILE__, 'img.Drawable');
?>
