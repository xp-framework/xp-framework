<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Handles the user authorization
   *
   * @purpose  Authorization
   */
  class WebdavAuthorizationHandler extends Object {
  
    /**
     * Checks if the user is authorized to do something
     *
     * @access public
     * @param  org.webdav.WebdavScriptletRequest request The request
     * @param  org.webdav.auth.WebdavUser        user    The Webdav user
     * @return bool
     */
    function isAuthorized(&$request, $user) {
      return TRUE;
    }
  
  }
?>
