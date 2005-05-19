<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('img.chart.Chart');

  // Alignment
  define('CHART_HORIZONTAL',  0x0000);
  define('CHART_VERTICAL',    0x0001);
  
  // Distance
  define('DISTANCE_AUTO',     -1);
  
  // Ranges
  define('RANGE_AUTO',        -1);
  
  /**
   * Bar chart
   *
   * @see      xp://img.chart.Chart
   * @purpose  Chart
   */
  class BarChart extends Chart {
    var
      $alignment = CHART_HORIZONTAL,
      $barWidth  = 20,
      $distance  = DISTANCE_AUTO,
      $range     = array(RANGE_AUTO, RANGE_AUTO, RANGE_AUTO);

    /**
     * Set range. Pass RANGE_AUTO to upper, lower and/or step to have 
     * this value calculated automatically (default behaviour).
     *
     * @access  public
     * @param   float lower
     * @param   float upper
     * @param   float step
     */
    function setRange($lower, $upper, $step) {
      $this->range= array($lower, $upper, $step);
    }

    /**
     * Get range
     *
     * @access  public
     * @param   float[] the lower and upper range and the range setp, in this order
     */
    function getRange() {
      return $this->range;
    }

    /**
     * Set alignment
     *
     * @access  public
     * @param   int alignment one of CHART_HORIZONTAL, CHART_VERTICAL
     */
    function setAlignment($alignment) {
      $this->alignment= $alignment;
    }

    /**
     * Sets the width of one bar in this bar chart
     *
     * @access  public
     * @param   int width
     */
    function setBarWidth($width) {
      $this->barWidth= $width;
    }
    
    /**
     * Returns the width of one bar in this bar chart
     *
     * @access  public
     * @return  int
     */
    function getBarWidth() {
      return $this->barWidth;
    }

    /**
     * Set distance between the bars. Pass the DISTANCE_AUTO constant to
     * have it calculated automatically.
     *
     * @access  public
     * @param   int distance
     */
    function setDistance($distance) {
      $this->distance= $distance;
    }

    /**
     * Get distance between the bars
     *
     * @access  public
     * @return  int
     */
    function getDistance() {
      return $this->distance;
    }
  }
?>
