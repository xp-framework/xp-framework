<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet');

  /**
   * Class wrapper for table eventng, database METHADON
   * (Auto-generated on Wed, 04 Apr 2007 10:45:27 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class Eventng extends DataSet {

    protected
      $_isLoaded= false,
      $_loadCrit= NULL;

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('METHADON..eventng');
        $peer->setConnection('sybintern');
        $peer->setIdentity('eventng_id');
        $peer->setPrimary(array('eventng_id'));
        $peer->setTypes(array(
          'name'                => array('%s', FieldType::VARCHAR, FALSE),
          'description'         => array('%s', FieldType::VARCHAR, FALSE),
          'location'            => array('%s', FieldType::VARCHAR, FALSE),
          'start_at'            => array('%s', FieldType::VARCHAR, FALSE),
          'ends_at'             => array('%s', FieldType::VARCHAR, FALSE),
          'changedby'           => array('%s', FieldType::VARCHAR, FALSE),
          'all_day'             => array('%d', FieldType::INT, FALSE),
          'max_invited'         => array('%d', FieldType::INT, FALSE),
          'access'              => array('%d', FieldType::INT, FALSE),
          'recurrence'          => array('%d', FieldType::INT, FALSE),
          'eventng_id'          => array('%d', FieldType::NUMERIC, FALSE),
          'person_id'           => array('%d', FieldType::NUMERIC, FALSE),
          'location_id'         => array('%d', FieldType::NUMERIC, FALSE),
          'bz_id'               => array('%d', FieldType::NUMERIC, FALSE),
          'start_date'          => array('%s', FieldType::DATETIME, FALSE),
          'end_date'            => array('%s', FieldType::DATETIME, FALSE),
          'lastchange'          => array('%s', FieldType::DATETIME, FALSE)
        ));
      }
    }  

    function __get($name) {
      $this->load();
      return $this->get($name);
    }

    function __sleep() {
      $this->load();
      return array_merge(array_keys(self::getPeer()->types), array('_new', '_changed'));
    }

    /**
     * force loading this entity from database
     *
     */
    public function load() {
      if ($this->_isLoaded) return;
      $this->_isLoaded= true;
      $e= self::getPeer()->doSelect($this->_loadCrit);
      if (!$e) return;
      foreach (array_keys(self::getPeer()->types) as $p) {
        if (isset($this->{$p})) continue;
        $this->{$p}= $e[0]->$p;
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
     * Gets an instance of this object by index "eventng_eventn_577642321"
     * 
     * @param   int eventng_id
     * @return  de.schlund.db.methadon.Eventng entitiy object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByEventng_id($eventng_id) {
      return new self(array(
        'eventng_id'  => $eventng_id,
        '_loadCrit' => new Criteria(array('eventng_id', $eventng_id, EQUAL))
      ));
    }

    /**
     * Gets an instance of this object by index "i_eventng_table"
     * 
     * @param   int person_id
     * @return  de.schlund.db.methadon.Eventng[] entity objects
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByPerson_id($person_id) {
      $r= self::getPeer()->doSelect(new Criteria(array('person_id', $person_id, EQUAL)));
      foreach ($r as $e) $e->_isLoaded= true;
      return $r;
    }

    /**
     * Gets an instance of this object by index "i_eventngloc_table"
     * 
     * @param   int location_id
     * @return  de.schlund.db.methadon.Eventng[] entity objects
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByLocation_id($location_id) {
      $r= self::getPeer()->doSelect(new Criteria(array('location_id', $location_id, EQUAL)));
      foreach ($r as $e) $e->_isLoaded= true;
      return $r;
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
     * Retrieves location
     *
     * @return  string
     */
    public function getLocation() {
      return $this->location;
    }
      
    /**
     * Sets location
     *
     * @param   string location
     * @return  string the previous value
     */
    public function setLocation($location) {
      return $this->_change('location', $location);
    }

    /**
     * Retrieves start_at
     *
     * @return  string
     */
    public function getStart_at() {
      return $this->start_at;
    }
      
    /**
     * Sets start_at
     *
     * @param   string start_at
     * @return  string the previous value
     */
    public function setStart_at($start_at) {
      return $this->_change('start_at', $start_at);
    }

    /**
     * Retrieves ends_at
     *
     * @return  string
     */
    public function getEnds_at() {
      return $this->ends_at;
    }
      
    /**
     * Sets ends_at
     *
     * @param   string ends_at
     * @return  string the previous value
     */
    public function setEnds_at($ends_at) {
      return $this->_change('ends_at', $ends_at);
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
     * Retrieves all_day
     *
     * @return  int
     */
    public function getAll_day() {
      return $this->all_day;
    }
      
    /**
     * Sets all_day
     *
     * @param   int all_day
     * @return  int the previous value
     */
    public function setAll_day($all_day) {
      return $this->_change('all_day', $all_day);
    }

    /**
     * Retrieves max_invited
     *
     * @return  int
     */
    public function getMax_invited() {
      return $this->max_invited;
    }
      
    /**
     * Sets max_invited
     *
     * @param   int max_invited
     * @return  int the previous value
     */
    public function setMax_invited($max_invited) {
      return $this->_change('max_invited', $max_invited);
    }

    /**
     * Retrieves access
     *
     * @return  int
     */
    public function getAccess() {
      return $this->access;
    }
      
    /**
     * Sets access
     *
     * @param   int access
     * @return  int the previous value
     */
    public function setAccess($access) {
      return $this->_change('access', $access);
    }

    /**
     * Retrieves recurrence
     *
     * @return  int
     */
    public function getRecurrence() {
      return $this->recurrence;
    }
      
    /**
     * Sets recurrence
     *
     * @param   int recurrence
     * @return  int the previous value
     */
    public function setRecurrence($recurrence) {
      return $this->_change('recurrence', $recurrence);
    }

    /**
     * Retrieves eventng_id
     *
     * @return  int
     */
    public function getEventng_id() {
      return $this->eventng_id;
    }
      
    /**
     * Sets eventng_id
     *
     * @param   int eventng_id
     * @return  int the previous value
     */
    public function setEventng_id($eventng_id) {
      return $this->_change('eventng_id', $eventng_id);
    }

    /**
     * Retrieves person_id
     *
     * @return  int
     */
    public function getPerson_id() {
      return $this->person_id;
    }
      
    /**
     * Sets person_id
     *
     * @param   int person_id
     * @return  int the previous value
     */
    public function setPerson_id($person_id) {
      return $this->_change('person_id', $person_id);
    }

    /**
     * Retrieves location_id
     *
     * @return  int
     */
    public function getLocation_id() {
      return $this->location_id;
    }
      
    /**
     * Sets location_id
     *
     * @param   int location_id
     * @return  int the previous value
     */
    public function setLocation_id($location_id) {
      return $this->_change('location_id', $location_id);
    }

    /**
     * Retrieves bz_id
     *
     * @return  int
     */
    public function getBz_id() {
      return $this->bz_id;
    }
      
    /**
     * Sets bz_id
     *
     * @param   int bz_id
     * @return  int the previous value
     */
    public function setBz_id($bz_id) {
      return $this->_change('bz_id', $bz_id);
    }

    /**
     * Retrieves start_date
     *
     * @return  util.Date
     */
    public function getStart_date() {
      return $this->start_date;
    }
      
    /**
     * Sets start_date
     *
     * @param   util.Date start_date
     * @return  util.Date the previous value
     */
    public function setStart_date($start_date) {
      return $this->_change('start_date', $start_date);
    }

    /**
     * Retrieves end_date
     *
     * @return  util.Date
     */
    public function getEnd_date() {
      return $this->end_date;
    }
      
    /**
     * Sets end_date
     *
     * @param   util.Date end_date
     * @return  util.Date the previous value
     */
    public function setEnd_date($end_date) {
      return $this->_change('end_date', $end_date);
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

    /**
     * Retrieves the Bearbeitungszustand entity
     * referenced by bz_id=>bz_id
     *
     * @return  de.schlund.db.methadon.Bearbeitungszustand entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getBz() {
      $r= XPClass::forName('de.schlund.db.methadon.Bearbeitungszustand')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('bz_id', $this->getBz_id(), EQUAL)
      ));
      return $r ? $r[0] : NULL;
    }

    /**
     * Retrieves the Person entity
     * referenced by person_id=>person_id
     *
     * @return  de.schlund.db.methadon.Person entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getPerson() {
      $r= XPClass::forName('de.schlund.db.methadon.Person')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('person_id', $this->getPerson_id(), EQUAL)
      ));
      return $r ? $r[0] : NULL;
    }

    /**
     * Retrieves the Location entity
     * referenced by location_id=>location_id
     *
     * @return  de.schlund.db.methadon.Location entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getLocation() {
      $r= XPClass::forName('de.schlund.db.methadon.Location')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('location_id', $this->getLocation_id(), EQUAL)
      ));
      return $r ? $r[0] : NULL;
    }

    /**
     * Retrieves an array of all Eventng_history entities referencing
     * this entity by eventng_id=>eventng_id
     *
     * @return  de.schlund.db.methadon.Eventng_history[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEventng_historyEventngList() {
      return XPClass::forName('de.schlund.db.methadon.Eventng_history')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('eventng_id', $this->getEventng_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Eventng_history entities referencing
     * this entity by eventng_id=>eventng_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Eventng_history>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEventng_historyEventngIterator() {
      return XPClass::forName('de.schlund.db.methadon.Eventng_history')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('eventng_id', $this->getEventng_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Eventng_organizer_matrix entities referencing
     * this entity by eventng_id=>eventng_id
     *
     * @return  de.schlund.db.methadon.Eventng_organizer_matrix[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEventng_organizer_matrixEventngList() {
      return XPClass::forName('de.schlund.db.methadon.Eventng_organizer_matrix')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('eventng_id', $this->getEventng_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Eventng_organizer_matrix entities referencing
     * this entity by eventng_id=>eventng_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Eventng_organizer_matrix>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEventng_organizer_matrixEventngIterator() {
      return XPClass::forName('de.schlund.db.methadon.Eventng_organizer_matrix')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('eventng_id', $this->getEventng_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Eventng_recurrence_criteria entities referencing
     * this entity by eventng_id=>eventng_id
     *
     * @return  de.schlund.db.methadon.Eventng_recurrence_criteria[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEventng_recurrence_criteriaEventngList() {
      return XPClass::forName('de.schlund.db.methadon.Eventng_recurrence_criteria')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('eventng_id', $this->getEventng_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Eventng_recurrence_criteria entities referencing
     * this entity by eventng_id=>eventng_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Eventng_recurrence_criteria>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEventng_recurrence_criteriaEventngIterator() {
      return XPClass::forName('de.schlund.db.methadon.Eventng_recurrence_criteria')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('eventng_id', $this->getEventng_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Eventng_recurrence_matrix entities referencing
     * this entity by eventng_id=>eventng_id
     *
     * @return  de.schlund.db.methadon.Eventng_recurrence_matrix[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEventng_recurrence_matrixEventngList() {
      return XPClass::forName('de.schlund.db.methadon.Eventng_recurrence_matrix')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('eventng_id', $this->getEventng_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Eventng_recurrence_matrix entities referencing
     * this entity by eventng_id=>eventng_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Eventng_recurrence_matrix>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEventng_recurrence_matrixEventngIterator() {
      return XPClass::forName('de.schlund.db.methadon.Eventng_recurrence_matrix')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('eventng_id', $this->getEventng_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Eventng_reminder entities referencing
     * this entity by eventng_id=>eventng_id
     *
     * @return  de.schlund.db.methadon.Eventng_reminder[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEventng_reminderEventngList() {
      return XPClass::forName('de.schlund.db.methadon.Eventng_reminder')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('eventng_id', $this->getEventng_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Eventng_reminder entities referencing
     * this entity by eventng_id=>eventng_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Eventng_reminder>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEventng_reminderEventngIterator() {
      return XPClass::forName('de.schlund.db.methadon.Eventng_reminder')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('eventng_id', $this->getEventng_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Eventng_exception entities referencing
     * this entity by eventng_id=>eventng_id
     *
     * @return  de.schlund.db.methadon.Eventng_exception[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEventng_exceptionEventngList() {
      return XPClass::forName('de.schlund.db.methadon.Eventng_exception')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('eventng_id', $this->getEventng_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Eventng_exception entities referencing
     * this entity by eventng_id=>eventng_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Eventng_exception>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEventng_exceptionEventngIterator() {
      return XPClass::forName('de.schlund.db.methadon.Eventng_exception')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('eventng_id', $this->getEventng_id(), EQUAL)
      ));
    }
  }
?>