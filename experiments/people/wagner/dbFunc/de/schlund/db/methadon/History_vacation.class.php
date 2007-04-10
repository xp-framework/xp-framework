<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet');

  /**
   * Class wrapper for table history_vacation, database METHADON
   * (Auto-generated on Wed, 04 Apr 2007 10:45:27 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class History_vacation extends DataSet {

    protected
      $_isLoaded= false,
      $_loadCrit= NULL;

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('METHADON..history_vacation');
        $peer->setConnection('sybintern');
        $peer->setIdentity('history_vacation_id');
        $peer->setPrimary(array('history_vacation_id'));
        $peer->setTypes(array(
          'substitute'          => array('%s', FieldType::VARCHAR, TRUE),
          'changedby'           => array('%s', FieldType::VARCHAR, FALSE),
          'days'                => array('%d', FieldType::INT, FALSE),
          'states'              => array('%d', FieldType::INT, FALSE),
          'history_vacation_id' => array('%d', FieldType::NUMERIC, FALSE),
          'vacation_id'         => array('%d', FieldType::NUMERIC, FALSE),
          'person_id'           => array('%d', FieldType::NUMERIC, FALSE),
          'bz_id'               => array('%d', FieldType::NUMERIC, FALSE),
          'ts_init'             => array('%s', FieldType::SMALLDATETIME, FALSE),
          'start_date'          => array('%s', FieldType::SMALLDATETIME, FALSE),
          'end_date'            => array('%s', FieldType::SMALLDATETIME, FALSE),
          'lastchange'          => array('%s', FieldType::SMALLDATETIME, FALSE),
          'reference_id'        => array('%d', FieldType::NUMERICN, TRUE)
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
     * Gets an instance of this object by index "PK_HISTORY_VACATION"
     * 
     * @param   int history_vacation_id
     * @return  de.schlund.db.methadon.History_vacation entitiy object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByHistory_vacation_id($history_vacation_id) {
      return new self(array(
        'history_vacation_id'  => $history_vacation_id,
        '_loadCrit' => new Criteria(array('history_vacation_id', $history_vacation_id, EQUAL))
      ));
    }

    /**
     * Retrieves substitute
     *
     * @return  string
     */
    public function getSubstitute() {
      return $this->substitute;
    }
      
    /**
     * Sets substitute
     *
     * @param   string substitute
     * @return  string the previous value
     */
    public function setSubstitute($substitute) {
      return $this->_change('substitute', $substitute);
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
     * Retrieves days
     *
     * @return  int
     */
    public function getDays() {
      return $this->days;
    }
      
    /**
     * Sets days
     *
     * @param   int days
     * @return  int the previous value
     */
    public function setDays($days) {
      return $this->_change('days', $days);
    }

    /**
     * Retrieves states
     *
     * @return  int
     */
    public function getStates() {
      return $this->states;
    }
      
    /**
     * Sets states
     *
     * @param   int states
     * @return  int the previous value
     */
    public function setStates($states) {
      return $this->_change('states', $states);
    }

    /**
     * Retrieves history_vacation_id
     *
     * @return  int
     */
    public function getHistory_vacation_id() {
      return $this->history_vacation_id;
    }
      
    /**
     * Sets history_vacation_id
     *
     * @param   int history_vacation_id
     * @return  int the previous value
     */
    public function setHistory_vacation_id($history_vacation_id) {
      return $this->_change('history_vacation_id', $history_vacation_id);
    }

    /**
     * Retrieves vacation_id
     *
     * @return  int
     */
    public function getVacation_id() {
      return $this->vacation_id;
    }
      
    /**
     * Sets vacation_id
     *
     * @param   int vacation_id
     * @return  int the previous value
     */
    public function setVacation_id($vacation_id) {
      return $this->_change('vacation_id', $vacation_id);
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
     * Retrieves ts_init
     *
     * @return  util.Date
     */
    public function getTs_init() {
      return $this->ts_init;
    }
      
    /**
     * Sets ts_init
     *
     * @param   util.Date ts_init
     * @return  util.Date the previous value
     */
    public function setTs_init($ts_init) {
      return $this->_change('ts_init', $ts_init);
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
     * Retrieves reference_id
     *
     * @return  int
     */
    public function getReference_id() {
      return $this->reference_id;
    }
      
    /**
     * Sets reference_id
     *
     * @param   int reference_id
     * @return  int the previous value
     */
    public function setReference_id($reference_id) {
      return $this->_change('reference_id', $reference_id);
    }
  }
?>