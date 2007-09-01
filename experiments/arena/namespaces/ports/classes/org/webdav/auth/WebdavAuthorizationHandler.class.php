<?php
/* This class is part of the XP framework
 *
 * $Id: WebdavAuthorizationHandler.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace org::webdav::auth;

  /**
   * Handles the user authorization
   *
   * @purpose  Authorization
   */
  class WebdavAuthorizationHandler extends lang::Object {
  
    /**
     * Checks if the user is authorized to do something. 
     *
     * @param   string uri                       The requested path 
     * @param   &org.webdav.auth.WebdavUser user The WebdavUser object
     * @param   &org.webdav.xml.WebdavScriptletRequest request The Request
     * @return  bool
     */
    public function isAuthorized($path, $user, $request) {
      return TRUE;
    }
  }
?>
