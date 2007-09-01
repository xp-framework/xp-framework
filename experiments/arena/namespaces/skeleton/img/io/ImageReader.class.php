<?php
/* This class is part of the XP framework
 *
 * $Id: ImageReader.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace img::io;

  uses('img.ImagingException');

  /**
   * Read images
   *
   * @see      xp://img.Image#loadFrom
   * @purpose  Interface
   */
  interface ImageReader {
  
    /**
     * Retrieve an image resource
     *
     * @return  resource
     * @throws  img.ImagingException
     */
    public function getResource();
  }
?>
