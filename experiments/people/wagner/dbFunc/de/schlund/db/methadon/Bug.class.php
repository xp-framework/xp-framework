<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet');

  /**
   * Class wrapper for table bug, database METHADON
   * (Auto-generated on Wed, 04 Apr 2007 10:45:27 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class Bug extends DataSet {

    protected
      $_isLoaded= false,
      $_loadCrit= NULL;

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('METHADON..bug');
        $peer->setConnection('sybintern');
        $peer->setIdentity('bug_id');
        $peer->setPrimary(array('bug_id'));
        $peer->setTypes(array(
          'changedby'           => array('%s', FieldType::VARCHAR, FALSE),
          'bug_id'              => array('%d', FieldType::NUMERIC, FALSE),
          'reporter_id'         => array('%d', FieldType::NUMERIC, FALSE),
          'bz_id'               => array('%d', FieldType::NUMERIC, FALSE),
          'feature'             => array('%d', FieldType::INTN, TRUE),
          'ts_init'             => array('%s', FieldType::SMALLDATETIME, FALSE),
          'lastchange'          => array('%s', FieldType::SMALLDATETIME, FALSE)
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
     * Gets an instance of this object by index "PK_bug"
     * 
     * @param   int bug_id
     * @return  de.schlund.db.methadon.Bug entitiy object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByBug_id($bug_id) {
      return new self(array(
        'bug_id'  => $bug_id,
        '_loadCrit' => new Criteria(array('bug_id', $bug_id, EQUAL))
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
     * Retrieves bug_id
     *
     * @return  int
     */
    public function getBug_id() {
      return $this->bug_id;
    }
      
    /**
     * Sets bug_id
     *
     * @param   int bug_id
     * @return  int the previous value
     */
    public function setBug_id($bug_id) {
      return $this->_change('bug_id', $bug_id);
    }

    /**
     * Retrieves reporter_id
     *
     * @return  int
     */
    public function getReporter_id() {
      return $this->reporter_id;
    }
      
    /**
     * Sets reporter_id
     *
     * @param   int reporter_id
     * @return  int the previous value
     */
    public function setReporter_id($reporter_id) {
      return $this->_change('reporter_id', $reporter_id);
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
     * Retrieves the Person entity
     * referenced by person_id=>reporter_id
     *
     * @return  de.schlund.db.methadon.Person entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getReporter() {
      $r= XPClass::forName('de.schlund.db.methadon.Person')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('person_id', $this->getReporter_id(), EQUAL)
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
     * Retrieves an array of all Bug_notify entities referencing
     * this entity by bug_id=>bug_id
     *
     * @return  de.schlund.db.methadon.Bug_notify[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getBug_notifyBugList() {
      return XPClass::forName('de.schlund.db.methadon.Bug_notify')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('bug_id', $this->getBug_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Bug_notify entities referencing
     * this entity by bug_id=>bug_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Bug_notify>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getBug_notifyBugIterator() {
      return XPClass::forName('de.schlund.db.methadon.Bug_notify')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('bug_id', $this->getBug_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Bug_history entities referencing
     * this entity by bug_id=>bug_id
     *
     * @return  de.schlund.db.methadon.Bug_history[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getBug_historyBugList() {
      return XPClass::forName('de.schlund.db.methadon.Bug_history')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('bug_id', $this->getBug_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Bug_history entities referencing
     * this entity by bug_id=>bug_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Bug_history>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getBug_historyBugIterator() {
      return XPClass::forName('de.schlund.db.methadon.Bug_history')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('bug_id', $this->getBug_id(), EQUAL)
      ));
    }
  }
?>