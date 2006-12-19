<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('img.Color', 'img.chart.Series');

  // Distance
  define('DISTANCE_AUTO',     -1);
  
  // Ranges
  define('RANGE_AUTO',        -1);
  
  /**
   * Chart
   *
   * <code>
   *   // Construct a barchart
   *   $chart= &new BarChart();
   *   $chart->add(new Series('April', array(1, 2, 4, 10, 11, 28)));
   *
   *   // Render it with an ImageRenderer and save to chart.png
   *   $ir= &new ImageRenderer(400, 400);
   *   $image= &$ir->render($chart);
   *   $image->saveTo(new PngStreamWriter(new File('chart.png')));
   *
   *   // Render it with an SVG renderer and save to chart.svg
   *   $sr= &new SVGRenderer();
   *   $svg= $sr->render($chart);
   *   FileUtil::setContents(new File('chart.svg'), $svg);
   * </code>
   *
   * @purpose  Base class for charts
   */
  class Chart extends Object {
    public
      $series = array(),
      $colors = array(),
      $theme  = array(
        'background'  => '#ffffff',
        'chartback'   => '#efefef',
        'sample'      => '#990000',
        'axis'        => '#000000',
        'grid'        => '#888888',
        'legend'      => '#444444',
        'legendback'  => '#ffffff'
      ),
      $gridlines = FALSE,
      $labels    = array(),
      $displeg   = FALSE,
      $dispval   = FALSE;

    /**
     * Add a series of data
     *
     * @access  public
     * @param   &img.graph.Series series
     * @return  &img.graph.Series
     */
    public function &add(&$series) {
      $this->series[]= &$series;
      return $series;
    }
    
    /**
     * Sets chart's background color
     *
     * @access  public
     * @param   &img.Color color
     */
    public function setBackgroundColor(&$color) {
      $this->setColor('background', $color);
    }
    
    /**
     * Set a color for a specified key
     *
     * @access  public
     * @param   string key
     * @param   &img.Color color
     */
    public function setColor($key, &$color) {
      $this->colors[$key]= &$color;
    }
    
    /**
     * Returns a color by a name
     *
     * @access  public
     * @param   string key
     * @return  &img.Color
     */
    public function &getColor($key) {
      if (!isset($this->colors[$key])) return new Color($this->theme[$key]);
      return $this->colors[$key];
    }
    
    /**
     * Sets series labels
     *
     * @access public
     * @param string[] labels The series labels
     */
    public function setLabels($labels) {
      $this->labels= $labels;
    }
    
    /**
     * Returns labels for series as array
     *
     * @access public
     * @return string[]
     */
    public function getLabels() {
      return $this->labels;
    }
    
    /**
     * Sets grid on or off
     +
     * @access public
     * @param bool gridlines Draw grid lines
     */
    public function setGridLines($gridlines) {
      $this->gridlines= $gridlines;
    }
    
    /**
     * Returns if grid should be drawn or not
     *
     * @access public
     * @return bool
     */
    public function getGridLines() {
      return $this->gridlines;
    }
    
    /**
     * Set flag to display a legend
     * 
     * @access public
     * @param bool bool The flag
     */
    public function setDisplayLegend($bool) {
      $this->displeg= $bool;
    }
    
    /**
     * Returns the flag to display a legend
     *
     * @access public
     * @return bool
     */
    public function getDisplayLegend() {
      return $this->displeg;
    }
    
    /**
     * Set flag to display values
     *
     * @access public
     * @return bool
     */
    public function getDisplayValues() {
      return $this->dispval;
    }
    
    /**
     * Returns flag to display values
     *
     * @access public
     * @param bool bool The flag
     */
    public function setDisplayValues($bool) {
      $this->dispval= $bool;
    }
    
    /**
     * Returns the number of elements in the longest series
     *
     * @access  public
     * @return  int
     */
    public function count() {
      $max= 0;
      for ($i= 0, $s= sizeof($this->series); $i < $s; $i++) {
        $max= max($max, sizeof($this->series[$i]->values));
      }
      return $max;
    }
    
    /**
     * Returns the number of series
     *
     * @access public
     * @return int
     */
    public function seriesCount() {
      return sizeof($this->series);
    }
    
    /**
     * Helper method which returns the largest value from all series
     *
     * @access  public
     * @return  float
     */
    public function max() {
      $max= 0;
      for ($i= 0, $s= sizeof($this->series); $i < $s; $i++) {
        $max= max($max, max($this->series[$i]->values));
      }
      return $max;
    }

    /**
     * Helper method which returns the smallest value from all series
     *
     * @access  public
     * @return  float
     */
    public function min() {
      $min= 0;
      for ($i= 0, $s= sizeof($this->series); $i < $s; $i++) {
        $min= min($min, min($this->series[$i]->values));
      }
      return $min;
    }
  }
?>
