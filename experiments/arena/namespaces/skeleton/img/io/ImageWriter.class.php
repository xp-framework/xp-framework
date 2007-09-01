<?php
/* This class is part of the XP framework
 *
 * $Id: ImageWriter.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace img::io;

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
