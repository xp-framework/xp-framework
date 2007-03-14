<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses('rdbms.DataSet');
 
  /**
   * Class wrapper for table event, database uska
   * (Auto-generated on Wed, 14 Mar 2007 22:22:17 +0100 by ak)
   *
   * @purpose  Datasource accessor
   */
  class Event extends DataSet {
    public
      $event_id           = 0,
      $team_id            = 0,
      $name               = '',
      $description        = NULL,
      $target_date        = NULL,
      $deadline           = NULL,
      $max_attendees      = NULL,
      $req_attendees      = NULL,
      $allow_guests       = NULL,
      $event_type_id      = 0,
      $changedby          = '',
      $lastchange         = NULL;

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('uska.event');
        $peer->setConnection('uska');
        $peer->setIdentity('event_id');
        $peer->setPrimary(array('event_id'));
        $peer->setTypes(array(
          'event_id'            => array('%d', FieldType::INT, FALSE),
          'team_id'             => array('%d', FieldType::INT, FALSE),
          'name'                => array('%s', FieldType::VARCHAR, FALSE),
          'description'         => array('%s', FieldType::TEXT, TRUE),
          'target_date'         => array('%s', FieldType::DATETIME, FALSE),
          'deadline'            => array('%s', FieldType::DATETIME, TRUE),
          'max_attendees'       => array('%d', FieldType::INT, TRUE),
          'req_attendees'       => array('%d', FieldType::INT, TRUE),
          'allow_guests'        => array('%d', FieldType::INT, TRUE),
          'event_type_id'       => array('%d', FieldType::INT, FALSE),
          'changedby'           => array('%s', FieldType::VARCHAR, FALSE),
          'lastchange'          => array('%s', FieldType::DATETIME, FALSE)
        ));
      }
    }  
  
    /**
     * Retrieve associated peer
     *
     * @return  rdbms.Peer
     */
    public static function getPeer() {
      return Peer::forName(__CLASS__);
    }
  
    /**
     * Gets an instance of this object by index "PRIMARY"
     * 
     * @param   int event_id
     * @return  de.uska.db.Event object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByEvent_id($event_id) {
      return current(self::getPeer()->doSelect(new Criteria(array('event_id', $event_id, EQUAL))));
    }

    /**
     * Gets an instance of this object by index "target_date"
     * 
     * @param   util.Date target_date
     * @return  de.uska.db.Event[] object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByTarget_date($target_date) {
      return self::getPeer()->doSelect(new Criteria(array('target_date', $target_date, EQUAL)));
    }

    /**
     * Gets an instance of this object by index "team_id"
     * 
     * @param   int team_id
     * @return  de.uska.db.Event[] object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByTeam_id($team_id) {
      return self::getPeer()->doSelect(new Criteria(array('team_id', $team_id, EQUAL)));
    }

    /**
     * Gets an instance of this object by index "event_type_id"
     * 
     * @param   int event_type_id
     * @return  de.uska.db.Event[] object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByEvent_type_id($event_type_id) {
      return self::getPeer()->doSelect(new Criteria(array('event_type_id', $event_type_id, EQUAL)));
    }

    /**
     * Retrieves event_id
     *
     * @return  int
     */
    public function getEvent_id() {
      return $this->event_id;
    }
      
    /**
     * Sets event_id
     *
     * @param   int event_id
     * @return  int the previous value
     */
    public function setEvent_id($event_id) {
      return $this->_change('event_id', $event_id);
    }

    /**
     * Retrieves team_id
     *
     * @return  int
     */
    public function getTeam_id() {
      return $this->team_id;
    }
      
    /**
     * Sets team_id
     *
     * @param   int team_id
     * @return  int the previous value
     */
    public function setTeam_id($team_id) {
      return $this->_change('team_id', $team_id);
    }

    /**
     * Retrieves name
     *
     * @return  string
     */
    public function getName() {
      return $this->name;
    }
      
    /**
     * Sets name
     *
     * @param   string name
     * @return  string the previous value
     */
    public function setName($name) {
      return $this->_change('name', $name);
    }

    /**
     * Retrieves description
     *
     * @return  string
     */
    public function getDescription() {
      return $this->description;
    }
      
    /**
     * Sets description
     *
     * @param   string description
     * @return  string the previous value
     */
    public function setDescription($description) {
      return $this->_change('description', $description);
    }

    /**
     * Retrieves target_date
     *
     * @return  util.Date
     */
    public function getTarget_date() {
      return $this->target_date;
    }
      
    /**
     * Sets target_date
     *
     * @param   util.Date target_date
     * @return  util.Date the previous value
     */
    public function setTarget_date($target_date) {
      return $this->_change('target_date', $target_date);
    }

    /**
     * Retrieves deadline
     *
     * @return  util.Date
     */
    public function getDeadline() {
      return $this->deadline;
    }
      
    /**
     * Sets deadline
     *
     * @param   util.Date deadline
     * @return  util.Date the previous value
     */
    public function setDeadline($deadline) {
      return $this->_change('deadline', $deadline);
    }

    /**
     * Retrieves max_attendees
     *
     * @return  int
     */
    public function getMax_attendees() {
      return $this->max_attendees;
    }
      
    /**
     * Sets max_attendees
     *
     * @param   int max_attendees
     * @return  int the previous value
     */
    public function setMax_attendees($max_attendees) {
      return $this->_change('max_attendees', $max_attendees);
    }

    /**
     * Retrieves req_attendees
     *
     * @return  int
     */
    public function getReq_attendees() {
      return $this->req_attendees;
    }
      
    /**
     * Sets req_attendees
     *
     * @param   int req_attendees
     * @return  int the previous value
     */
    public function setReq_attendees($req_attendees) {
      return $this->_change('req_attendees', $req_attendees);
    }

    /**
     * Retrieves allow_guests
     *
     * @return  int
     */
    public function getAllow_guests() {
      return $this->allow_guests;
    }
      
    /**
     * Sets allow_guests
     *
     * @param   int allow_guests
     * @return  int the previous value
     */
    public function setAllow_guests($allow_guests) {
      return $this->_change('allow_guests', $allow_guests);
    }

    /**
     * Retrieves event_type_id
     *
     * @return  int
     */
    public function getEvent_type_id() {
      return $this->event_type_id;
    }
      
    /**
     * Sets event_type_id
     *
     * @param   int event_type_id
     * @return  int the previous value
     */
    public function setEvent_type_id($event_type_id) {
      return $this->_change('event_type_id', $event_type_id);
    }

    /**
     * Retrieves changedby
     *
     * @return  string
     */
    public function getChangedby() {
      return $this->changedby;
    }
      
    /**
     * Sets changedby
     *
     * @param   string changedby
     * @return  string the previous value
     */
    public function setChangedby($changedby) {
      return $this->_change('changedby', $changedby);
    }

    /**
     * Retrieves lastchange
     *
     * @return  util.Date
     */
    public function getLastchange() {
      return $this->lastchange;
    }
      
    /**
     * Sets lastchange
     *
     * @param   util.Date lastchange
     * @return  util.Date the previous value
     */
    public function setLastchange($lastchange) {
      return $this->_change('lastchange', $lastchange);
    }
  }
?>