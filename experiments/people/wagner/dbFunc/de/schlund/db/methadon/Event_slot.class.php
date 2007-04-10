<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet');

  /**
   * Class wrapper for table event_slot, database METHADON
   * (Auto-generated on Wed, 04 Apr 2007 10:45:27 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class Event_slot extends DataSet {

    protected
      $_isLoaded= false,
      $_loadCrit= NULL;

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('METHADON..event_slot');
        $peer->setConnection('sybintern');
        $peer->setIdentity('event_slot_id');
        $peer->setPrimary(array('event_slot_id'));
        $peer->setTypes(array(
          'changedby'           => array('%s', FieldType::VARCHAR, FALSE),
          'cnt_attendee'        => array('%d', FieldType::INT, FALSE),
          'feature'             => array('%d', FieldType::INT, FALSE),
          'event_slot_id'       => array('%d', FieldType::NUMERIC, FALSE),
          'event_id'            => array('%d', FieldType::NUMERIC, FALSE),
          'bz_id'               => array('%d', FieldType::NUMERIC, FALSE),
          'ts_start'            => array('%s', FieldType::DATETIME, FALSE),
          'ts_end'              => array('%s', FieldType::DATETIME, FALSE),
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
     * Gets an instance of this object by index "PK_EVENTSLOT"
     * 
     * @param   int event_slot_id
     * @return  de.schlund.db.methadon.Event_slot entitiy object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByEvent_slot_id($event_slot_id) {
      return new self(array(
        'event_slot_id'  => $event_slot_id,
        '_loadCrit' => new Criteria(array('event_slot_id', $event_slot_id, EQUAL))
      ));
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
     * Retrieves cnt_attendee
     *
     * @return  int
     */
    public function getCnt_attendee() {
      return $this->cnt_attendee;
    }
      
    /**
     * Sets cnt_attendee
     *
     * @param   int cnt_attendee
     * @return  int the previous value
     */
    public function setCnt_attendee($cnt_attendee) {
      return $this->_change('cnt_attendee', $cnt_attendee);
    }

    /**
     * Retrieves feature
     *
     * @return  int
     */
    public function getFeature() {
      return $this->feature;
    }
      
    /**
     * Sets feature
     *
     * @param   int feature
     * @return  int the previous value
     */
    public function setFeature($feature) {
      return $this->_change('feature', $feature);
    }

    /**
     * Retrieves event_slot_id
     *
     * @return  int
     */
    public function getEvent_slot_id() {
      return $this->event_slot_id;
    }
      
    /**
     * Sets event_slot_id
     *
     * @param   int event_slot_id
     * @return  int the previous value
     */
    public function setEvent_slot_id($event_slot_id) {
      return $this->_change('event_slot_id', $event_slot_id);
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
     * Retrieves ts_start
     *
     * @return  util.Date
     */
    public function getTs_start() {
      return $this->ts_start;
    }
      
    /**
     * Sets ts_start
     *
     * @param   util.Date ts_start
     * @return  util.Date the previous value
     */
    public function setTs_start($ts_start) {
      return $this->_change('ts_start', $ts_start);
    }

    /**
     * Retrieves ts_end
     *
     * @return  util.Date
     */
    public function getTs_end() {
      return $this->ts_end;
    }
      
    /**
     * Sets ts_end
     *
     * @param   util.Date ts_end
     * @return  util.Date the previous value
     */
    public function setTs_end($ts_end) {
      return $this->_change('ts_end', $ts_end);
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
     * Retrieves the Event entity
     * referenced by event_id=>event_id
     *
     * @return  de.schlund.db.methadon.Event entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEvent() {
      $r= XPClass::forName('de.schlund.db.methadon.Event')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('event_id', $this->getEvent_id(), EQUAL)
      ));
      return $r ? $r[0] : NULL;
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
     * Retrieves an array of all Event_person_matrix entities referencing
     * this entity by event_slot_id=>event_slot_id
     *
     * @return  de.schlund.db.methadon.Event_person_matrix[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEvent_person_matrixEvent_slotList() {
      return XPClass::forName('de.schlund.db.methadon.Event_person_matrix')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('event_slot_id', $this->getEvent_slot_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Event_person_matrix entities referencing
     * this entity by event_slot_id=>event_slot_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Event_person_matrix>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEvent_person_matrixEvent_slotIterator() {
      return XPClass::forName('de.schlund.db.methadon.Event_person_matrix')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('event_slot_id', $this->getEvent_slot_id(), EQUAL)
      ));
    }
  }
?>