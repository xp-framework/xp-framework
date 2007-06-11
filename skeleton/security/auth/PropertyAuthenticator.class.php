<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('security.auth.Authenticator');

  /**
   * Autenticates users against a property
   *
   * @purpose  Authenticator
   */
  class PropertyAuthenticator extends Object implements Authenticator {
    public
      $users = NULL;

    /**
     * Constructor
     *
     * @param   util.Properties users
     */    
    public function __construct($prop) {
      $this->users= $prop;

    }
    
    /**
     * Authenticate a user
     *
     * @param   string user
     * @param   string pass
     * @return  bool
     */
    public function authenticate($user, $pass) {
      $user= $this->users->readSection(sprintf('user::%s', $user), NULL);
      return ($pass === $user['password']) ? TRUE : FALSE;
    }
  
  } 
?>
