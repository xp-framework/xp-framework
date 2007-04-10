<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet');

  /**
   * Class wrapper for table resource_reservation, database METHADON
   * (Auto-generated on Wed, 04 Apr 2007 10:45:27 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class Resource_reservation extends DataSet {

    protected
      $_isLoaded= false,
      $_loadCrit= NULL;

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('METHADON..resource_reservation');
        $peer->setConnection('sybintern');
        $peer->setIdentity('resource_reservation_id');
        $peer->setPrimary(array('resource_reservation_id'));
        $peer->setTypes(array(
          'purpose'             => array('%s', FieldType::VARCHAR, FALSE),
          'changedby'           => array('%s', FieldType::VARCHAR, FALSE),
          'no_invited'          => array('%d', FieldType::INT, FALSE),
          'external_guests'     => array('%d', FieldType::INT, FALSE),
          'recurrence'          => array('%d', FieldType::INT, FALSE),
          'all_day'             => array('%d', FieldType::INT, FALSE),
          'expired'             => array('%d', FieldType::INT, FALSE),
          'resource_reservation_id' => array('%d', FieldType::NUMERIC, FALSE),
          'resourceng_id'       => array('%d', FieldType::NUMERIC, FALSE),
          'person_id'           => array('%d', FieldType::NUMERIC, FALSE),
          'bz_id'               => array('%d', FieldType::NUMERIC, FALSE),
          'start_date'          => array('%s', FieldType::DATETIME, FALSE),
          'end_date'            => array('%s', FieldType::DATETIME, FALSE),
          'lastchange'          => array('%s', FieldType::DATETIME, FALSE),
          'lastcached'          => array('%s', FieldType::DATETIMN, TRUE),
          'expiredate'          => array('%s', FieldType::DATETIMN, TRUE),
          'eventng_id'          => array('%d', FieldType::NUMERICN, TRUE)
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
     * Gets an instance of this object by index "resource_r_resour_1197244289"
     * 
     * @param   int resource_reservation_id
     * @return  de.schlund.db.methadon.Resource_reservation entitiy object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByResource_reservation_id($resource_reservation_id) {
      return new self(array(
        'resource_reservation_id'  => $resource_reservation_id,
        '_loadCrit' => new Criteria(array('resource_reservation_id', $resource_reservation_id, EQUAL))
      ));
    }

    /**
     * Gets an instance of this object by index "i_resrez_table"
     * 
     * @param   int person_id
     * @return  de.schlund.db.methadon.Resource_reservation[] entity objects
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByPerson_id($person_id) {
      $r= self::getPeer()->doSelect(new Criteria(array('person_id', $person_id, EQUAL)));
      foreach ($r as $e) $e->_isLoaded= true;
      return $r;
    }

    /**
     * Gets an instance of this object by index "i_resrezres_table"
     * 
     * @param   int resourceng_id
     * @return  de.schlund.db.methadon.Resource_reservation[] entity objects
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByResourceng_id($resourceng_id) {
      $r= self::getPeer()->doSelect(new Criteria(array('resourceng_id', $resourceng_id, EQUAL)));
      foreach ($r as $e) $e->_isLoaded= true;
      return $r;
    }

    /**
     * Gets an instance of this object by index "i_resrez_recurrence"
     * 
     * @param   int recurrence
     * @return  de.schlund.db.methadon.Resource_reservation[] entity objects
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByRecurrence($recurrence) {
      $r= self::getPeer()->doSelect(new Criteria(array('recurrence', $recurrence, EQUAL)));
      foreach ($r as $e) $e->_isLoaded= true;
      return $r;
    }

    /**
     * Gets an instance of this object by index "i_resrez_expired"
     * 
     * @param   int expired
     * @return  de.schlund.db.methadon.Resource_reservation[] entity objects
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByExpired($expired) {
      $r= self::getPeer()->doSelect(new Criteria(array('expired', $expired, EQUAL)));
      foreach ($r as $e) $e->_isLoaded= true;
      return $r;
    }

    /**
     * Gets an instance of this object by index "i_resrez_lastcached"
     * 
     * @param   util.Date lastcached
     * @return  de.schlund.db.methadon.Resource_reservation[] entity objects
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByLastcached($lastcached) {
      $r= self::getPeer()->doSelect(new Criteria(array('lastcached', $lastcached, EQUAL)));
      foreach ($r as $e) $e->_isLoaded= true;
      return $r;
    }

    /**
     * Gets an instance of this object by index "i_resrez_expiredate"
     * 
     * @param   util.Date expiredate
     * @return  de.schlund.db.methadon.Resource_reservation[] entity objects
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByExpiredate($expiredate) {
      $r= self::getPeer()->doSelect(new Criteria(array('expiredate', $expiredate, EQUAL)));
      foreach ($r as $e) $e->_isLoaded= true;
      return $r;
    }

    /**
     * Gets an instance of this object by index "i_resrez_startdate"
     * 
     * @param   util.Date start_date
     * @return  de.schlund.db.methadon.Resource_reservation[] entity objects
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByStart_date($start_date) {
      $r= self::getPeer()->doSelect(new Criteria(array('start_date', $start_date, EQUAL)));
      foreach ($r as $e) $e->_isLoaded= true;
      return $r;
    }

    /**
     * Gets an instance of this object by index "i_resrez_end_date"
     * 
     * @param   util.Date end_date
     * @return  de.schlund.db.methadon.Resource_reservation[] entity objects
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByEnd_date($end_date) {
      $r= self::getPeer()->doSelect(new Criteria(array('end_date', $end_date, EQUAL)));
      foreach ($r as $e) $e->_isLoaded= true;
      return $r;
    }

    /**
     * Gets an instance of this object by index "i_resrez_bz_id"
     * 
     * @param   int bz_id
     * @return  de.schlund.db.methadon.Resource_reservation[] entity objects
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByBz_id($bz_id) {
      $r= self::getPeer()->doSelect(new Criteria(array('bz_id', $bz_id, EQUAL)));
      foreach ($r as $e) $e->_isLoaded= true;
      return $r;
    }

    /**
     * Retrieves purpose
     *
     * @return  string
     */
    public function getPurpose() {
      return $this->purpose;
    }
      
    /**
     * Sets purpose
     *
     * @param   string purpose
     * @return  string the previous value
     */
    public function setPurpose($purpose) {
      return $this->_change('purpose', $purpose);
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
     * Retrieves no_invited
     *
     * @return  int
     */
    public function getNo_invited() {
      return $this->no_invited;
    }
      
    /**
     * Sets no_invited
     *
     * @param   int no_invited
     * @return  int the previous value
     */
    public function setNo_invited($no_invited) {
      return $this->_change('no_invited', $no_invited);
    }

    /**
     * Retrieves external_guests
     *
     * @return  int
     */
    public function getExternal_guests() {
      return $this->external_guests;
    }
      
    /**
     * Sets external_guests
     *
     * @param   int external_guests
     * @return  int the previous value
     */
    public function setExternal_guests($external_guests) {
      return $this->_change('external_guests', $external_guests);
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
     * Retrieves expired
     *
     * @return  int
     */
    public function getExpired() {
      return $this->expired;
    }
      
    /**
     * Sets expired
     *
     * @param   int expired
     * @return  int the previous value
     */
    public function setExpired($expired) {
      return $this->_change('expired', $expired);
    }

    /**
     * Retrieves resource_reservation_id
     *
     * @return  int
     */
    public function getResource_reservation_id() {
      return $this->resource_reservation_id;
    }
      
    /**
     * Sets resource_reservation_id
     *
     * @param   int resource_reservation_id
     * @return  int the previous value
     */
    public function setResource_reservation_id($resource_reservation_id) {
      return $this->_change('resource_reservation_id', $resource_reservation_id);
    }

    /**
     * Retrieves resourceng_id
     *
     * @return  int
     */
    public function getResourceng_id() {
      return $this->resourceng_id;
    }
      
    /**
     * Sets resourceng_id
     *
     * @param   int resourceng_id
     * @return  int the previous value
     */
    public function setResourceng_id($resourceng_id) {
      return $this->_change('resourceng_id', $resourceng_id);
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
     * Retrieves lastcached
     *
     * @return  util.Date
     */
    public function getLastcached() {
      return $this->lastcached;
    }
      
    /**
     * Sets lastcached
     *
     * @param   util.Date lastcached
     * @return  util.Date the previous value
     */
    public function setLastcached($lastcached) {
      return $this->_change('lastcached', $lastcached);
    }

    /**
     * Retrieves expiredate
     *
     * @return  util.Date
     */
    public function getExpiredate() {
      return $this->expiredate;
    }
      
    /**
     * Sets expiredate
     *
     * @param   util.Date expiredate
     * @return  util.Date the previous value
     */
    public function setExpiredate($expiredate) {
      return $this->_change('expiredate', $expiredate);
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
     * Retrieves the Resourceng entity
     * referenced by resourceng_id=>resourceng_id
     *
     * @return  de.schlund.db.methadon.Resourceng entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getResourceng() {
      $r= XPClass::forName('de.schlund.db.methadon.Resourceng')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('resourceng_id', $this->getResourceng_id(), EQUAL)
      ));
      return $r ? $r[0] : NULL;
    }

    /**
     * Retrieves an array of all Reservation_criteria entities referencing
     * this entity by resource_reservation_id=>resource_reservation_id
     *
     * @return  de.schlund.db.methadon.Reservation_criteria[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getReservation_criteriaResource_reservationList() {
      return XPClass::forName('de.schlund.db.methadon.Reservation_criteria')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('resource_reservation_id', $this->getResource_reservation_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Reservation_criteria entities referencing
     * this entity by resource_reservation_id=>resource_reservation_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Reservation_criteria>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getReservation_criteriaResource_reservationIterator() {
      return XPClass::forName('de.schlund.db.methadon.Reservation_criteria')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('resource_reservation_id', $this->getResource_reservation_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Reservation_history entities referencing
     * this entity by resource_reservation_id=>resource_reservation_id
     *
     * @return  de.schlund.db.methadon.Reservation_history[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getReservation_historyResource_reservationList() {
      return XPClass::forName('de.schlund.db.methadon.Reservation_history')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('resource_reservation_id', $this->getResource_reservation_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Reservation_history entities referencing
     * this entity by resource_reservation_id=>resource_reservation_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Reservation_history>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getReservation_historyResource_reservationIterator() {
      return XPClass::forName('de.schlund.db.methadon.Reservation_history')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('resource_reservation_id', $this->getResource_reservation_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Reservation_exception entities referencing
     * this entity by resource_reservation_id=>resource_reservation_id
     *
     * @return  de.schlund.db.methadon.Reservation_exception[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getReservation_exceptionResource_reservationList() {
      return XPClass::forName('de.schlund.db.methadon.Reservation_exception')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('resource_reservation_id', $this->getResource_reservation_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Reservation_exception entities referencing
     * this entity by resource_reservation_id=>resource_reservation_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Reservation_exception>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getReservation_exceptionResource_reservationIterator() {
      return XPClass::forName('de.schlund.db.methadon.Reservation_exception')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('resource_reservation_id', $this->getResource_reservation_id(), EQUAL)
      ));
    }
  }
?>