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
     * @param   class handler
     * @param   string uri of actual directory
     * @param   string username
     * @return  bool
     */
    function isAuthorized($handler= NULL, $uri, $username) {
      // Always return TRUE if no handler is specified
      if ($handler === NULL) return TRUE;
      return $handler->hasPermission($uri, $username);
    }
  }
?>
