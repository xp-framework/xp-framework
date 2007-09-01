<?php
/* This class is part of the XP framework
 *
 * $Id: ImageLoader.class.php 10594 2007-06-11 10:04:54Z friebe $ 
 */

  namespace peer::mail::util;

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
