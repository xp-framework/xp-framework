<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Writes images
   *
   * @see      xp://img.Image#saveTo
   * @purpose  Interface
   */
  class ImageWriter extends Interface {
  
    /**
     * Sets the image resource that is to be written
     *
     * @access  public
     * @param   resource handle
     * @throws  img.ImagingException
     */
    function setResource($handle) { }
  }
?>
