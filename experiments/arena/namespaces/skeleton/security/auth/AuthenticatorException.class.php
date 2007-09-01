<?php
/* This class is part of the XP framework
 *
 * $Id: AuthenticatorException.class.php 10977 2007-08-27 17:14:26Z friebe $ 
 */

  namespace security::auth;

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
  class AuthenticatorException extends lang::ChainedException {
  
  }
?>
