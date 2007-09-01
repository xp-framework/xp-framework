<?php
/* This class is part of the XP framework
 *
 * $Id: Authenticator.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace security::auth;

  uses('security.auth.AuthenticatorException');

  /**
   * This interface describes objects that are able to authenticate 
   * username / password combinations.
   *
   * @purpose  Authenticator
   */
  interface Authenticator {
  
    /**
     * Authenticate a user
     *
     * @param   string user
     * @param   string pass
     * @return  bool
     * @throws  security.auth.AuthenticatorException
     */
    public function authenticate($user, $pass);
  }
?>
