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
      with ($img= &Image::create($this->width, $this->height)); {
      
        // Flood fill with background color
        $img->fill($img->allocate($bc->getColor('background')));
      
        $leftBorder= $rightBorder= $topBorder= $bottomBorder= 10;
        
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
        
        // Create local variables for faster access
        $max= $bc->max();
        $width= $bc->barWidth();
        $count= $bc->count();
        
        // Figure out the distance between the bars
        if (DISTANCE_AUTO == ($distance= $bc->getDistance())) {
          $distance= $innerWidth / $count;
        }

        // Draw bars
        $color= &$img->allocate($bc->getColor('sample'));
        for ($i= 0; $i < $count; $i++) {
          imagefilledrectangle(
            $img->handle,
            $leftBorder + $i * $distance,
            $this->height - $bottomBorder - ($bc->series[0]->values[$i] / $max * $innerHeight),
            $leftBorder + $i * $distance + $width,
            $this->height - $bottomBorder,
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
