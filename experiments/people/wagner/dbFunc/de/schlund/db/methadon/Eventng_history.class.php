<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet');

  /**
   * Class wrapper for table eventng_history, database METHADON
   * (Auto-generated on Wed, 04 Apr 2007 10:45:27 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class Eventng_history extends DataSet {

    protected
      $_isLoaded= false,
      $_loadCrit= NULL;

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('METHADON..eventng_history');
        $peer->setConnection('sybintern');
        $peer->setIdentity('eventng_history_id');
        $peer->setPrimary(array('eventng_history_id'));
        $peer->setTypes(array(
          'changedby'           => array('%s', FieldType::VARCHAR, FALSE),
          'eventng_history_id'  => array('%d', FieldType::NUMERIC, FALSE),
          'eventng_id'          => array('%d', FieldType::NUMERIC, FALSE),
          'bz_id'               => array('%d', FieldType::NUMERIC, FALSE),
          'apparition_date'     => array('%s', FieldType::DATETIME, FALSE),
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
     * Gets an instance of this object by index "eventng_hi_eventn_3777653721"
     * 
     * @param   int eventng_history_id
     * @return  de.schlund.db.methadon.Eventng_history entitiy object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByEventng_history_id($eventng_history_id) {
      return new self(array(
        'eventng_history_id'  => $eventng_history_id,
        '_loadCrit' => new Criteria(array('eventng_history_id', $eventng_history_id, EQUAL))
      ));
    }

    /**
     * Gets an instance of this object by index "i_eventng_history_table"
     * 
     * @param   int eventng_id
     * @return  de.schlund.db.methadon.Eventng_history[] entity objects
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByEventng_id($eventng_id) {
      $r= self::getPeer()->doSelect(new Criteria(array('eventng_id', $eventng_id, EQUAL)));
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
     * Retrieves eventng_history_id
     *
     * @return  int
     */
    public function getEventng_history_id() {
      return $this->eventng_history_id;
    }
      
    /**
     * Sets eventng_history_id
     *
     * @param   int eventng_history_id
     * @return  int the previous value
     */
    public function setEventng_history_id($eventng_history_id) {
      return $this->_change('eventng_history_id', $eventng_history_id);
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
     * Retrieves apparition_date
     *
     * @return  util.Date
     */
    public function getApparition_date() {
      return $this->apparition_date;
    }
      
    /**
     * Sets apparition_date
     *
     * @param   util.Date apparition_date
     * @return  util.Date the previous value
     */
    public function setApparition_date($apparition_date) {
      return $this->_change('apparition_date', $apparition_date);
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