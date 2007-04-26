<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet');

  /**
   * Class wrapper for table eventng_recurrence_criteria, database METHADON
   * (Auto-generated on Wed, 04 Apr 2007 10:45:27 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class Eventng_recurrence_criteria extends DataSet {

    protected
      $_isLoaded= false,
      $_loadCrit= NULL;

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('METHADON..eventng_recurrence_criteria');
        $peer->setConnection('sybintern');
        $peer->setIdentity('eventng_recurrence_criteria_id');
        $peer->setPrimary(array(''));
        $peer->setTypes(array(
          'criteria_name'       => array('%s', FieldType::VARCHAR, TRUE),
          'criteria_description' => array('%s', FieldType::VARCHAR, TRUE),
          'dayofmonth'          => array('%s', FieldType::VARCHAR, TRUE),
          'changedby'           => array('%s', FieldType::VARCHAR, FALSE),
          'eventng_recurrence_criteria_id' => array('%d', FieldType::NUMERIC, FALSE),
          'eventng_id'          => array('%d', FieldType::NUMERIC, FALSE),
          'bz_id'               => array('%d', FieldType::NUMERIC, FALSE),
          'lastchange'          => array('%s', FieldType::DATETIME, FALSE),
          'criteria_repeat'     => array('%d', FieldType::INTN, TRUE),
          'criteria_type_repeat' => array('%d', FieldType::INTN, TRUE),
          'weekofmonth'         => array('%d', FieldType::INTN, TRUE),
          'dayofweek'           => array('%d', FieldType::INTN, TRUE)
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
     * Retrieves criteria_name
     *
     * @return  string
     */
    public function getCriteria_name() {
      return $this->criteria_name;
    }
      
    /**
     * Sets criteria_name
     *
     * @param   string criteria_name
     * @return  string the previous value
     */
    public function setCriteria_name($criteria_name) {
      return $this->_change('criteria_name', $criteria_name);
    }

    /**
     * Retrieves criteria_description
     *
     * @return  string
     */
    public function getCriteria_description() {
      return $this->criteria_description;
    }
      
    /**
     * Sets criteria_description
     *
     * @param   string criteria_description
     * @return  string the previous value
     */
    public function setCriteria_description($criteria_description) {
      return $this->_change('criteria_description', $criteria_description);
    }

    /**
     * Retrieves dayofmonth
     *
     * @return  string
     */
    public function getDayofmonth() {
      return $this->dayofmonth;
    }
      
    /**
     * Sets dayofmonth
     *
     * @param   string dayofmonth
     * @return  string the previous value
     */
    public function setDayofmonth($dayofmonth) {
      return $this->_change('dayofmonth', $dayofmonth);
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
     * Retrieves eventng_recurrence_criteria_id
     *
     * @return  int
     */
    public function getEventng_recurrence_criteria_id() {
      return $this->eventng_recurrence_criteria_id;
    }
      
    /**
     * Sets eventng_recurrence_criteria_id
     *
     * @param   int eventng_recurrence_criteria_id
     * @return  int the previous value
     */
    public function setEventng_recurrence_criteria_id($eventng_recurrence_criteria_id) {
      return $this->_change('eventng_recurrence_criteria_id', $eventng_recurrence_criteria_id);
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
     * Retrieves the Eventng entity
     * referenced by eventng_id=>eventng_id
     *
     * @return  de.schlund.db.methadon.Eventng entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEventng() {
      $r= XPClass::forName('de.schlund.db.methadon.Eventng')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('eventng_id', $this->getEventng_id(), EQUAL)
      ));
      return $r ? $r[0] : NULL;
    }
  }
?>