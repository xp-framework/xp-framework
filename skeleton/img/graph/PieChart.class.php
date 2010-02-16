<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses(
    'img.shapes.Arc',
    'img.graph.PieSlice',
    'img.Drawable'
  );
  
  /**
   * Shape class representing a 3D Pie chart
   *
   * @deprecated
   * @see img.Image
   */
  class PieChart extends Object implements Drawable {
    public
      $slices       = array(),
      $perspective  = 0,
      $shadow       = 0,
      $fill         = 0;
      
    /**
     * Constructor
     *
     * @param   int perspective default 0
     * @param   int shadow default 10 
     * @param   int fill default IMG_ARC_PIE one of IMG_ARC_* constants
     */ 
    public function __construct($perspective= 0, $shadow= 10, $fill= IMG_ARC_PIE) {
      $this->perspective= $perspective;
      $this->shadow= $shadow;
      $this->fill= $fill;
    }
    
    /**
     * Add a pie slice to the data
     *
     * @param   string key
     * @param   img.graph.PieSlice a slice object
     * @return  img.graph.PieSlice the slice object put in
     */
    public function add($slice) {
      $this->slices[]= $slice;
      return $slice;
    }

    /**
     * Draws this object onto an image
     *
     * @param   img.Image image
     * @return  var
     */
    public function draw($image) {
      $arc= new Arc(
        NULL,
        $image->getWidth() / 2, 
        $image->getHeight() / 2, 
        $image->getWidth() / 2, 
        $image->getHeight() / 2 - $this->perspective, 
        0,
        0,
        $this->fill
      );
      $y= $arc->cy;
      $x= $arc->cx;
      $factor= $arc->w / $arc->h;
      for ($i= $arc->cy+ $this->shadow; $i >= $y; $i--) {
        $arc->s= 0;

        foreach (array_keys($this->slices) as $key) {
          $arc->col= $this->slices[$key]->colors[$i != $y];
          $arc->e= $arc->s+ $this->slices[$key]->value * 3.6;

          $offset= 2 * M_PI - deg2rad($arc->s + $this->slices[$key]->value * 1.8);

          $arc->cx= $x;
          $arc->cy= $i;

          // If a slice is detached, move it.
          if ($this->slices[$key]->detached) {
            $arc->cx= $x+ 50 * cos($offset);
            $arc->cy= $i- (50 / $factor) * sin($offset);
          }
          
          $arc->draw($image);
          $arc->s= $arc->e;
        }
      }
    }

  } 
?>
