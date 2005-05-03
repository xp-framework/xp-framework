<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  /**
   * Shape class representing a 3D Pie chart's clice
   *
   * @see img.graph.PieChart
   */
  class PieSlice extends Object {
    var 
      $val      = 0.0,
      $colors   = array();
      
    /**
     * Constructor
     *
     * @access  public
     * @param   float val
     * @param   &mixed colors either an array of two colors, the second
     *          representing the shadow, or one color, for both lid and shadow
     */
    function __construct($val, &$colors) {
      $this->val= $val;
      if (!is_array($colors)) {
        $this->colors= array($colors, $colors);
      } else {
        $this->colors= &$colors;
      }
    } 
  }
?>
