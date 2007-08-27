<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('lang.ChainedException');

  /**
   * Indicates authentication failed unexpectedly, probably due to 
   * problems with the authentication backend. For instance, the 
   * LDAP server used in the LdapAuthenticator may be unavailable.
   *
   * @see      xp://security.auth.Authenticator
   * @see      xp://lang.ChainedException
   * @purpose  Exception
   */
  class AuthenticatorException extends ChainedException {
  
  }
?>
