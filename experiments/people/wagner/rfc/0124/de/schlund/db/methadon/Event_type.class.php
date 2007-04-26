<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet');

  /**
   * Class wrapper for table event_type, database METHADON
   * (Auto-generated on Wed, 04 Apr 2007 10:45:27 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class Event_type extends DataSet {

    protected
      $_isLoaded= false,
      $_loadCrit= NULL;

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('METHADON..event_type');
        $peer->setConnection('sybintern');
        $peer->setPrimary(array('event_type_id'));
        $peer->setTypes(array(
          'name'                => array('%s', FieldType::VARCHAR, FALSE),
          'description'         => array('%s', FieldType::VARCHAR, TRUE),
          'changedby'           => array('%s', FieldType::VARCHAR, FALSE),
          'feature'             => array('%d', FieldType::INT, FALSE),
          'event_type_id'       => array('%d', FieldType::NUMERIC, FALSE),
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
     * Gets an instance of this object by index "PK_EVENTTYPE"
     * 
     * @param   int event_type_id
     * @return  de.schlund.db.methadon.Event_type entitiy object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByEvent_type_id($event_type_id) {
      return new self(array(
        'event_type_id'  => $event_type_id,
        '_loadCrit' => new Criteria(array('event_type_id', $event_type_id, EQUAL))
      ));
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
     * Retrieves an array of all Event entities referencing
     * this entity by event_type_id=>event_type_id
     *
     * @return  de.schlund.db.methadon.Event[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEventEvent_typeList() {
      return XPClass::forName('de.schlund.db.methadon.Event')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('event_type_id', $this->getEvent_type_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Event entities referencing
     * this entity by event_type_id=>event_type_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Event>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEventEvent_typeIterator() {
      return XPClass::forName('de.schlund.db.methadon.Event')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('event_type_id', $this->getEvent_type_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Event_template entities referencing
     * this entity by event_type_id=>event_type_id
     *
     * @return  de.schlund.db.methadon.Event_template[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEvent_templateEvent_typeList() {
      return XPClass::forName('de.schlund.db.methadon.Event_template')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('event_type_id', $this->getEvent_type_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Event_template entities referencing
     * this entity by event_type_id=>event_type_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Event_template>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEvent_templateEvent_typeIterator() {
      return XPClass::forName('de.schlund.db.methadon.Event_template')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('event_type_id', $this->getEvent_type_id(), EQUAL)
      ));
    }
  }
?>