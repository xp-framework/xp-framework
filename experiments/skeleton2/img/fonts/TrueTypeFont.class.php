<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  /**
   * True type font
   *
   * @see xp://img.shapes.Text
   */
  class TrueTypeFont extends Object {
    public
      $name=            '',
      $size=            10,
      $angle=           0,
      $antialiasing=    TRUE;
      
    /**
     * Constructor
     *
     * @access  public
     */ 
    public function __construct($name, $size= 10, $angle= 0) {
      $this->name= $name;
      $this->size= $size;
      $this->angle= $angle;
      
    }
    
    /**
     * Draw function
     *
     * @access  public
     * @param   &resource hdl an image resource
     */
    public function drawtext($hdl, $col, $text, $x, $y) {
      return imagettftext(
        $hdl,
        $this->size,
        $this->angle,
        $x,
        $y,
        $col->_hdl * ($this->antialiasing ? 1 : -1),
        $this->name,
        $text
      );
    }
  }
?>
