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
  class ImageLoader extends Interface {

    /**
     * Load an image
     *
     * @access  public
     * @param   &peer.URL source
     * @return  string[2] data and contenttype
     */
    function load(&$source) { }
  }
?>
