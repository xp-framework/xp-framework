<?php
/* This class is part of the XP framework
 *
 * $Id: Line.class.php 10594 2007-06-11 10:04:54Z friebe $
 */

  namespace img::shapes;

  uses('img.Drawable');

  /**
   * Shape class representing a line
   *
   * <code>
   *   $i= &new PngImage(300, 300);
   *   $i->create();
   *   $blue= &$i->allocate(new Color('#0000cc'));
   *   $i->draw(new LineShape($blue, 0, 0, 300, 300));
   *   $i->toFile(new File('out.png'));
   * </code>
   *
   * @see xp://img.Image
   */
  class Line extends lang::Object implements img::Drawable {
    public
      $col=  NULL,
      $x1=   0,
      $y1=   0,
      $x2=   0,
      $y2=   0;
      
    /**
     * Constructor
     *
     * @param   img.Color col color
     * @param   int x1 x coordinate of starting point
     * @param   int y1 y coordinate of starting point
     * @param   int x2 x coordinate of ending point
     * @param   int y2 y coordinate of ending point
     */ 
    public function __construct($col, $x1, $y1, $x2, $y2) {
      $this->col= $col;
      $this->x1= $x1;
      $this->y1= $y1;
      $this->x2= $x2;
      $this->y2= $y2;
      
    }
    
    /**
     * Draws this object onto an image
     *
     * @param   img.Image image
     * @return  mixed
     */
    public function draw($image) {
      return imageline(
        $image->handle,
        $this->x1,
        $this->y1,
        $this->x2,
        $this->y2,
        $this->col->handle
      );
    }

  } 
?>
