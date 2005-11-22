<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'scriptlet.xml.workflow.Handler',
    'de.uska.scriptlet.wrapper.AttendEventWrapper',
    'de.uska.db.Player',
    'de.uska.db.Event',
    'de.uska.db.EventAttendee'
  );
  
  /**
   * Set information about attendee status of player or guest.
   *
   * @purpose  Attend event
   */
  class AttendEventHandler extends Handler {
  
    /**
     * Constructor.
     *
     * @access  public
     */
    function __construct() {
      $this->setWrapper(new AttendEventWrapper());
      parent::__construct();
    }
    
    /**
     * Retrieve identifier.
     *
     * @access  public
     * @param   &scriptlet.xml.XMLScriptletRequest request
     * @param   &scriptlet.xml.workflow.Context context
     * @return  string
     */
    function identifierFor(&$request, &$context) {
      return $this->name.'.'.$request->getParam('event_id');
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
      $this->setFormValue('offers_seats', 0);
      if ($request->hasParam('guest')) $this->setValue('mode', 'addguest');
      

      $player_id= $context->user->getPlayer_id();
      if ($request->hasParam('player_id') && $request->getParam('player_id') != $player_id) {
        
        // TBI: Checks for administrator (may he edit that user?)
        $player_id= $request->getParam('player_id');
      }
      
      // Check if we're updating or inserting later
      $attendee= &EventAttendee::getByEvent_idPlayer_id($request->getParam('event_id'), $player_id);
      if ($player_id == $context->user->getPlayer_id()) {
        $player= &$context->user;
      } else {
        $player= &Player::getByPlayer_id($player_id);
      }
      
      if ($attendee) {
        $this->setFormValue('attend', $attendee->getAttend());
        $this->setFormValue('offers_seats', $attendee->getOffers_seats());
        $this->setFormValue('needs_seat', $attendee->getNeeds_driver());
        
        if ($this->getvalue('mode') != 'addguest') {
          $this->setFormvalue('firstname', $player->getFirstname());
          $this->setFormValue('lastname', $player->getLastname());
        }
      }

      $this->setValue('event_id', $request->getParam('event_id'));
      $this->setValue('player_id', $player_id);
      
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
      $cm= &ConnectionManager::getInstance();
      
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
      try(); {
        $db= &$cm->getByHost('uskadb', 0);
        $transaction= &$db->begin(new Transaction('attend'));
        
        
        // Check if this is a guest attendee
        if ('addguest' == $this->getValue('mode')) {

          $event= &Event::getByEvent_id($this->wrapper->getEvent_id());
          
          // Prevent adding guests to events without such.
          if (!$event->getAllow_guests()) {
            $transaction->rollback();
            $this->addError('no_guests');
            return FALSE;
          }
          
          // Create guest player
          $player= &new Player();
          $player->setFirstname($this->wrapper->getFirstname());
          $player->setLastname($this->wrapper->getLastname());
          $player->setCreated_by($context->user->getPlayer_id());
          $player->setTeam_id($context->user->getTeam_id());
          $player->setChangedby($context->user->getUsername());
          $player->setLastchange(Date::now());
          $player->setPlayer_type_id(2);  // Guest
          $player->insert();
          
          $attendee= $player->getPlayer_id();
        }
        
        $newAttend= FALSE;
        $eventattend= &EventAttendee::getByEvent_idPlayer_id($this->wrapper->getEvent_id(), $attendee);
        if (!is('de.uska.db.EventAttendee', $eventattend)) {
          $eventattend= &new EventAttendee();
          $eventattend->setEvent_id($this->wrapper->getEvent_id());
          $eventattend->setPlayer_id($attendee);
          $newAttend= TRUE;
        }
        
        $eventattend->setAttend($this->wrapper->getAttend());
        $eventattend->setOffers_seats($this->wrapper->getOffers_seats());
        $eventattend->setNeeds_driver($this->wrapper->getNeeds_seat());
        $eventattend->setChangedby($context->user->getUsername());
        $eventattend->setLastchange(Date::now());
        
        if ($newAttend) {
          $eventattend->insert();
        } else {
          $eventattend->update();
        }
      } if (catch('SQLException', $e)) {
        $transaction->rollback();
        return throw($e);
      }
      
      $transaction->commit();
      return TRUE;
    }
    
    /**
     * In case of success, redirect the user to the event's page.
     *
     * @access  public
     * @param   &scriptlet.xml.workflow.WorkflowScriptletRequest request 
     * @param   &scriptlet.xml.XMLScriptletResponse response 
     * @param   &scriptlet.xml.Context context
     */
    function finalize(&$request, &$response, &$context) {
      $response->forwardTo('event/view', $this->getValue('event_id'));
    }
  }
?>
