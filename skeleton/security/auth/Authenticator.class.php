<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * This interface describes objects that are able to authenticate 
   * username / password combinations.
   *
   * @purpose  Authenticator
   */
  class Authenticator extends Interface {
  
    /**
     * Authenticate a user
     *
     * @access  public
     * @param   string user
     * @param   string pass
     * @return  bool
     */
    function authenticate($user, $pass) { }
  }
?>
