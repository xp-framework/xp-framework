<?php
/* This class is part of the XP framework
 *
 * $Id: Permission.class.php 8971 2006-12-27 15:27:10Z friebe $
 */

  namespace security;
 
  /**
   * Permission base class
   *
   * @purpose  A single permission
   * @see      http://java.sun.com/j2se/1.4.1/docs/guide/security/permissions.html
   * @see      xp://security.Policy
   */
  class Permission extends lang::Object {
    public
      $name     = '',
      $actions  = array();
      
    /**
     * Constructor
     *
     * @param   string name
     */
    public function __construct($name, $actions) {
      $this->name= $name;
      $this->actions= $actions;
      
    }
    
    /**
     * Get this permission's name
     *
     * @return  string
     */
    public function getName() {
      return $this->name;
    }
    
    /**
     * Get this permission's actions
     *
     * @return  string[]
     */
    public function getActions() {
      return $this->actions;
    }
    
    /**
     * Create a string representation
     * 
     * Examples:
     * <pre>
     * permission io.FilePermission "/foo/bar", "read";
     * permission io.FilePermission "/baz/example", "read,write";
     * </pre>
     *
     * @return  string
     */
    public function toString() {
      return sprintf(
        'permission %s: "%s", "%s";',
        $this->getClassName(),
        $this->name,
        implode(',', $this->actions)
      );
    }
  }
?>
