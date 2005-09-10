<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'scriptlet.xml.workflow.Handler',
    'de.uska.scriptlet.wrapper.EditEventWrapper',
    'de.uska.db.Event'
  );

  /**
   * Handler to add or edit events.
   *
   * @purpose  Edit events
   */
  class EditEventHandler extends Handler {

    /**
     * Constructor.
     *
     * @access  public
     */
    function __construct() {
      $this->setWrapper(new EditEventWrapper());
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
      $id= (intVal($request->getQueryString()) ? intVal($request->getQueryString()) : 'new');
      return $this->name.'.'.$id;
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
        strlen($request->getQueryString()) &&
        ($event= &Event::getByEvent_id($request->getQueryString()))
      ) {
        $this->setFormValue('event_id', $event->getEvent_id());
        $this->setFormValue('event_type', $event->getEvent_type());
        $this->setFormValue('team', $event->getTeam_id());
        $this->setFormValue('name', $event->getName());
        $this->setFormValue('description', $event->getDescription());

        $tdate= &$event->getTarget_date();
        if ($tdate) $this->setFormValue('target_date', $tdate->toString('d.m.Y'));

        $ddate= &$event->getDeadline();
        if ($ddate) $this->setFormValue('deadline', $ddate->toString('d.m.Y'));

        $this->setFormValue('max', $event->getMax_attendees());
        $this->setFormValue('req', $event->getReq_attendees());
        $this->setFormValue('guests', $event->getGuests_allowed());

        $this->setValue('mode', 'update');
      } else {
      
        // New event, set some default values...
        $date= &Date::now();
        $this->setFormValue('target_date', $date->toString('d.m.Y'));
        $this->setFormValue('target_time', '18:30');
        $this->setFormValue('guests', 1);
        
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
      $sane= TRUE;
      switch ($this->getValue('mode')) {
        case 'update':
          try(); {
            $event= &Event::getByEvent_id($this->wrapper->getEvent_id());
          } if (catch('SQLException', $e)) {
            return throw($e);
          }
          break;
        
        case 'create':
        default:
          $event= &new Event();
          break;
      }
      $event->setName($this->wrapper->getName());
      $event->setDescription($this->wrapper->getDescription());
      $event->setTeam_id($this->wrapper->getTeam());
      $event->setEvent_type_id($this->wrapper->getEvent_type());
      $event->setChangedby($context->user->getUsername());
      $event->setLastchange(Date::now());
      
      $targetdate= &$this->wrapper->getTarget_date();
      $deadline= &$this->wrapper->getDeadline_date();
      
      list($th, $tm)= preg_split('/[:\.\-]/', $this->wrapper->getTarget_time(), 2);
      $targetdate= &new Date(Date::mktime(
        $th,
        $tm,
        0,
        $targetdate->getMonth(),
        $targetdate->getDay(),
        $targetdate->getYear()
      ));
      
      if ($deadline) {
        list($dh, $dm)= preg_split('/[:\.\-]/', $this->wrapper->getDeadline_time(), 2);
        $deadline= &new Date(Date::mktime(
          $dh,
          $dm,
          0,
          $deadline->getMonth(),
          $deadline->getDay(),
          $deadline->getYear()
        ));
      }
      
      // Check order of dates. Now < deadline < target_date
      with ($now= &Date::now()); {
        if ($now->isAfter($targetdate)) {
          $this->addError('order', 'target_date');
          $sane= FALSE;
        }
        
        if (
          $deadline &&
          $targetdate->isBefore($deadline)
        ) {
          $this->addError('order', 'deadline_date');
          $sane= FALSE;
        }
      }
      
      // Max attendees must be greater or requal than requireds
      if ($this->wrapper->getMax() < $this->wrapper->getReq()) {
        $this->addError('order', 'max');
        $this->addError('order', 'req');
        $sane= FALSE;
      }
      
      $event->setTarget_date($targetdate);
      $event->setDeadline($daedline);
      
      $event->setMax_attendees($this->wrapper->getMax());
      $event->setReq_attendees($this->wrapper->getReq());
      $event->setAllow_guests($this->wrapper->getGuests());
      
      // Some check failed, bail out...
      if (!$sane) return FALSE;
      
      try(); {
        if ($event->getEvent_id() > 0) { 
          $event->update(); 
        } else {
          $event->insert();
        }
      } if (catch('SQLException', $e)) {
        return throw($e);
      }
      
      return TRUE;
    }
  }
?>
