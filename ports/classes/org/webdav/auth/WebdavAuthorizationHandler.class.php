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
     * @param   string uri                       The requested path 
     * @param   org.webdav.auth.WebdavUser user The WebdavUser object
     * @param   org.webdav.xml.WebdavScriptletRequest request The Request
     * @return  bool
     */
    public function isAuthorized($path, $user, $request) {
      return TRUE;
    }
  }
?>
