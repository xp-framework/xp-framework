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
     * Method to render bar charts
     *
     * @access  protected
     * @param   img.chart.BarChart bc
     * @return  &img.Image
     */
    function &renderBarChart(&$bc) {
 
      // Create local variables for faster access
      $max= $bc->max();
      $min= $bc->min();
      $width= $bc->getBarWidth();
      $count= $bc->count();
      
      // Calculate ranges and stepping
      list($lower, $upper, $step)= $bc->getRange();
      RANGE_AUTO == $lower && $lower= $min;
      RANGE_AUTO == $upper && $upper= $max;
      RANGE_AUTO == $step && $step= round(($max- $min) / 10);

      // Sanity checks
      if ($lower > $upper) {
        return throw(new IllegalArgumentException('Lower range greater than upper range'));
      }

      // Create image
      with ($img= &Image::create($this->width, $this->height)); {
      
        // Flood fill with background color
        $img->fill($img->allocate($bc->getColor('background')));
      
        $leftBorder= 30;
        $rightBorder= $topBorder= $bottomBorder= 10;
        
        // Calculate inner borders
        $innerWidth= $this->width- $leftBorder- $rightBorder;
        $innerHeight= $this->height- $topBorder- $bottomBorder;
        $color= &$img->allocate($bc->getColor('axis'));
        imagerectangle(
          $img->handle,
          $leftBorder,
          $topBorder,
          $this->width - $rightBorder,
          $this->height - $bottomBorder,
          $color->handle
        );

        // Flood fill with background color
        $img->fill($img->allocate($bc->getColor('chartback')), $leftBorder+ 1, $topBorder+ 1);
        
        // Calculate zero line
        $zero= $this->height - $bottomBorder + ($lower / ($upper - $lower) * $innerHeight);
        imageline(
          $img->handle,
          $leftBorder,
          $zero,
          $this->width - $rightBorder,
          $zero,
          $color->handle
        );

        // Draw Y axis scale
        $font= 2;
        $fontw= imagefontwidth($font);
        $fonth= imagefontheight($font);
        for ($i= $lower; $i <= $upper; $i+= $step) {
          $y= $zero - ($i / ($upper - $lower) * $innerHeight);
          imageline(
            $img->handle,
            $leftBorder - 5,
            $y,
            $leftBorder,
            $y,
            $color->handle
          );
          imagestring(
            $img->handle,
            $font,
            $leftBorder - 10 - $fontw * strlen($i),
            $y - $fonth / 2,
            $i,
            $color->handle
          );
        }
        
        // Figure out the distance between the bars
        if (DISTANCE_AUTO == ($distance= $bc->getDistance())) {
          $distance= $innerWidth / $count;
        }
        
        // Draw bars
        $color= &$img->allocate($bc->getColor('sample'));
        for ($i= 0; $i < $count; $i++) {
          $h= ($bc->series[0]->values[$i] / ($upper - $lower) * $innerHeight);
          imagefilledrectangle(
            $img->handle,
            $leftBorder + $i * $distance,
            $h < 0 ? $zero : $zero - $h,
            $leftBorder + $i * $distance + $width,
            $h < 0 ? min($zero - $h, $this->height- $bottomBorder) : $zero,
            $color->handle
          );
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
