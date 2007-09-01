<?php
/* This class is part of the XP framework
 *
 * $Id: EditPlayerHandler.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace de::uska::scriptlet::handler;

  ::uses(
    'scriptlet.xml.workflow.Handler',
    'de.uska.scriptlet.wrapper.EditPlayerWrapper',
    'de.uska.db.Player',
    'de.uska.EzmlmSqlUtil'
  );

  /**
   * Handler to add or edit players
   *
   * @purpose  Edit player
   */
  class EditPlayerHandler extends scriptlet::xml::workflow::Handler {
      
    /**
     * Constructor.
     *
     */
    public function __construct() {
      $this->setWrapper(new de::uska::scriptlet::wrapper::EditPlayerWrapper());
      parent::__construct();
    }
    
    /**
     * Get identifier.
     *
     * @param   &scriptlet.xml.workflow.WorkflowScriptletRequest request
     * @param   &scriptlet.xml.Context context
     * @return  string
     */
    public function identifierFor($request, $context) {
      return $this->name.'.'.$request->getParam('player_id', 'new');
    }
    
    /**
     * Setup handler
     *
     * @param   &scriptlet.xml.XMLScriptletRequest request
     * @param   &scriptlet.xml.workflow.Context context
     * @return  boolean
     */
    public function setup($request, $context) {
      if (
        $request->hasParam('player_id') && 
        ($player= de::uska::db::Player::getByPlayer_id($request->getParam('player_id')))
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
        $this->setFormValue('player_id', 'new');
        $this->setValue('mode', 'create');
      }
      
      // Select teams
      $pm= util::PropertyManager::getInstance();
      $prop= $pm->getProperties('product');
      $cm= rdbms::ConnectionManager::getInstance();
      
      try {
        $db= $cm->getByHost('uska', 0);
        $teams= $db->select('
            team_id,
            name
          from
            team
          where team_id in (%d)',
          $prop->readArray($request->getProduct(), 'teams')
        );
      } catch (rdbms::SQLException $e) {
        throw($e);
      }
      $this->setValue('teams', $teams);
      
      // Select mailinglists
      try {
        $mls= $db->select('
            m.mailinglist_id,
            m.name,
            m.address,
            mpm.player_id as subscribed
          from
            mailinglist as m
              left outer join mailinglist_player_matrix as mpm
            on
              m.mailinglist_id= mpm.mailinglist_id
              and mpm.player_id= %d
          ',
          $request->getParam('player_id', NULL)
        );
      } catch (rdbms::SQLException $e) {
        throw($e);
      }
      
      foreach ($mls as $m) {
        $this->setFormValue(
          'mailinglist[ml_'.$m['mailinglist_id'].']',
          $m['subscribed']
        );
      }
      $this->setValue('mailinglists', $mls);

      return TRUE;
    }
      
    /**
     * Handle submitted data. Either create an event or update an existing one.
     *
     * @param   &scriptlet.xml.XMLScriptletRequest request
     * @param   &scriptlet.xml.workflow.Context context
     * @return  boolean
     */
    public function handleSubmittedData($request, $context) {
      $log= util::log::Logger::getInstance();
      $cat= $log->getCategory();
      
      switch ($this->getValue('mode')) {
        case 'update':
          try {
            $player= de::uska::db::Player::getByPlayer_id($this->wrapper->getPlayer_id());
          } catch (rdbms::SQLException $e) {
            throw($e);
          }
          break;
        
        case 'create':
        default:
          $player= new de::uska::db::Player();
          $player->setPlayer_type_id(1);  // Normal player
          break;
      }
      
      // Take over new values
      $player->setFirstName($this->wrapper->getFirstname());
      $player->setLastName($this->wrapper->getLastname());
      
      // Only admins may change usernames
      if (
        strlen($this->wrapper->getUsername()) &&
        $context->hasPermission('create_player')
      ) {
        $player->setUsername($this->wrapper->getUsername());
      }
      
      // Update password only if new one is given
      if (strlen ($this->wrapper->getPassword())) {
        $player->setPassword(md5($this->wrapper->getPassword()));
      }
      
      // update email, remember old one for ezmlm updates
      $email= $this->wrapper->getEmail();
      $oldemail= NULL;
      if ($player->getEmail() != $email->localpart.'@'.$email->domain) $oldemail= $player->getEmail();
      $player->setEmail($email->localpart.'@'.$email->domain);
      $player->setPosition($this->wrapper->getPosition());
      $player->setTeam_id($this->wrapper->getTeam_id());
      
      if (NULL === $player->getCreated_by()) {
        $player->setCreated_by($context->user->getPlayer_id());
      }
      
      $player->setChangedby($context->user->getUsername());
      $player->setLastchange(util::Date::now());
      
      // Now insert or update...
      try {
        $peer= de::uska::db::Player::getPeer();
        $transaction= $peer->begin(new ('editplayer'));
        
        $cm= rdbms::ConnectionManager::getInstance();
        $db= $cm->getByHost($peer->connection, 0);
        
        if ($this->getValue('mode') == 'update') {
          $player->update();
        } else {
          $player->insert();
        }
        
        // If email was changed, update all mailinglists
        $mls= $this->getValue('mailinglists');
        if ($oldemail) {
          foreach ($mls as $mailinglist) {
            $ezmlm= new de::uska::EzmlmSqlUtil('ezmlm', $mailinglist['name']);
            $ezmlm->setConnection($db);
            $ezmlm->alterAddress($oldemail, $player->getEmail());
          }
        }
        
        // Mailinglist management
        $newml= $this->wrapper->getMailinglist();
        foreach ($mls as $mailinglist) {

          if (!empty($newml['ml_'.$mailinglist['mailinglist_id']])) {
            $found= FALSE;
            try {
              $db->insert('into mailinglist_player_matrix (
                  mailinglist_id,
                  player_id,
                  lastchange,
                  changedby
                ) values (
                  %d,
                  %d,
                  %s,
                  %s
                )',
                $mailinglist['mailinglist_id'],
                $player->getPlayer_id(),
                util::Date::now(),
                $context->user->getUsername()
              );
            } catch (rdbms::SQLStatementFailedException $ignored) {
              // already there, ok...
              $found= TRUE;
            }

            if (!$found) {
              $ezmlm= new de::uska::EzmlmSqlUtil('ezmlm', $mailinglist['name']);
              $ezmlm->setConnection($db);
              $ezmlm->addSubscriber($player->getEmail());
            }
          } else {
            $cnt= $db->delete('
              from 
                mailinglist_player_matrix
              where player_id= %d
                and mailinglist_id= %d
              ',
              $player->getPlayer_id(),
              $mailinglist['mailinglist_id']
            );

            if ($cnt) {
              $ezmlm= new de::uska::EzmlmSqlUtil('ezmlm', $mailinglist['name']);
              $ezmlm->setConnection($db);
              $ezmlm->removeSubscriber($player->getEmail());
            }
          }
        }
      } catch (rdbms::SQLException $e) {
        $transaction->rollback();
        $this->addError('dberror', '*', $e->getMessage());
        return FALSE;
      }
      
      $transaction->commit();
      return TRUE;
    }
  }
?>
