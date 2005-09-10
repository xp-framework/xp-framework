<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'scriptlet.xml.workflow.Handler',
    'de.uska.scriptlet.wrapper.EditPlayerWrapper',
    'de.uska.db.Player'
  );

  /**
   * Handler to add or edit players
   *
   * @purpose  Edit player
   */
  class EditPlayerHandler extends Handler {
      
    /**
     * Constructor.
     *
     * @access  public
     */
    function __construct() {
      $this->setWrapper(new EditPlayerWrapper());
      parent::__construct();
    }
    
    /**
     * Get identifier.
     *
     * @access  public
     * @param   &scriptlet.xml.workflow.WorkflowScriptletRequest request
     * @param   &scriptlet.xml.Context context
     * @return  string
     */
    function identifierFor(&$request, &$context) {
      return $this->name.'.'.$request->getParam('player_id', 'new');
    }
    
    /**
     * Setup handler
     *
     * @access  public
     * @param   &scriptlet.xml.XMLScriptletRequest request
     * @param   &scriptlet.xml.workflow.Context context
     * @return  boolean
     */
    function setup(&$request, &$context) {
      if (
        $request->hasParam('player_id') && 
        ($player= &Player::getByPlayer_id($request->getParam('player_id')))
      ) {
      
        // Check for admin permission, if not editing himself
        if (
          $player->getPlayer_id() != $context->user->getPlayer_id() &&
          !$context->hasPermission('create_player')
        ) {
          $this->addError('edit', 'permission_denied');
          return FALSE;
        }
      
        // Fetch team
        $this->setFormValue('team_id', $player->getTeam_id());
        $this->setFormValue('player_id', $player->getPlayer_id());
        $this->setFormValue('firstname', $player->getFirstName());
        $this->setFormValue('lastname', $player->getLastName());
        $this->setFormValue('username', $player->getUsername());
        $this->setFormValue('email', $player->getEmail());
        $this->setFormValue('position', $player->getPosition());
        $this->setFormValue('team_id', $player->getTeam_id());
        $this->setValue('mode', 'update');
      } else {
        $this->setValue('mode', 'create');
      }
      
      // Select teams
      $pm= &PropertyManager::getInstance();
      $prop= &$pm->getProperties('product');
      $cm= &ConnectionManager::getInstance();
      
      try(); {
        $db= &$cm->getByHost('uska', 0);
        $teams= $db->select('
            team_id,
            name
          from
            team
          where team_id in (%d)',
          $prop->readArray($request->getProduct(), 'teams')
        );
      } if (catch('SQLException', $e)) {
        return throw($e);
      }
      
      $this->setValue('teams', $teams);

      return TRUE;
    }
      
    /**
     * Handle submitted data. Either create an event or update an existing one.
     *
     * @access  public
     * @param   &scriptlet.xml.XMLScriptletRequest request
     * @param   &scriptlet.xml.workflow.Context context
     * @return  boolean
     */
    function handleSubmittedData(&$request, &$context) {
      switch ($this->getValue('mode')) {
        case 'update':
          try(); {
            $player= &Player::getByPlayer_id($this->wrapper->getPlayer_id());
          } if (catch('SQLException', $e)) {
            return throw($e);
          }
          break;
        
        case 'create':
        default:
          $player= &new Player();
          break;
      }
      
      // Take over new values
      $player->setFirstName($this->wrapper->getFirstname());
      $player->setLastName($this->wrapper->getLastname());
      $player->setUsername($this->wrapper->getUsername());
      
      // Update password only if new one is given
      if (strlen ($this->wrapper->getPassword())) {
        $player->setPassword(md5($this->wrapper->getPassword()));
      }
      
      $email= &$this->wrapper->getEmail();
      $player->setEmail($email->localpart.'@'.$email->domain);
      $player->setPosition($this->wrapper->getPosition());
      if (NULL === $player->getTeam_id()) {
        $player->setTeam_id($this->wrapper->getTeam_id());
      }
      
      if (NULL === $player->getCreated_by()) {
        $player->setCreated_by($context->user->getPlayer_id());
      }
      
      $player->setChangedby($context->user->getUsername());
      $player->setLastchange(Date::now());
      
      // Now insert or update...
      try(); {
        $peer= &Player::getPeer();
        $transaction= &$peer->begin(new Transaction('editplayer'));
        
        $cm= &ConnectionManager::getInstance();
        $db= &$cm->getByHost($peer->connection, 0);
        
        if ($player->getPlayer_id() > 0) {
          $player->update();
        } else {
          $player->insert();
        }
        
      } if (catch('SQLException', $e)) {
        $transaction->rollback();
        return throw($e);
      }
      
      $transaction->commit();
      return TRUE;
    }
  }
?>
