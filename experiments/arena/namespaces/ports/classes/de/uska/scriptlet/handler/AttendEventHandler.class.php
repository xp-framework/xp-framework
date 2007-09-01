<?php
/* This class is part of the XP framework
 *
 * $Id: AttendEventHandler.class.php 10655 2007-06-23 15:37:59Z kiesel $ 
 */

  namespace de::uska::scriptlet::handler;

  ::uses(
    'scriptlet.xml.workflow.Handler',
    'de.uska.scriptlet.wrapper.AttendEventWrapper',
    'de.uska.db.Player',
    'de.uska.db.Event',
    'de.uska.db.Event_attendee'
  );
  
  /**
   * Set information about attendee status of player or guest.
   *
   * @purpose  Attend event
   */
  class AttendEventHandler extends scriptlet::xml::workflow::Handler {
  
    /**
     * Constructor.
     *
     */
    public function __construct() {
      $this->setWrapper(new de::uska::scriptlet::wrapper::AttendEventWrapper());
      parent::__construct();
    }
    
    /**
     * Retrieve identifier.
     *
     * @param   scriptlet.xml.XMLScriptletRequest request
     * @param   scriptlet.xml.workflow.Context context
     * @return  string
     */
    public function identifierFor($request, $context) {
      return $this->name.'.'.$request->getParam('event_id');
    }
    
    /**
     * Setup handler
     *
     * @param   scriptlet.xml.XMLScriptletRequest request
     * @param   scriptlet.xml.workflow.Context context
     * @return  boolean
     */
    public function setup($request, $context) {
      $this->setFormValue('offers_seats', 0);
      if ($request->hasParam('guest')) $this->setValue('mode', 'addguest');

      $player_id= $context->user->getPlayer_id();
      if (
        $request->hasParam('player_id') && 
        $request->getParam('player_id') != $player_id &&
        $context->hasPermission('create_player')
      ) {
        $player_id= $request->getParam('player_id');
      }
      
      // Check if we're updating or inserting later
      $attendee= de::uska::db::Event_attendee::getByEvent_idPlayer_id($request->getParam('event_id'), $player_id);
      if ($player_id == $context->user->getPlayer_id()) {
        $player= $context->user;
      } else {
        $player= de::uska::db::Player::getByPlayer_id($player_id);
      }
      
      if ($attendee instanceof de::uska::db::Event_attendee) {
        $this->setFormValue('attend', $attendee->getAttend());
        $this->setFormValue('offers_seats', $attendee->getOffers_seats());
        $this->setFormValue('needs_seat', $attendee->getNeeds_driver());
        
        if ($this->getvalue('mode') != 'addguest') {
          $this->setFormvalue('firstname', $player->getFirstname());
          $this->setFormValue('lastname', $player->getLastname());
        }
      }
      
      // Check that this event is still in the future. If not, only admins may change
      // the attending status
      $event= de::uska::db::Event::getByEvent_id($request->getParam('event_id'));
      $deadline_date= $event->getDeadline();
      $target_date= $event->getTarget_date();
      if ((($daedline_date && $deadline_date->isBefore(util::Date::now())) || $target_date->isBefore(util::Date::now())) && !$context->hasPermission('create_event')) {
        $this->addError('date_expired', '*');
        return FALSE;
      }

      $this->setValue('event_id', $request->getParam('event_id'));
      $this->setValue('player_id', $player_id);
      
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
      $cm= rdbms::ConnectionManager::getInstance();
      
      // Either user requires a driver or he can offer seats - not both.
      if ($this->wrapper->getNeeds_seat() && $this->wrapper->getOffers_seats() > 0) {
        $this->addError('mutex-fail', 'needs_seat');
        $this->addError('mutex-fail', 'offers_seats');
        return FALSE;
      }
      
      if (
        'addguest' == $this->getValue('mode') &&
        (0 == strlen($this->wrapper->getFirstname()) || 0 == strlen($this->wrapper->getLastname()))
      ) {
        $this->addError('missing', 'firstname');
        $this->addError('missing', 'lastname');
        return FALSE;
      }
      
      $attendee= $this->wrapper->getPlayer_id();
      try {
        $db= $cm->getByHost('uskadb', 0);
        $transaction= $db->begin(new ('attend'));
        
        // Check if this is a guest attendee
        if ('addguest' == $this->getValue('mode')) {

          $event= de::uska::db::Event::getByEvent_id($this->wrapper->getEvent_id());
          
          // Prevent adding guests to events without such.
          if (!$event->getAllow_guests()) {
            $transaction->rollback();
            $this->addError('no_guests');
            return FALSE;
          }
          
          // Create guest player
          $player= new de::uska::db::Player();
          $player->setFirstname($this->wrapper->getFirstname());
          $player->setLastname($this->wrapper->getLastname());
          $player->setCreated_by($context->user->getPlayer_id());
          $player->setTeam_id($context->user->getTeam_id());
          $player->setChangedby($context->user->getUsername());
          $player->setLastchange(util::Date::now());
          $player->setPlayer_type_id(2);  // Guest
          $player->insert();
          
          $attendee= $player->getPlayer_id();
        }
        
        $newAttend= FALSE;
        $eventattend= de::uska::db::Event_attendee::getByEvent_idPlayer_id($this->wrapper->getEvent_id(), $attendee);
        if (!is('de.uska.db.Event_attendee', $eventattend)) {
          $eventattend= new de::uska::db::Event_attendee();
          $eventattend->setEvent_id($this->wrapper->getEvent_id());
          $eventattend->setPlayer_id($attendee);
          $newAttend= TRUE;
        }
        
        $eventattend->setAttend($this->wrapper->getAttend());
        $eventattend->setOffers_seats($this->wrapper->getOffers_seats());
        $eventattend->setNeeds_driver($this->wrapper->getNeeds_seat());
        $eventattend->setChangedby($context->user->getUsername());
        $eventattend->setLastchange(util::Date::now());
        
        if ($newAttend) {
          $eventattend->insert();
        } else {
          $eventattend->update();
        }
        
        // Check on current number of attendees
        $event= de::uska::db::Event::getByEvent_id($this->wrapper->getEvent_id());
        $count= array_shift($db->select('
          count(*) as attendees
          from
            event as e,
            event_attendee as a
          where e.event_id= %d
            and e.event_id= a.event_id
            and a.attend= 1
          ',
          $event->getEvent_id()
        ));
      } catch (rdbms::SQLException $e) {
        $transaction->rollback();
        throw($e);
      }
      
      if (
        $count['attendees'] > $event->getMax_attendees() &&
        !$this->context->hasPermission('create_event')
      ) {
        $this->addError('too_many_attendees', '*');
        $transaction->rollback();
        return FALSE;
      }
      
      $transaction->commit();
      return TRUE;
    }
    
    /**
     * In case of success, redirect the user to the event's page.
     *
     * @param   &scriptlet.xml.workflow.WorkflowScriptletRequest request 
     * @param   &scriptlet.xml.XMLScriptletResponse response 
     * @param   &scriptlet.xml.Context context
     */
    public function finalize($request, $response, $context) {
      $response->forwardTo('event/view', $this->getValue('event_id'));
    }
  }
?>
