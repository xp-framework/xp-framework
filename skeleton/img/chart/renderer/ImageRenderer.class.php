<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('img.Image');

  /**
   * Renders charts to images
   *
   * @ext      gd
   * @see      xp://img.chart.GraphRenderer
   * @purpose  Renderer
   */
  class ImageRenderer extends Object {
    var
      $width  = 0, 
      $height = 0;
    
    /**
     * Constructor
     *
     * @access  public
     * @param   int width
     * @param   int height
     */
    function __construct($width, $height) {
      $this->width= $width;
      $this->height= $height;
    }
    
    /**
     * Prepare/calculate common values
     *
     * @access private
     * @param img.chart.Chart &chart The chart
     * @param int font The font size
     * @return mixed[]
     */
    function _prepare(&$chart, $font) {
      list($lower, $upper, $step)= $chart->getRange();
      RANGE_AUTO == $lower && $lower= $chart->min();
      RANGE_AUTO == $upper && $upper= $chart->max();
      RANGE_AUTO == $step && $step= max(round(($chart->max()- $chart->min()) / 10), 1);
      $fontw= imagefontwidth($font);
      $fonth= imagefontheight($font);
      $leftBorder= 15 + max(strlen($lower), strlen($upper)) * $fontw;
      $rightBorder= $topBorder= $bottomBorder= 10;
      $innerWidth= $this->width- $leftBorder- $rightBorder;
      $innerHeight= $this->height- $topBorder- $bottomBorder;
      return array(
      
        // Common values
        $chart->max(), $chart->min(),
        $chart->count(), $chart->seriesCount(),
        
        // Ranges and stepping
        $lower, $upper, $step,
        
        // Font
        $fontw, $fonth,
        
        // Borders (left, right, top, bottom)
        $leftBorder, $rightBorder,
        $topBorder, $bottomBorder,

        // Calculate inner borders
        $innerWidth,
        $innerHeight,
        
        // Calculate zero line
        $this->height - $bottomBorder + ($lower / ($upper - $lower) * $innerHeight)
      );
    }
    
    /**
     * Returns an array containing the allocated colors for the passed
     * image.
     *
     * @access private
     * @param img.Image &img The image
     * @param string[] c The colors
     * @return &img.Color[]
     */
    function &_colors(&$img, $c) {
      $colors= array();
      if (!is_array($c)) $c= array($c);
      foreach ($c as $color) $colors[]= $img->allocate($color);
      return $colors;
    }
    
    /**
     * Renders the skelton of a chart (e.g. axis)
     *
     * @access private
     * @param mixed[] params The axis parameters
     * @param img.Image &img The image
     * @return &img.Image
     */
    function &_renderAxis($params, &$img) {
      $count= $params['count'];
      $distance= $params['distance'];
      $lower= $params['lower'];
      $upper= $params['upper'];
      $step= $params['step'];
      $leftBorder= $params['leftBorder'];
      $rightBorder= $params['rightBorder'];
      $topBorder= $params['topBorder'];
      $bottomBorder= $params['bottomBorder'];
      $font= $params['font'];
      $fontw= $params['fontWidth'];
      $fonth= $params['fontHeight'];

      // Flood fill with background color
      $img->fill($params['backgroundColor']);

      // Calculate inner borders
      $innerWidth= $this->width- $leftBorder- $rightBorder;
      $innerHeight= $this->height- $topBorder- $bottomBorder;
      imagerectangle(
        $img->handle,
        $leftBorder,
        $topBorder,
        $this->width - $rightBorder,
        $this->height - $bottomBorder,
        $params['axisColor']->handle
      );

      // Flood fill with background color
      $img->fill($params['chartbackColor'], $leftBorder+ 1, $topBorder+ 1);

      // Calculate zero line
      $zero= $this->height - $bottomBorder + ($lower / ($upper - $lower) * $innerHeight);
      imageline(
        $img->handle,
        $leftBorder,
        $zero,
        $this->width - $rightBorder,
        $zero,
        $params['axisColor']->handle
      );

      // Draw Y axis scale
      for ($i= $lower; $i <= $upper; $i+= $step) {
        $y= $zero - ($i / ($upper - $lower) * $innerHeight);
        imageline(
          $img->handle,
          $leftBorder - 5,
          $y,
          $leftBorder,
          $y,
          $params['axisColor']->handle
        );
        imagestring(
          $img->handle,
          $font,
          $leftBorder - 10 - $fontw * strlen($i),
          $y - $fonth / 2,
          $i,
          $params['axisColor']->handle
        );
      }

      // Draw X axis
      for ($i= 0; $i < $count; $i++) {
        $x= $leftBorder + $i * $distance + $distance / 2;
        imageline(
          $img->handle,
          $x,
          $this->height - $bottomBorder,
          $x,
          $this->height - $bottomBorder + 5,
          $params['axisColor']->handle
        );
      }
      return $img;
    }
    
    /**
     * Method to render bar charts
     *
     * @access  protected
     * @param   img.chart.BarChart bc
     * @return  &img.Image
     */
    function &renderBarChart(&$bc) {
 
      // Create local variables for faster access
      list(
        $max, $min,
        $count, $seriesCount,
        $lower, $upper, $step,
        $fontw, $fonth,
        $leftBorder, $rightBorder, $topBorder, $bottomBorder,
        $innerWidth, $innerHeight,
        $zero,
      )= $this->_prepare($bc, $font= 2);
      $width= $bc->getBarWidth();
      
      // Sanity checks
      if ($lower > $upper) {
        return throw(new IllegalArgumentException('Lower range greater than upper range'));
      }

      // Figure out the distance between the bars
      if (DISTANCE_AUTO == ($distance= $bc->getDistance())) {
        $distance= round($innerWidth / $count);
      }

      // Create image
      with ($img= &Image::create($this->width, $this->height)); {
        $this->_renderAxis(array(
          'count'           => $count,
          'distance'        => $distance,
          'lower'           => $lower,
          'upper'           => $upper,
          'step'            => $step,
          'backgroundColor' => $img->allocate($bc->getColor('background')),
          'axisColor'       => $img->allocate($bc->getColor('axis')),
          'chartbackColor'  => $img->allocate($bc->getColor('chartback')),
          'leftBorder'      => $leftBorder,
          'rightBorder'     => $rightBorder,
          'topBorder'       => $topBorder,
          'bottomBorder'    => $bottomBorder,
          'font'            => $font,
          'fontWidth'       => $fontw,
          'fontHeight'      => $fonth
        ), $img);
        
        // Draw bars
        $barWidth= $width / $seriesCount;
        $colors= $this->_colors($img, $bc->getColor('sample'));
        for ($i= 0; $i < $count; $i++) {
          for ($j= 0; $j < $seriesCount; $j++) {
            $offset= $leftBorder + ($i + 0.5) * $distance - $width / 2 + $barWidth * $j;
            $h= ($bc->series[$j]->values[$i] / ($upper - $lower) * $innerHeight);
            imagefilledrectangle(
              $img->handle,
              $offset,
              $h < 0 ? $zero : $zero - $h,
              $offset + $barWidth,
              $h < 0 ? min($zero - $h, $this->height- $bottomBorder) : $zero,
              $colors[$j % sizeof($colors)]->handle
            );
          }
        }
      }
      return $img;
    }
    
    /**
     * Method to render line charts
     *
     * @access  protected
     * @param   img.chart.LineChart bc
     * @return  &img.Image
     */
    function renderLineChart($lc) {
 
      // Create local variables for faster access
      list(
        $max, $min,
        $count, $seriesCount,
        $lower, $upper, $step,
        $fontw, $fonth,
        $leftBorder, $rightBorder, $topBorder, $bottomBorder,
        $innerWidth, $innerHeight,
        $zero
      )= $this->_prepare($lc, $font= 2);

      // Sanity checks
      if ($lower > $upper) {
        return throw(new IllegalArgumentException('Lower range greater than upper range'));
      }

      // Figure out the distance between the bars
      if (DISTANCE_AUTO == ($distance= $lc->getDistance())) {
        $distance= round($innerWidth / $count);
      }

      // Create image
      with ($img= &Image::create($this->width, $this->height)); {
        $this->_renderAxis(array(
          'count'           => $count,
          'distance'        => $distance,
          'lower'           => $lower,
          'upper'           => $upper,
          'step'            => $step,
          'backgroundColor' => $img->allocate($lc->getColor('background')),
          'axisColor'       => $img->allocate($lc->getColor('axis')),
          'chartbackColor'  => $img->allocate($lc->getColor('chartback')),
          'leftBorder'      => $leftBorder,
          'rightBorder'     => $rightBorder,
          'topBorder'       => $topBorder,
          'bottomBorder'    => $bottomBorder,
          'font'            => $font,
          'fontWidth'       => $fontw,
          'fontHeight'      => $fonth
        ), $img);
        
        // Draw bars
        $x= $y= array();
        $colors= $this->_colors($img, $lc->getColor('sample'));
        for ($i= 0; $i < $count; $i++) {
          for ($j= 0; $j < $seriesCount; $j++) {
            $offset= $leftBorder + ($i + 0.5) * $distance - $width / 2 + $barWidth * $j;
            $h= ($lc->series[$j]->values[$i] / ($upper - $lower) * $innerHeight);
            imageline(
              $img->handle,
              isset($x[$j][sizeof($x[$j]) - 1]) ? $x[$j][sizeof($x[$j]) - 1] : $offset,
              isset($y[$j][sizeof($y[$j]) - 1]) ? $zero - $y[$j][sizeof($y[$j]) - 1] : $zero - $h,
              $offset,
              $zero - $h,
              $colors[$j % sizeof($colors)]->handle
            );
            $x[$j][]= $offset;
            $y[$j][]= $h;
          }
        }
      }
      return $img;
    }
  
    /**
     * Method to render pie charts
     *
     * @access  protected
     * @param   img.chart.PieChart bc
     * @return  &img.Image
     */
    function renderPieChart($pc) {
 
      // Create local variables for faster access
      $border= 50;
      $sum= $pc->sum();
      $innerHeight= $this->height - $border * 2;
      $innerWidth= $this->width - $border * 2;
      $middleX= $this->width / 2;
      $middleY= $this->height / 2;
      $count= $pc->count();

      // Create image
      with ($img= &Image::create($this->width, $this->height)); {
        $colors= $this->_colors($img, $pc->getColor('sample'));

        // Flood fill with background color
        $img->fill($img->allocate($pc->getColor('chartback')), $leftBorder+ 1, $topBorder+ 1);
        
        $start= $end= 0;
        for ($i= 0; $i < $count; $i++) {
          $end+= $pc->series[0]->values[$i];
          imagefilledarc(
            $img->handle,
            $middleX,
            $middleY,
            $innerWidth,
            $innerHeight,
            $start / $sum * 360,
            $end / $sum * 360,
            $colors[$i % sizeof($colors)]->handle,
            IMG_ARC_PIE
          );
          $start= $end;
        }
      }
      return $img;
    }

    /**
     * Renders a chart
     *
     * @access  public
     * @param   &img.chart.Chart chart
     * @return  &img.Image
     * @throws  lang.IllegalArgumentException if chart is not renderable
     */
    function &render(&$chart) { 
    
      // Method overloading by delegation
      if (!is_a($chart, 'Chart') || !method_exists($this, $method= 'render'.get_class($chart))) {
        return throw(new IllegalArgumentException('Cannot render '.xp::typeOf($chart).'s'));
      }
      return $this->{$method}($chart);
    }

  } implements(__FILE__, 'img.chart.renderer.GraphRenderer');
?>
