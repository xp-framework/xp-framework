<?php
/* This class is part of the XP framework
 *
 * $Id: PieChart.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace img::chart;

  uses('img.chart.Chart');

  /**
   * Pie chart
   *
   * @see      xp://img.chart.Chart
   * @purpose  Chart
   */
  class PieChart extends Chart {

    public
      $valinset= array();
    
    /**
     * Helper method which returns the sum from all values
     *
     * @return  float
     */
    public function sum() {
      $sum= 0;
      for ($i= 0, $s= sizeof($this->series[0]->values); $i < $s; $i++) {
        $sum+= $this->series[0]->values[$i];
      }
      return $sum;
    }
    
    /**
     * Sets inset for the specified item
     *
     * @param int item The item index
     */
    public function setValueInset($item, $inset= 10) {
      $this->valinset[$item]= $inset;
    }
    
    /**
     * Returns the inset for the specified item
     *
     * @param int item The item index
     * @return int
     */
    public function getValueInset($item) {
      return isset($this->valinset[$item]) ? $this->valinset[$item] : 0;
    }
  }
?>
