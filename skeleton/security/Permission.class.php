<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  /**
   * Permission base class
   *
   * @purpose  A single permission
   * @see      http://java.sun.com/j2se/1.4.1/docs/guide/security/permissions.html
   * @see      xp://security.Policy
   */
  class Permission extends Object {
    var
      $name     = '',
      $actions  = array();
      
    /**
     * Constrzctor
     *
     * @access  public
     * @param   string name
     */
    function __construct($name) {
      $this->name= $name;
      parent::__construct();
    }  
  }
?>
