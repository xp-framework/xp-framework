<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'img.Image',
    'img.Color'
  );

  /**
   * Graph class
   *
   * Example [creating a piechart]:
   * <code>
   *   uses(
   *     'img.graph.Graph',
   *     'img.graph.PieChart',
   *     'img.Color',
   *     'img.PngImage',
   *     'io.File'
   *   );
   *   
   *   $g= new Graph(400, 400);
   *   $g->create();
   *   $colors= array(
   *     'white'     => $g->allocate(new Color('#ffffff')),
   *     'red'       => $g->allocate(new Color('#ff0000')),
   *     'darkred'   => $g->allocate(new Color('#990000')),
   *     'blue'      => $g->allocate(new Color('#0000ff')),
   *     'darkblue'  => $g->allocate(new Color('#000099')),
   *     'green'     => $g->allocate(new Color('#00ff00')),
   *     'darkgreen' => $g->allocate(new Color('#009900')),
   *     'orange'    => $g->allocate(new Color('#ffff00')),
   *     'brown'     => $g->allocate(new Color('#999900')),
   *     'violet'    => $g->allocate(new Color('#ff00ff')),
   *     'purple'    => $g->allocate(new Color('#990099')),
   *   );
   *   
   *   $g->draw(new PieChart(array(
   *     'first'  => new PieSlice(10.0, array($colors['red'], $colors['darkred'])),
   *     'second' => new PieSlice(60.0, array($colors['blue'], $colors['darkblue'])),
   *     'third'  => new PieSlice( 6.0, array($colors['green'], $colors['darkgreen'])),
   *     'fourth' => new PieSlice( 4.0, array($colors['orange'], $colors['brown'])),
   *     'fifth'  => new PieSlice(12.0, array($colors['violet'], $colors['purple'])),
   *   ), 100));
   *   
   *   $i= new PngImage($g->getWidth(), $g->getHeight());
   *   $i->create();
   *   $i->copyFrom($g);
   *   try(); {
   *     $f= $i->toFile(new File('out.png'));
   *   } if (catch('ImagingException', $e)) {
   *     $e->printStackTrace();
   *     exit;
   *   }
   * </code>
   *
   * @see      xp://img.Image
   * @purpose  Base class for all graphs
   */
  class Graph extends Image {
  
    /**
     * Draws an object
     *
     * @access  public
     * @param   img.DrawableGraphObject graphObject
     * @return  mixed the return value of graphObject's draw function
     */
    public function draw(&$graphObject) {
      return $graphObject->draw($this->_hdl, self::getWidth(), self::getHeight());
    }
  }
?>
