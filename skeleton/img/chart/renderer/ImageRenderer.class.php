<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('img.Image', 'img.chart.renderer.GraphRenderer');

  /**
   * Renders charts to images
   *
   * @ext      gd
   * @see      xp://img.chart.GraphRenderer
   * @purpose  Renderer
   */
  class ImageRenderer extends Object implements GraphRenderer {
    public
      $width  = 0, 
      $height = 0;
    
    /**
     * Constructor
     *
     * @param   int width
     * @param   int height
     */
    public function __construct($width, $height) {
      $this->width= $width;
      $this->height= $height;
    }
    
    /**
     * Prepare/calculate common values
     *
     * @param  &img.chart.Chart chart The chart
     * @param  int font The font size
     * @return mixed[]
     */
    protected function _prepare($chart, $font) {
      list($lower, $upper, $step)= $chart->getRange();
      RANGE_AUTO == $lower && $lower= $chart->min();
      RANGE_AUTO == $upper && $upper= $chart->max();
      RANGE_AUTO == $step && $step= max(round(($chart->max()- $chart->min()) / 10), 1);
      $fontw= imagefontwidth($font);
      $fonth= imagefontheight($font);
      $leftBorder= 15 + max(strlen($lower), strlen($upper)) * $fontw;
      $rightBorder= $topBorder= $bottomBorder= 10;
      if (sizeof($chart->getLabels())) {
        $bottomBorder += $fonth + 5;
      }
      $innerWidth= $this->width- $leftBorder- $rightBorder;
      $innerHeight= $this->height- $topBorder- $bottomBorder;
      $zero= $this->height - $bottomBorder + ($lower / ($upper - $lower) * $innerHeight);
      if ($chart->getDisplayValues()) {
        $h= ($fonth + 10) * ($upper - $lower) / $innerHeight;
        if ($upper > 0) $upper += $h;
        if ($lower < 0) $lower += $h;
      }
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
        $zero
      );
    }
    
    /**
     * Returns an array containing the allocated colors for the passed
     * image.
     *
     * @param  &img.Image img The image
     * @param  string[] c The colors
     * @return &img.Color[]
     */
    protected function _colors($img, $c) {
      $colors= array();
      if (!is_array($c)) $c= array($c);
      foreach ($c as $color) $colors[]= $img->allocate($color);
      return $colors;
    }
    
    /**
     * Renders the skelton of a chart (e.g. axis)
     *
     * @param  mixed[] params The axis parameters
     * @param  &img.Image img The image
     * @return &img.Image
     */
    protected function _renderAxis($params, $img) {
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
      $labels= $params['labels'];
      
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
      imagesetstyle($img->handle, array(
        $params['gridColor']->handle,
        $params['gridColor']->handle,
        $params['chartbackColor']->handle,
        $params['chartbackColor']->handle
      ));
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
        if ($params['gridLines'] && ($i> $lower) && ($i<$upper)) {
          imageline(
            $img->handle,
            $leftBorder + 1,
            $y,
            $this->width - $rightBorder - 1,
            $y,
            IMG_COLOR_STYLED
          );
        }
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
        if (isset($labels[$i])) {
          imagestring(
            $img->handle,
            $font,
            $x - strlen($labels[$i]) * $fontw / 2,
            $this->height - $bottomBorder + 5,
            $labels[$i],
            $params['axisColor']->handle
          );
        }
      }
      return $img;
    }
    
    /**
     * Renders the legend box
     *
     * @param  mixed[] params The axis parameters
     * @param  &img.Image img The image
     * @return &img.Image
     */
    protected function _renderLegend($params, $img) {
      $labels= $params['labels'];
      $font= $params['font'];
      $fontw= $params['fontWidth'];
      $fonth= $params['fontHeight'];
      $rightBorder= $params['rightBorder'];
      $topBorder= $params['topBorder'];
      $margin= $params['margin'];
      $inset= 10;
      $sampleHeight= (int)($fonth * 0.6);
      
      // Get maximum string length
      $maxlen = 0;
      foreach ($labels as $label) {
        if ($maxlen < strlen($label)) $maxlen= strlen($label);
      }
      
      $xoffset= $this->width - $rightBorder - ($maxlen + 2) * $fontw - $margin * 2 - $inset;
      $yoffset= $topBorder + $inset;

      // Draw bounding box
      imagefilledrectangle(
        $img->handle,
        $xoffset + 1,
        $yoffset + 1,
        $xoffset + ($maxlen + 2) * $fontw + $margin * 2 - 1,
        $yoffset + sizeof($labels) * $fonth + $margin * 2 - 1,
        $params['legendbackColor']->handle
      );
      imagerectangle(
        $img->handle,
        $xoffset,
        $yoffset,
        $xoffset + ($maxlen + 2) * $fontw + $margin * 2,
        $yoffset + sizeof($labels) * $fonth + $margin * 2,
        $params['legendColor']->handle
      );
      
      // Write labels into box
      $xoffset += $margin;
      $yoffset += $margin;
      foreach ($labels as $i => $label) {
        imagestring(
          $img->handle,
          $font,
          $xoffset + 2 * $fontw,
          $yoffset,
          $label,
          $params['legendColor']->handle
        );
        imagefilledrectangle(
          $img->handle,
          $xoffset,
          $yoffset + ($fonth - $sampleHeight) / 2,
          $xoffset + $fontw,
          $yoffset + ($fonth + $sampleHeight) / 2,
          $params['sampleColor'][$i % sizeof($params['sampleColor'])]->handle
        );
        $yoffset += $fonth;
      }
    }
    
    /**
     * Method to render bar charts
     *
     * @param   img.chart.BarChart bc
     * @return  img.Image
     */
    public function renderBarChart($bc) {
 
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
        throw(new IllegalArgumentException('Lower range greater than upper range'));
      }

      // Figure out the distance between the bars
      if (DISTANCE_AUTO == ($distance= $bc->getDistance())) {
        $distance= $innerWidth / $count;
      }

      // Create image
      with ($img= Image::create($this->width, $this->height)); {
        $colors= $this->_colors($img, $bc->getColor('sample'));
        $this->_renderAxis(array(
          'count'           => $count,
          'distance'        => $distance,
          'lower'           => $lower,
          'upper'           => $upper,
          'step'            => $step,
          'backgroundColor' => $img->allocate($bc->getColor('background')),
          'axisColor'       => $axisColor= $img->allocate($bc->getColor('axis')),
          'chartbackColor'  => $img->allocate($bc->getColor('chartback')),
          'gridColor'       => $img->allocate($bc->getColor('grid')),
          'leftBorder'      => $leftBorder,
          'rightBorder'     => $rightBorder,
          'topBorder'       => $topBorder,
          'bottomBorder'    => $bottomBorder,
          'font'            => $font,
          'fontWidth'       => $fontw,
          'fontHeight'      => $fonth,
          'labels'          => $bc->getLabels(),
          'gridLines'       => $bc->getGridLines()
        ), $img);
        
        $bc->getDisplayLegend() && $this->_renderLegend(array(
          'labels'          => array_map(create_function('$a', 'return $a->name;'), $bc->series),
          'font'            => $font,
          'fontWidth'       => $fontw,
          'fontHeight'      => $fonth,
          'rightBorder'     => $rightBorder,
          'topBorder'       => $topBorder,
          'margin'          => 5,
          'legendColor'     => $img->allocate($bc->getColor('legend')),
          'legendbackColor' => $img->allocate($bc->getColor('legendback')),
          'sampleColor'     => $colors
        ), $img);
        
        // Draw bars
        $barWidth= $bc->getAccumulated()
          ? $width
          : $width / $seriesCount;
        $py= $v= array();
        for ($i= 0; $i < $count; $i++) {
          for ($j= 0; $j < $seriesCount; $j++) {
            $offset= $leftBorder + ($i + 0.5) * $distance - $width / 2;
            $h= ($bc->series[$j]->values[$i] / ($upper - $lower) * $innerHeight);
            if ($bc->getAccumulated()) {
              if (!isset($py[$i])) $py[$i]= $zero;
              imagefilledrectangle(
                $img->handle,
                $offset,
                $py[$i] - $h,
                $offset + $barWidth,
                $py[$i],
                $colors[$j % sizeof($colors)]->handle
              );
              $py[$i] -= $h;
              $v[$i] += $bc->series[$j]->values[$i];
              if ($bc->getDisplayValues() && ($j == $seriesCount -1)) {
                imagestring(
                  $img->handle,
                  $font,
                  $offset + ($barWidth - $fontw * strlen($v[$i])) / 2,
                  $py[$i] - $fonth - 5,
                  $v[$i],
                  $axisColor->handle
                );
              }
            } else {
              $offset += $barWidth * $j;
              imagefilledrectangle(
                $img->handle,
                $offset,
                $h < 0 ? $zero : $zero - $h,
                $offset + $barWidth - 1,
                $h < 0 ? min($zero - $h, $this->height- $bottomBorder) : $zero,
                $colors[$j % sizeof($colors)]->handle
              );
              if ($bc->getDisplayValues()) {
                imagestring(
                  $img->handle,
                  $font,
                  $offset + ($barWidth - $fontw * strlen($bc->series[$j]->values[$i])) / 2,
                  ($h < 0 ? $zero : $zero - $h) - $fonth - 5,
                  $bc->series[$j]->values[$i],
                  $axisColor->handle
                );
              }
            }
          }
        }
      }
      return $img;
    }
    
    /**
     * Method to render line charts
     *
     * @param   img.chart.LineChart bc
     * @return  img.Image
     */
    public function renderLineChart($lc) {
 
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
        throw(new IllegalArgumentException('Lower range greater than upper range'));
      }

      // Figure out the distance between the bars
      if (DISTANCE_AUTO == ($distance= $lc->getDistance())) {
        $distance= $innerWidth / $count;
      }

      // Create image
      with ($img= Image::create($this->width, $this->height)); {
        $colors= $this->_colors($img, $lc->getColor('sample'));
        $this->_renderAxis(array(
          'count'           => $count,
          'distance'        => $distance,
          'lower'           => $lower,
          'upper'           => $upper,
          'step'            => $step,
          'backgroundColor' => $img->allocate($lc->getColor('background')),
          'axisColor'       => $axisColor= $img->allocate($lc->getColor('axis')),
          'chartbackColor'  => $img->allocate($lc->getColor('chartback')),
          'gridColor'       => $img->allocate($lc->getColor('grid')),
          'leftBorder'      => $leftBorder,
          'rightBorder'     => $rightBorder,
          'topBorder'       => $topBorder,
          'bottomBorder'    => $bottomBorder,
          'font'            => $font,
          'fontWidth'       => $fontw,
          'fontHeight'      => $fonth,
          'labels'          => $lc->getLabels(),
          'gridLines'       => $lc->getGridLines()
        ), $img);
        
        $lc->getDisplayLegend() && $this->_renderLegend(array(
          'labels'          => array_map(create_function('$a', 'return $a->name;'), $lc->series),
          'font'            => $font,
          'fontWidth'       => $fontw,
          'fontHeight'      => $fonth,
          'rightBorder'     => $rightBorder,
          'topBorder'       => $topBorder,
          'margin'          => 5,
          'legendColor'     => $img->allocate($lc->getColor('legend')),
          'legendbackColor' => $img->allocate($lc->getColor('legendback')),
          'sampleColor'     => $colors
        ), $img);
        
        // Draw lines
        $x= $y= $py= array();
        for ($i= 0; $i < $count; $i++) {
          for ($j= 0; $j < $seriesCount; $j++) {
            $offset= $leftBorder + ($i + 0.5) * $distance;
            $h= ($lc->series[$j]->values[$i] / ($upper - $lower) * $innerHeight);
            $xp= $i > 0 ? $x[$j][$i - 1] : $offset;
            $yp= $i > 0 ? $y[$j][$i - 1] : @$py[$i] + $h;
            if ($lc->getAccumulated()) {
              if (!isset($py[$i])) $py[$i]= 0;
              imageline(
                $img->handle,
                $xp,
                $zero - $yp,
                $offset,
                $zero - $py[$i] - $h,
                $colors[$j % sizeof($colors)]->handle
              );
              $v[$i] += $lc->series[$j]->values[$i];
              if ($lc->getDisplayValues() && ($j == $seriesCount -1)) {
                imagestring(
                  $img->handle,
                  $font,
                  $offset - ($fontw * strlen($v[$i])) / 2,
                  $zero - $py[$i] - $h - $fonth - 5,
                  $v[$i],
                  $axisColor->handle
                );
              }
              $hold= $h;
              $h+= $py[$i];
              $py[$i] += $hold;
            } else {
              imageline(
                $img->handle,
                $xp,
                $zero - $yp,
                $offset,
                $zero - $h,
                $colors[$j % sizeof($colors)]->handle
              );
              if ($lc->getDisplayValues()) {
                imagestring(
                  $img->handle,
                  $font,
                  $offset + ($fontw * strlen($lc->series[$j]->values[$i])) / 2,
                  $zero - $h - $fonth - 5,
                  $lc->series[$j]->values[$i],
                  $axisColor->handle
                );
              }
            }
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
     * @param   img.chart.PieChart bc
     * @return  img.Image
     */
    public function renderPieChart($pc) {
 
      // Create local variables for faster access
      $border= 50;
      $sum= $pc->sum();
      $innerHeight= $this->height - $border * 2;
      $innerWidth= $this->width - $border * 2;
      $middleX= $this->width / 2;
      $middleY= $this->height / 2;
      $count= $pc->count();
      $font= 2;
      $fontw= imagefontwidth($font);
      $fonth= imagefontheight($font);

      // Create image
      with ($img= Image::create($this->width, $this->height)); {
        $colors= $this->_colors($img, $pc->getColor('sample'));
        $axisColor= $img->allocate($pc->getColor('axis'));

        // Flood fill with background color
        $img->fill($img->allocate($pc->getColor('chartback')), $leftBorder+ 1, $topBorder+ 1);
        
        $pc->getDisplayLegend() && $this->_renderLegend(array(
          'labels'          => $pc->getLabels(),
          'font'            => $font,
          'fontWidth'       => $fontw,
          'fontHeight'      => $fonth,
          'rightBorder'     => $border / 5,
          'topBorder'       => $border / 5,
          'margin'          => 5,
          'legendColor'     => $img->allocate($pc->getColor('legend')),
          'legendbackColor' => $img->allocate($pc->getColor('legendback')),
          'sampleColor'     => $colors
        ), $img);

        $start= $end= 0;
        for ($i= 0; $i < $count; $i++) {
          $end+= $pc->series[0]->values[$i];
          $angle= deg2rad(90 - ($start + ($end - $start) / 2) / $sum * 360);
          $insetX= sin($angle) * $pc->getValueInset($i);
          $insetY= cos($angle) * $pc->getvalueInset($i);
          imagefilledarc(
            $img->handle,
            $middleX + $insetX,
            $middleY + $insetY,
            $innerWidth,
            $innerHeight,
            $start / $sum * 360,
            $end / $sum * 360,
            $colors[$i % sizeof($colors)]->handle,
            IMG_ARC_PIE
          );
          if ($pc->getDisplayValues()) {
            imagestring(
              $img->handle,
              $font,
              $middleX + $insetX + sin($angle) * $innerWidth / 3 - strlen($pc->series[0]->values[$i]) * $fontw / 2,
              $middleY + $insetY + cos($angle) * $innerHeight / 3 - $fonth / 2,
              $pc->series[0]->values[$i],
              $axisColor->handle
            );
          }
          $start= $end;
        }
      }
      return $img;
    }

    /**
     * Renders a chart
     *
     * @param   img.chart.Chart chart
     * @return  img.Image
     * @throws  lang.IllegalArgumentException if chart is not renderable
     */
    public function render($chart) { 
    
      // Method overloading by delegation
      if (!is('Chart', $chart) || !method_exists($this, $method= 'render'.get_class($chart))) {
        throw(new IllegalArgumentException('Cannot render '.xp::typeOf($chart).'s'));
      }
      return $this->{$method}($chart);
    }

  } 
?>
