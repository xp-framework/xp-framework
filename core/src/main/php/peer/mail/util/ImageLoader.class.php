<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Image loader
   *
   * @see      xp://peer.mail.util.HtmlMessage
   * @purpose  Interface
   */
  interface ImageLoader {

    /**
     * Load an image
     *
     * @param   peer.URL source
     * @return  string[2] data and contenttype
     */
    public function load($source);
  }
?>
