<?php
/* This class is part of the XP framework
 *
 * $Id: Graph.class.php 10594 2007-06-11 10:04:54Z friebe $
 */

  namespace img::graph;
 
  uses('img.Image');
  
  /**
   * Graph class
   *
   * @deprecated
   * @see      xp://img.Image
   * @purpose  Base class for all graphs
   */
  class Graph extends img::Image {

    /**
     * Creates a new blank image in memory
     *
     * @param   int w width
     * @param   int h height
     * @param   int type default IMG_PALETTE either IMG_PALETTE or IMG_TRUECOLOR
     * @return  img.graph.Graph
     * @throws  img.ImagingException in case the image could not be created
     */
    public static function create($w, $h, $type= ) {
      return parent::create($w, $h, $type, __CLASS__);
    }
  }
?>
