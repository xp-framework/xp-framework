<?php
/* This class is part of the XP framework
 *
 * $Id: LdapAuthenticator.class.php 10594 2007-06-11 10:04:54Z friebe $ 
 */

  namespace security::auth;

  uses(
    'peer.ldap.LDAPClient',
    'security.crypto.UnixCrypt',
    'security.auth.Authenticator'
  );

  /**
   * Autenticates users against LDAP
   *
   * @purpose  Authenticator
   */
  class LdapAuthenticator extends lang::Object implements Authenticator {
    public
      $lc     = NULL,
      $basedn = '';

    /**
     * Constructor
     *
     * @param   peer.ldap.LDAPClient lc
     * @param   string basedn
     */
    public function __construct($lc, $basedn) {
      $this->lc= $lc;
      $this->basedn= $basedn;
    }
  
    /**
     * Authenticate a user
     *
     * @param   string user
     * @param   string pass
     * @return  bool
     */
    public function authenticate($user, $pass) {
      try {
        $r= $this->lc->search($this->basedn, '(uid='.$user.')');
      } catch (peer::ldap::LDAPException $e) {
        throw(new AuthenticatorException(sprintf(
          'Authentication failed (#%d: "%s")', 
          $e->getErrorCode(),
          $e->getMessage()
        ), $e));
      } catch (peer::ConnectException $e) {
        throw(new AuthenticatorException(sprintf(
          'Authentication failed (<connect>: "%s")', 
          $e->getMessage()
        ), $e));
      }
      
      // Check return, we must find a distinct user
      if (1 != $r->numEntries()) return FALSE;
      
      $entry= $r->getNextEntry();
      return security::crypto::UnixCrypt::matches($entry->getAttribute('userpassword', 0), $pass);
    }
    
  } 
?>
