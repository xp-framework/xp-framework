<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

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
