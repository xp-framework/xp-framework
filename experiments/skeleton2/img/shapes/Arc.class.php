<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  /**
   * Shape class representing an arc
   *
   * <code>
   *   $i= new PngImage(300, 300);
   *   $i->create();
   *   $blue= $i->allocate(new Color('#0000cc'));
   *   $i->draw(new ArcShape($blue, 200, 100, 200, 100, 0, 320));
   *   $i->toFile(new File('out.png'));
   * </code>
   *
   * @see xp://img.Image
   */
  class Arc extends Object {
    public
      $col=  NULL,
      $cx=   0,
      $cy=   0,
      $w=    0,
      $h=    0,
      $s=    0,
      $e=    0,
      $fill= FALSE;
      
    /**
     * Constructor
     *
     * @access  public
     * @param   &img.Color col color
     * @param   int cx x center of circle
     * @param   int cy y center of circle
     * @param   int w width
     * @param   int h height
     * @param   int s default 0 start
     * @param   int e default 360 end
     * @param   int fill default FALSE FALSE for no fill or one of
     *          IMG_ARC_PIE
     *          IMG_ARC_CHORD
     *          IMG_ARC_NOFILL
     *          IMG_ARC_EDGED
     */ 
    public function __construct(Color $col, $cx, $cy, $w, $h, $s= 0, $e= 360, $fill= FALSE) {
      $this->col= $col;
      $this->cx= $cx;
      $this->cy= $cy;
      $this->w= $w;
      $this->h= $h;
      $this->s= $s;
      $this->e= $e;
      $this->fill= $fill;
      
    }
    
    /**
     * Draw function
     *
     * @access  public
     * @param   &resource hdl an image resource
     */
    public function draw($hdl) {
      if (FALSE !== $this->fill) return imagefilledarc(
        $hdl,
        $this->cx,
        $this->cy,
        $this->w,
        $this->h,
        $this->s,
        $this->e,
        $this->col->_hdl,
        $this->fill
      ); else return imagearc(
        $hdl,
        $this->cx,
        $this->cy,
        $this->w,
        $this->h,
        $this->s,
        $this->e,
        $this->col->_hdl
      );
    }
  }
?>
