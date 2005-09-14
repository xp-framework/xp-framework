<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'peer.ldap.LDAPClient',
    'security.crypto.UnixCrypt'
  );

  /**
   * Autenticates users against LDAP
   *
   * @purpose  Authenticator
   */
  class LdapAuthenticator extends Object {
    var
      $lc     = NULL,
      $basedn = '';

    /**
     * Constructor
     *
     * @access  public
     * @param   &peer.ldap.LDAPClient lc
     * @param   string basedn
     */
    function __construct(&$lc, $basedn) {
      $this->lc= &$lc;
      $this->basedn= $basedn;
    }
  
    /**
     * Authenticate a user
     *
     * @access  public
     * @param   string user
     * @param   string pass
     * @return  bool
     */
    function authenticate($user, $pass) {
      try(); {
        $r= $this->lc->search($this->basedn, '(uid='.$user.')');
      } if (catch('LDAPException', $e)) {
        return throw(new AuthenticatorException('Authentication failed ("'.$e->getMessage().'")', $e));
      } if (catch('ConnectException', $e)) {
        return throw(new AuthenticatorException('Authentication failed ("'.$e->getMessage().'")', $e));
      }
      
      // Check return, we must find a distinct user
      if (1 != $r->numEntries()) return FALSE;
      
      $entry= $r->getNextEntry();
      return UnixCrypt::matches($entry->getAttribute('userpassword', 0), $pass);
    }
    
  } implements(__FILE__, 'security.auth.Authenticator');
?>
