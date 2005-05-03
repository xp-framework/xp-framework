<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses('img.shapes.Arc', 'img.graph.PieSlice');
  
  /**
   * Shape class representing a 3D Pie chart
   *
   * @see img.Image
   */
  class PieChart extends Object {
    var
      $data=   array();
      
    /**
     * Constructor
     *
     * @access  public
     * @param   array data
     * @param   int perspective default 0
     * @param   int shadow default 10 
     * @param   int fill default IMG_ARC_PIE one of
     *          IMG_ARC_PIE
     *          IMG_ARC_CHORD
     *          IMG_ARC_NOFILL
     *          IMG_ARC_EDGED
     */ 
    function __construct($data, $perspective= 0, $shadow= 10, $fill= IMG_ARC_PIE) {
      $this->data= $data;
      $this->fill= $fill;
      $this->shadow= $shadow;
      $this->perspective= $perspective;
      
    }
    
    /**
     * Add a pie slice to the data
     *
     * @access  public
     * @param   string key
     * @param   &img.graph.PieSlice a slice object
     * @return  &img.graph.PieSlice the slice object put in
     */
    function &add($key, &$s) {
      $this->data[$key]= &$s;
      return $s;
    }

    /**
     * Draw function
     *
     * @access  public
     * @param   &resource hdl an image resource
     * @param   int w width of graph
     * @param   int h height of graph
     */
    function draw(&$hdl, $gw, $gh) {
      $a= &new Arc(
        NULL,
        $gw / 2, 
        $gh / 2, 
        $gw / 2, 
        $gh / 2 - $this->perspective, 
        0,
        0,
        $this->fill
      );
      $y= $a->cy;
      for ($i= $a->cy+ $this->shadow; $i >= $y; $i--) {
        $a->s= 0;
        $a->cy= $i;
        foreach (array_keys($this->data) as $key) {
          $a->col= &$this->data[$key]->colors[$i != $y];
          $a->e= $a->s+ $this->data[$key]->val * 3.6;
          $a->draw($hdl);
          $a->s= $a->e;
        }
      }
    }
  }
?>
