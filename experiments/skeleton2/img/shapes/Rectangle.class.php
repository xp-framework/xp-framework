<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  /**
   * Shape class representing a rectangle
   *
   * <code>
   *   $i= new PngImage(300, 300);
   *   $i->create();
   *   $blue= $i->allocate(new Color('#0000cc'));
   *   $i->draw(new RectangleShape($blue, 0, 0, 300, 300));
   *   $i->toFile(new File('out.png'));
   * </code>
   *
   * @see xp://img.Image
   */
  class Rectangle extends Object {
    public
      $col=  NULL,
      $x1=   0,
      $y1=   0,
      $x2=   0,
      $y2=   0;
      
    /**
     * Constructor
     *
     * @access  public
     * @param   &img.Color col color
     * @param   int x1 x coordinate of upper left corner
     * @param   int y1 y coordinate of upper left corner
     * @param   int x2 x coordinate of bottom right corner
     * @param   int y2 y coordinate of bottom right corner
     * @param   bool fill default FALSE
     */ 
    public function __construct(Color $col, $x1, $y1, $x2, $y2, $fill= FALSE) {
      $this->col= $col;
      $this->x1= $x1;
      $this->y1= $y1;
      $this->x2= $x2;
      $this->y2= $y2;
      $this->fill= $fill;
      
    }
    
    /**
     * Draw function
     *
     * @access  public
     * @param   &resource hdl an image resource
     */
    public function draw($hdl) {
      if ($this->fill) return imagefilledrectangle(
        $hdl,
        $this->x1,
        $this->y1,
        $this->x2,
        $this->y2,
        $this->col->_hdl
      ); else return imagerectangle(
        $hdl,
        $this->x1,
        $this->y1,
        $this->x2,
        $this->y2,
        $this->col->_hdl
      );
    }
  }
?>
