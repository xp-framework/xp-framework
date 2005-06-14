<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('img.chart.Chart');

  /**
   * Pie chart
   *
   * @see      xp://img.chart.Chart
   * @purpose  Chart
   */
  class PieChart extends Chart {

    /**
     * Helper method which returns the sum from all values
     *
     * @access  public
     * @return  float
     */
    function sum() {
      $sum= 0;
      for ($i= 0, $s= sizeof($this->series[0]->values); $i < $s; $i++) {
        $sum+= $this->series[0]->values[$i];
      }
      return $sum;
    }
  }
?>
