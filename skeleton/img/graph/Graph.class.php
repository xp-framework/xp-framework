<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses('img.Image', 'img.Color');
  
  /**
   * Graph class
   *
   * @see img.Image
   */
  class Graph extends Image {
  
    /**
     * Draws an object
     *
     * @access  public
     * @param   img.DrawableGraphObject graphObject
     * @return  mixed the return value of graphObject's draw function
     */
    function draw(&$graphObject) {
      return $graphObject->draw($this->_hdl, $this->getWidth(), $this->getHeight());
    }
  }
?>
