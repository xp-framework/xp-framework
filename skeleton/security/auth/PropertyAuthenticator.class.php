<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Autenticates users against a property
   *
   * @purpose  Authenticator
   */
  class PropertyAuthenticator extends Object {
    var
      $users = NULL;

    /**
     * Constructor
     *
     * @access  public
     * @param   &util.Properties users
     */    
    function __construct(&$prop) {
      $this->users= &$prop;

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
      $user= $this->users->readSection(sprintf('user::%s', $user), NULL);
      return ($pass === $user['password']) ? TRUE : FALSE;
    }
  
  } implements(__FILE__, 'security.auth.Authenticator');
?>
