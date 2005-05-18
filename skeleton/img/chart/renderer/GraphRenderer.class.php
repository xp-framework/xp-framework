<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Renderer
   *
   * @see      xp://img.chart.Chart
   * @purpose  Interface
   */
  class GraphRenderer extends Interface {
  
    /**
     * Renders a chart
     *
     * @access  public
     * @param   &img.chart.Chart chart
     * @return  &mixed
     */
    function &render(&$chart) { }
  }
?>
