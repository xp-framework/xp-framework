<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet');

  /**
   * Class wrapper for table ts_user_history, database METHADON
   * (Auto-generated on Wed, 04 Apr 2007 10:45:27 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class Ts_user_history extends DataSet {

    protected
      $_isLoaded= false,
      $_loadCrit= NULL;

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('METHADON..ts_user_history');
        $peer->setConnection('sybintern');
        $peer->setPrimary(array(''));
        $peer->setTypes(array(
          'currency'            => array('%s', FieldType::VARCHAR, FALSE),
          'new_currency'        => array('%s', FieldType::VARCHAR, FALSE),
          'changedby'           => array('%s', FieldType::VARCHAR, FALSE),
          'type'                => array('%d', FieldType::SMALLINT, FALSE),
          'person_id'           => array('%d', FieldType::NUMERIC, FALSE),
          'bz_id'               => array('%d', FieldType::NUMERIC, FALSE),
          'hourly'              => array('%f', FieldType::MONEY, FALSE),
          'new_hourly'          => array('%f', FieldType::MONEY, FALSE),
          'lastchange'          => array('%s', FieldType::DATETIME, FALSE),
          'start_date'          => array('%s', FieldType::DATETIMN, TRUE)
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
     * Retrieves currency
     *
     * @return  string
     */
    public function getCurrency() {
      return $this->currency;
    }
      
    /**
     * Sets currency
     *
     * @param   string currency
     * @return  string the previous value
     */
    public function setCurrency($currency) {
      return $this->_change('currency', $currency);
    }

    /**
     * Retrieves new_currency
     *
     * @return  string
     */
    public function getNew_currency() {
      return $this->new_currency;
    }
      
    /**
     * Sets new_currency
     *
     * @param   string new_currency
     * @return  string the previous value
     */
    public function setNew_currency($new_currency) {
      return $this->_change('new_currency', $new_currency);
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
     * Retrieves type
     *
     * @return  int
     */
    public function getType() {
      return $this->type;
    }
      
    /**
     * Sets type
     *
     * @param   int type
     * @return  int the previous value
     */
    public function setType($type) {
      return $this->_change('type', $type);
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
     * Retrieves hourly
     *
     * @return  float
     */
    public function getHourly() {
      return $this->hourly;
    }
      
    /**
     * Sets hourly
     *
     * @param   float hourly
     * @return  float the previous value
     */
    public function setHourly($hourly) {
      return $this->_change('hourly', $hourly);
    }

    /**
     * Retrieves new_hourly
     *
     * @return  float
     */
    public function getNew_hourly() {
      return $this->new_hourly;
    }
      
    /**
     * Sets new_hourly
     *
     * @param   float new_hourly
     * @return  float the previous value
     */
    public function setNew_hourly($new_hourly) {
      return $this->_change('new_hourly', $new_hourly);
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
  }
?>