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
     * Checks if the user is authorized to do something. 
     *
     * @access  public
     * @param   object handler
     * @param   string uri of actual directory
     * @param   &org.webdav.auth.WebdavUser The WebdavUser object
     * @return  bool
     */
    function isAuthorized($handler= NULL, $uri, &$user) {
      // Always return TRUE if no handler is specified
      if ($handler === NULL) return TRUE;
      return $handler->hasPermission($uri, $user);
    }
  }
?>
