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
    public
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
    public function __construct(Font $col, $font, $text, $x, $y) {
      $this->col= $col;
      $this->font= $font;
      $this->text= $text;
      $this->x= $x;
      $this->y= $y;
      
    }
    
    /**
     * Draw function
     *
     * @access  public
     * @param   &resource hdl an image resource
     */
    public function draw($hdl) {
      return $this->font->drawtext(
        $hdl, 
        $this->col, 
        $this->text, 
        $this->x, 
        $this->y
      );
    }
  }
?>
