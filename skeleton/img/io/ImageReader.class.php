<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('img.ImagingException');

  /**
   * Read images
   *
   * @see      xp://img.Image#loadFrom
   * @purpose  Interface
   */
  class ImageReader extends Interface {
  
    /**
     * Retrieve an image resource
     *
     * @access  public
     * @return  resource
     * @throws  img.ImagingException
     */
    function getResource() { }
  }
?>
