<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet');

  /**
   * Class wrapper for table reservation_criteria, database METHADON
   * (Auto-generated on Wed, 04 Apr 2007 10:45:27 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class Reservation_criteria extends DataSet {

    protected
      $_isLoaded= false,
      $_loadCrit= NULL;

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('METHADON..reservation_criteria');
        $peer->setConnection('sybintern');
        $peer->setIdentity('reservation_criteria_id');
        $peer->setPrimary(array('reservation_criteria_id'));
        $peer->setTypes(array(
          'changedby'           => array('%s', FieldType::VARCHAR, FALSE),
          'reservation_criteria_id' => array('%d', FieldType::NUMERIC, FALSE),
          'resource_reservation_id' => array('%d', FieldType::NUMERIC, FALSE),
          'bz_id'               => array('%d', FieldType::NUMERIC, FALSE),
          'lastchange'          => array('%s', FieldType::DATETIME, FALSE),
          'criteria_repeat'     => array('%d', FieldType::INTN, TRUE),
          'criteria_type_repeat' => array('%d', FieldType::INTN, TRUE),
          'dayofmonth'          => array('%d', FieldType::INTN, TRUE),
          'weekofmonth'         => array('%d', FieldType::INTN, TRUE),
          'dayofweek'           => array('%d', FieldType::INTN, TRUE),
          'end_date'            => array('%s', FieldType::DATETIMN, TRUE),
          'recurrence_number'   => array('%d', FieldType::NUMERICN, TRUE)
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
     * Gets an instance of this object by index "reservatio_reserv_1161768165"
     * 
     * @param   int reservation_criteria_id
     * @return  de.schlund.db.methadon.Reservation_criteria entitiy object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByReservation_criteria_id($reservation_criteria_id) {
      return new self(array(
        'reservation_criteria_id'  => $reservation_criteria_id,
        '_loadCrit' => new Criteria(array('reservation_criteria_id', $reservation_criteria_id, EQUAL))
      ));
    }

    /**
     * Gets an instance of this object by index "i_resng_recc_table"
     * 
     * @param   int resource_reservation_id
     * @return  de.schlund.db.methadon.Reservation_criteria[] entity objects
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByResource_reservation_id($resource_reservation_id) {
      $r= self::getPeer()->doSelect(new Criteria(array('resource_reservation_id', $resource_reservation_id, EQUAL)));
      foreach ($r as $e) $e->_isLoaded= true;
      return $r;
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
     * Retrieves reservation_criteria_id
     *
     * @return  int
     */
    public function getReservation_criteria_id() {
      return $this->reservation_criteria_id;
    }
      
    /**
     * Sets reservation_criteria_id
     *
     * @param   int reservation_criteria_id
     * @return  int the previous value
     */
    public function setReservation_criteria_id($reservation_criteria_id) {
      return $this->_change('reservation_criteria_id', $reservation_criteria_id);
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
     * Retrieves criteria_repeat
     *
     * @return  int
     */
    public function getCriteria_repeat() {
      return $this->criteria_repeat;
    }
      
    /**
     * Sets criteria_repeat
     *
     * @param   int criteria_repeat
     * @return  int the previous value
     */
    public function setCriteria_repeat($criteria_repeat) {
      return $this->_change('criteria_repeat', $criteria_repeat);
    }

    /**
     * Retrieves criteria_type_repeat
     *
     * @return  int
     */
    public function getCriteria_type_repeat() {
      return $this->criteria_type_repeat;
    }
      
    /**
     * Sets criteria_type_repeat
     *
     * @param   int criteria_type_repeat
     * @return  int the previous value
     */
    public function setCriteria_type_repeat($criteria_type_repeat) {
      return $this->_change('criteria_type_repeat', $criteria_type_repeat);
    }

    /**
     * Retrieves dayofmonth
     *
     * @return  int
     */
    public function getDayofmonth() {
      return $this->dayofmonth;
    }
      
    /**
     * Sets dayofmonth
     *
     * @param   int dayofmonth
     * @return  int the previous value
     */
    public function setDayofmonth($dayofmonth) {
      return $this->_change('dayofmonth', $dayofmonth);
    }

    /**
     * Retrieves weekofmonth
     *
     * @return  int
     */
    public function getWeekofmonth() {
      return $this->weekofmonth;
    }
      
    /**
     * Sets weekofmonth
     *
     * @param   int weekofmonth
     * @return  int the previous value
     */
    public function setWeekofmonth($weekofmonth) {
      return $this->_change('weekofmonth', $weekofmonth);
    }

    /**
     * Retrieves dayofweek
     *
     * @return  int
     */
    public function getDayofweek() {
      return $this->dayofweek;
    }
      
    /**
     * Sets dayofweek
     *
     * @param   int dayofweek
     * @return  int the previous value
     */
    public function setDayofweek($dayofweek) {
      return $this->_change('dayofweek', $dayofweek);
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
     * Retrieves recurrence_number
     *
     * @return  int
     */
    public function getRecurrence_number() {
      return $this->recurrence_number;
    }
      
    /**
     * Sets recurrence_number
     *
     * @param   int recurrence_number
     * @return  int the previous value
     */
    public function setRecurrence_number($recurrence_number) {
      return $this->_change('recurrence_number', $recurrence_number);
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
     * Retrieves the Resource_reservation entity
     * referenced by resource_reservation_id=>resource_reservation_id
     *
     * @return  de.schlund.db.methadon.Resource_reservation entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getResource_reservation() {
      $r= XPClass::forName('de.schlund.db.methadon.Resource_reservation')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('resource_reservation_id', $this->getResource_reservation_id(), EQUAL)
      ));
      return $r ? $r[0] : NULL;
    }
  }
?>