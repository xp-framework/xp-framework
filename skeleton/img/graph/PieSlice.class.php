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
      $value    = 0.0,
      $caption  = '',
      $colors   = array();
      
    /**
     * Constructor
     *
     * @access  public
     * @param   float value
     * @param   &mixed colors either an array of two colors, the second
     *          representing the shadow, or one color, for both lid and shadow
     */
    function __construct($value, &$colors) {
      $this->value= $value;
      if (!is_array($colors)) {
        $this->colors= array($colors, $colors);
      } else {
        $this->colors= &$colors;
      }
    }

    /**
     * Set Value
     *
     * @access  public
     * @param   int value
     */
    function setValue($value) {
      $this->value= $value;
    }

    /**
     * Get Value
     *
     * @access  public
     * @return  int
     */
    function getValue() {
      return $this->value;
    }

    /**
     * Set Caption
     *
     * @access  public
     * @param   string caption
     */
    function setCaption($caption) {
      $this->caption= $caption;
    }

    /**
     * Get Caption
     *
     * @access  public
     * @return  string
     */
    function getCaption() {
      return $this->caption;
    }
  }
?>
