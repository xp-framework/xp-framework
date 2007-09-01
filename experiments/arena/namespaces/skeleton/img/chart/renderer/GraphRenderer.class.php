<?php
/* This class is part of the XP framework
 *
 * $Id: GraphRenderer.class.php 10594 2007-06-11 10:04:54Z friebe $ 
 */

  namespace img::chart::renderer;

  /**
   * Renderer
   *
   * @see      xp://img.chart.Chart
   * @purpose  Interface
   */
  interface GraphRenderer {
  
    /**
     * Renders a chart
     *
     * @param   img.chart.Chart chart
     * @return  mixed
     */
    public function render($chart);
  }
?>
