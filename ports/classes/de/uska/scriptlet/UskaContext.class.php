<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('scriptlet.xml.workflow.Context');

  /**
   * Provide context information for uska.
   *
   * @purpose  Uska context
   */
  class UskaContext extends Context {
    var
      $user=          NULL,
      $permissions=   NULL;
    
    /**
     * Insert status information to result tree
     *
     * @access  public
     * @param   &scriptlet.xml.XMLScriptletResponse response
     */
    function insertStatus(&$response) {
      if ($this->user) {
        $response->addFormResult(Node::fromObject($this->user, 'user'));
        $response->addFormResult(Node::fromArray($this->permissions, 'permission'));
      }
    }
    
    /**
     * Set user.
     *
     * @access  public
     * @param   &de.uska.db.Player user
     */
    function setUser(&$user) {
      $this->user= &$user;
      $this->setChanged();
    }
    
    /**
     * Set permissions
     *
     * @access  public
     * @param   &array perms
     */
    function setPermissions(&$perm) {
      $this->permissions= &$perm;
      $this->setChanged();
    }
  }
?>
