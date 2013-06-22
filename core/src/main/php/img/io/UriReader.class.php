<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  $package= 'img.io';

  /**
   * Reads images from URIs
   */
  interface img·io·UriReader {

    /**
     * Read image
     *
     * @param   string uri
     * @return  resource
     * @throws  img.ImagingException
     */
    public function readImageFromUri($uri);
  }
?>
