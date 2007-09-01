<?php
/* This class is part of the XP framework
 *
 * $Id: Arc3D.class.php 10594 2007-06-11 10:04:54Z friebe $
 */

  namespace img::shapes;

  uses('img.shapes.Arc', 'img.Drawable');
  
  /**
   * Shape class representing a three-dimensional arc
   *
   * <code>
   *   $i= &new PngImage(300, 300);
   *   $i->create();
   *   $b= &$i->allocate(new Color('#0000cc'));
   *   $d= &$i->allocate(new Color('#000066'));
   *   $i->draw(new Arc3DShape(array($b, $d), 200, 100, 200, 100, 0, 320));
   *   $i->toFile(new File('out.png'));
   * </code>
   *
   * @see xp://img.Image
   */
  class Arc3D extends Arc implements img::Drawable {
    public
      $colors= array(),
      $shadow= 0;
      
    /**
     * Constructor
     *
     * @param   img.Color[] col colors, the first for the "lid", the second for the shadow
     * @param   int cx x center of circle
     * @param   int cy y center of circle
     * @param   int w width
     * @param   int h height
     * @param   int s default 0 start
     * @param   int e default 360 end
     * @param   int fill default IMG_ARC_PIE one of
     *          IMG_ARC_PIE
     *          IMG_ARC_CHORD
     *          IMG_ARC_NOFILL
     *          IMG_ARC_EDGED
     * @param   int shadow default 10 
     */ 
    public function __construct($colors, $cx, $cy, $w, $h, $s= 0, $e= 360, $fill= , $shadow= 10) {
      $this->colors= $colors;
      $this->shadow= $shadow;
      parent::__construct($colors[0], $cx, $cy, $w, $h, $s, $e, $fill);
    }

    /**
     * Draws this object onto an image
     *
     * @param   img.Image image
     * @return  mixed
     */
    public function draw($image) {
      $this->col= $this->colors[1];
      $cy= $this->cy;
      for ($i= 1; $i < $this->shadow; $i++) {
        $this->cy= $cy+ $i;
        parent::draw($image);
      }
      $this->cy= $cy;
      $this->col= $this->colors[0];
      parent::draw($image);
    }

  } 
?>
