<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet');

  /**
   * Class wrapper for table event_template_slot, database METHADON
   * (Auto-generated on Wed, 04 Apr 2007 10:45:27 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class Event_template_slot extends DataSet {

    protected
      $_isLoaded= false,
      $_loadCrit= NULL;

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('METHADON..event_template_slot');
        $peer->setConnection('sybintern');
        $peer->setIdentity('event_template_slot_id');
        $peer->setPrimary(array('event_template_slot_id'));
        $peer->setTypes(array(
          'changedby'           => array('%s', FieldType::VARCHAR, FALSE),
          'int_start'           => array('%d', FieldType::INT, FALSE),
          'int_end'             => array('%d', FieldType::INT, FALSE),
          'cnt_attendee'        => array('%d', FieldType::INT, FALSE),
          'feature'             => array('%d', FieldType::INT, FALSE),
          'event_template_slot_id' => array('%d', FieldType::NUMERIC, FALSE),
          'event_template_id'   => array('%d', FieldType::NUMERIC, FALSE),
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
     * Gets an instance of this object by index "PK_EVENTTPSLOT"
     * 
     * @param   int event_template_slot_id
     * @return  de.schlund.db.methadon.Event_template_slot entitiy object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByEvent_template_slot_id($event_template_slot_id) {
      return new self(array(
        'event_template_slot_id'  => $event_template_slot_id,
        '_loadCrit' => new Criteria(array('event_template_slot_id', $event_template_slot_id, EQUAL))
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
     * Retrieves int_start
     *
     * @return  int
     */
    public function getInt_start() {
      return $this->int_start;
    }
      
    /**
     * Sets int_start
     *
     * @param   int int_start
     * @return  int the previous value
     */
    public function setInt_start($int_start) {
      return $this->_change('int_start', $int_start);
    }

    /**
     * Retrieves int_end
     *
     * @return  int
     */
    public function getInt_end() {
      return $this->int_end;
    }
      
    /**
     * Sets int_end
     *
     * @param   int int_end
     * @return  int the previous value
     */
    public function setInt_end($int_end) {
      return $this->_change('int_end', $int_end);
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
     * Retrieves event_template_slot_id
     *
     * @return  int
     */
    public function getEvent_template_slot_id() {
      return $this->event_template_slot_id;
    }
      
    /**
     * Sets event_template_slot_id
     *
     * @param   int event_template_slot_id
     * @return  int the previous value
     */
    public function setEvent_template_slot_id($event_template_slot_id) {
      return $this->_change('event_template_slot_id', $event_template_slot_id);
    }

    /**
     * Retrieves event_template_id
     *
     * @return  int
     */
    public function getEvent_template_id() {
      return $this->event_template_id;
    }
      
    /**
     * Sets event_template_id
     *
     * @param   int event_template_id
     * @return  int the previous value
     */
    public function setEvent_template_id($event_template_id) {
      return $this->_change('event_template_id', $event_template_id);
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
     * Retrieves the Event_template entity
     * referenced by event_template_id=>event_template_id
     *
     * @return  de.schlund.db.methadon.Event_template entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEvent_template() {
      $r= XPClass::forName('de.schlund.db.methadon.Event_template')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('event_template_id', $this->getEvent_template_id(), EQUAL)
      ));
      return $r ? $r[0] : NULL;
    }
  }
?>