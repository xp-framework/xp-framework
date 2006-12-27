<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('img.ImagingException');

  /**
   * Writes images
   *
   * @see      xp://img.Image#saveTo
   * @purpose  Interface
   */
  interface ImageWriter {
  
    /**
     * Sets the image resource that is to be written
     *
     * @param   resource handle
     * @throws  img.ImagingException
     */
    public function setResource($handle);
  }
?>
