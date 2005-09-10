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
      $permissions=   NULL,
      $eventtypes=    array();
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function setup(&$request) {
      $cm= &ConnectionManager::getInstance();
      $db= &$cm->getByHost('uska', 0);
      
      $this->eventtypes= array();
      try(); {
        $q= $db->query('select event_type_id, name, description from uska.event_type');
        while ($q && $r= $q->next()) { $this->eventtypes[$r['event_type_id']]= array(
          'type'  => $r['name'],
          'name'  => $r['description']
          );
        }
      } if (catch('SQLException', $e)) {
        return throw($e);
      }
    }

    /**
     * Insert status information to result tree
     *
     * @access  public
     * @param   &scriptlet.xml.XMLScriptletResponse response
     */
    function insertStatus(&$response) {
      if ($this->user) {
        $n= &$response->addFormResult(Node::fromObject($this->user, 'user'));
        $n->addChild(Node::fromArray(array_keys($this->permissions), 'permissions'));
      }
      
      $enode= &$response->addFormResult(new node('eventtypes'));
      foreach ($this->eventtypes as $id => $desc) {
        $enode->addChild(new Node('type', $desc['name'], array(
          'id' => $id,
          'type' => $desc['type']
        )));
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
    
    /**
     * Check whether user has a certain permission
     *
     * @access  public
     * @param   string name
     * @return  bool
     */
    function hasPermission($name) {
      return isset($this->permissions[$name]);
    }
  }
?>
