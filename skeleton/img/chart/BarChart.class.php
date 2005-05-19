<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('img.chart.Chart');

  define('CHART_HORIZONTAL',  0x0000);
  define('CHART_VERTICAL',    0x0001);
  
  define('DISTANCE_AUTO',     -1);
  
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
      $distance  = DISTANCE_AUTO;

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
     * Returns the width of one bar in this bar chart
     *
     * @access  public
     * @return  int
     */
    function barWidth() {
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
