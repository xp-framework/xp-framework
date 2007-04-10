<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet');

  /**
   * Class wrapper for table program_schedule, database METHADON
   * (Auto-generated on Wed, 04 Apr 2007 10:45:27 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class Program_schedule extends DataSet {

    protected
      $_isLoaded= false,
      $_loadCrit= NULL;

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('METHADON..program_schedule');
        $peer->setConnection('sybintern');
        $peer->setIdentity('program_schedule_id');
        $peer->setPrimary(array('program_schedule_id'));
        $peer->setTypes(array(
          'name'                => array('%s', FieldType::VARCHAR, FALSE),
          'description'         => array('%s', FieldType::VARCHAR, TRUE),
          'program'             => array('%s', FieldType::VARCHAR, FALSE),
          'params'              => array('%s', FieldType::VARCHAR, TRUE),
          'laststatus'          => array('%s', FieldType::VARCHAR, TRUE),
          'hostlocation'        => array('%s', FieldType::VARCHAR, FALSE),
          'changedby'           => array('%s', FieldType::VARCHAR, FALSE),
          'interval_sec'        => array('%d', FieldType::INT, FALSE),
          'program_schedule_id' => array('%d', FieldType::NUMERIC, FALSE),
          'tool_id'             => array('%d', FieldType::NUMERIC, FALSE),
          'maintainer_id'       => array('%d', FieldType::NUMERIC, FALSE),
          'bz_id'               => array('%d', FieldType::NUMERIC, FALSE),
          'lastrun'             => array('%s', FieldType::DATETIME, FALSE),
          'nextrun'             => array('%s', FieldType::DATETIME, FALSE),
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
     * Gets an instance of this object by index "program_schedule_I0"
     * 
     * @param   util.Date lastrun
     * @return  de.schlund.db.methadon.Program_schedule[] entity objects
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByLastrun($lastrun) {
      $r= self::getPeer()->doSelect(new Criteria(array('lastrun', $lastrun, EQUAL)));
      foreach ($r as $e) $e->_isLoaded= true;
      return $r;
    }

    /**
     * Gets an instance of this object by index "PK_PROGRAMSCHEDULE"
     * 
     * @param   int program_schedule_id
     * @return  de.schlund.db.methadon.Program_schedule entitiy object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByProgram_schedule_id($program_schedule_id) {
      return new self(array(
        'program_schedule_id'  => $program_schedule_id,
        '_loadCrit' => new Criteria(array('program_schedule_id', $program_schedule_id, EQUAL))
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
     * Retrieves program
     *
     * @return  string
     */
    public function getProgram() {
      return $this->program;
    }
      
    /**
     * Sets program
     *
     * @param   string program
     * @return  string the previous value
     */
    public function setProgram($program) {
      return $this->_change('program', $program);
    }

    /**
     * Retrieves params
     *
     * @return  string
     */
    public function getParams() {
      return $this->params;
    }
      
    /**
     * Sets params
     *
     * @param   string params
     * @return  string the previous value
     */
    public function setParams($params) {
      return $this->_change('params', $params);
    }

    /**
     * Retrieves laststatus
     *
     * @return  string
     */
    public function getLaststatus() {
      return $this->laststatus;
    }
      
    /**
     * Sets laststatus
     *
     * @param   string laststatus
     * @return  string the previous value
     */
    public function setLaststatus($laststatus) {
      return $this->_change('laststatus', $laststatus);
    }

    /**
     * Retrieves hostlocation
     *
     * @return  string
     */
    public function getHostlocation() {
      return $this->hostlocation;
    }
      
    /**
     * Sets hostlocation
     *
     * @param   string hostlocation
     * @return  string the previous value
     */
    public function setHostlocation($hostlocation) {
      return $this->_change('hostlocation', $hostlocation);
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
     * Retrieves interval_sec
     *
     * @return  int
     */
    public function getInterval_sec() {
      return $this->interval_sec;
    }
      
    /**
     * Sets interval_sec
     *
     * @param   int interval_sec
     * @return  int the previous value
     */
    public function setInterval_sec($interval_sec) {
      return $this->_change('interval_sec', $interval_sec);
    }

    /**
     * Retrieves program_schedule_id
     *
     * @return  int
     */
    public function getProgram_schedule_id() {
      return $this->program_schedule_id;
    }
      
    /**
     * Sets program_schedule_id
     *
     * @param   int program_schedule_id
     * @return  int the previous value
     */
    public function setProgram_schedule_id($program_schedule_id) {
      return $this->_change('program_schedule_id', $program_schedule_id);
    }

    /**
     * Retrieves tool_id
     *
     * @return  int
     */
    public function getTool_id() {
      return $this->tool_id;
    }
      
    /**
     * Sets tool_id
     *
     * @param   int tool_id
     * @return  int the previous value
     */
    public function setTool_id($tool_id) {
      return $this->_change('tool_id', $tool_id);
    }

    /**
     * Retrieves maintainer_id
     *
     * @return  int
     */
    public function getMaintainer_id() {
      return $this->maintainer_id;
    }
      
    /**
     * Sets maintainer_id
     *
     * @param   int maintainer_id
     * @return  int the previous value
     */
    public function setMaintainer_id($maintainer_id) {
      return $this->_change('maintainer_id', $maintainer_id);
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
     * Retrieves lastrun
     *
     * @return  util.Date
     */
    public function getLastrun() {
      return $this->lastrun;
    }
      
    /**
     * Sets lastrun
     *
     * @param   util.Date lastrun
     * @return  util.Date the previous value
     */
    public function setLastrun($lastrun) {
      return $this->_change('lastrun', $lastrun);
    }

    /**
     * Retrieves nextrun
     *
     * @return  util.Date
     */
    public function getNextrun() {
      return $this->nextrun;
    }
      
    /**
     * Sets nextrun
     *
     * @param   util.Date nextrun
     * @return  util.Date the previous value
     */
    public function setNextrun($nextrun) {
      return $this->_change('nextrun', $nextrun);
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
     * referenced by person_id=>tool_id
     *
     * @return  de.schlund.db.methadon.Person entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getTool() {
      $r= XPClass::forName('de.schlund.db.methadon.Person')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('person_id', $this->getTool_id(), EQUAL)
      ));
      return $r ? $r[0] : NULL;
    }

    /**
     * Retrieves the Person entity
     * referenced by person_id=>maintainer_id
     *
     * @return  de.schlund.db.methadon.Person entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getMaintainer() {
      $r= XPClass::forName('de.schlund.db.methadon.Person')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('person_id', $this->getMaintainer_id(), EQUAL)
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
  }
?>