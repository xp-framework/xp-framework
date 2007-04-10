<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet');

  /**
   * Class wrapper for table pm_project, database METHADON
   * (Auto-generated on Wed, 04 Apr 2007 10:45:27 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class Pm_project extends DataSet {

    protected
      $_isLoaded= false,
      $_loadCrit= NULL;

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('METHADON..pm_project');
        $peer->setConnection('sybintern');
        $peer->setIdentity('pm_project_id');
        $peer->setPrimary(array('pm_project_id'));
        $peer->setTypes(array(
          'name'                => array('%s', FieldType::VARCHAR, FALSE),
          'description'         => array('%s', FieldType::VARCHAR, TRUE),
          'changedby'           => array('%s', FieldType::VARCHAR, FALSE),
          'done'                => array('%d', FieldType::SMALLINT, FALSE),
          'feature'             => array('%d', FieldType::INT, FALSE),
          'pm_project_id'       => array('%d', FieldType::NUMERIC, FALSE),
          'pm_id'               => array('%d', FieldType::NUMERIC, FALSE),
          'pmsub_id'            => array('%d', FieldType::NUMERIC, FALSE),
          'pmdes_id'            => array('%d', FieldType::NUMERIC, FALSE),
          'owner_id'            => array('%d', FieldType::NUMERIC, FALSE),
          'department_id'       => array('%d', FieldType::NUMERIC, FALSE),
          'bz_id'               => array('%d', FieldType::NUMERIC, FALSE),
          'ts_init'             => array('%s', FieldType::DATETIME, FALSE),
          'lastchange'          => array('%s', FieldType::DATETIME, FALSE),
          'ts_schedule'         => array('%s', FieldType::DATETIMN, TRUE),
          'ts_start'            => array('%s', FieldType::DATETIMN, TRUE),
          'ts_end'              => array('%s', FieldType::DATETIMN, TRUE)
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
     * Gets an instance of this object by index "U1_PM_PROJECT"
     * 
     * @param   int pm_id
     * @param   int pmsub_id
     * @param   int pmdes_id
     * @return  de.schlund.db.methadon.Pm_project entitiy object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByPm_idPmsub_idPmdes_id($pm_id, $pmsub_id, $pmdes_id) {
      return new self(array(
        'pm_id'  => $pm_id,
        'pmsub_id'  => $pmsub_id,
        'pmdes_id'  => $pmdes_id,
        '_loadCrit' => new Criteria(
          array('pm_id', $pm_id, EQUAL),
          array('pmsub_id', $pmsub_id, EQUAL),
          array('pmdes_id', $pmdes_id, EQUAL)
        )
      ));
    }

    /**
     * Gets an instance of this object by index "PK_PMPROJECT"
     * 
     * @param   int pm_project_id
     * @return  de.schlund.db.methadon.Pm_project entitiy object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByPm_project_id($pm_project_id) {
      return new self(array(
        'pm_project_id'  => $pm_project_id,
        '_loadCrit' => new Criteria(array('pm_project_id', $pm_project_id, EQUAL))
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
     * Retrieves done
     *
     * @return  int
     */
    public function getDone() {
      return $this->done;
    }
      
    /**
     * Sets done
     *
     * @param   int done
     * @return  int the previous value
     */
    public function setDone($done) {
      return $this->_change('done', $done);
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
     * Retrieves pm_project_id
     *
     * @return  int
     */
    public function getPm_project_id() {
      return $this->pm_project_id;
    }
      
    /**
     * Sets pm_project_id
     *
     * @param   int pm_project_id
     * @return  int the previous value
     */
    public function setPm_project_id($pm_project_id) {
      return $this->_change('pm_project_id', $pm_project_id);
    }

    /**
     * Retrieves pm_id
     *
     * @return  int
     */
    public function getPm_id() {
      return $this->pm_id;
    }
      
    /**
     * Sets pm_id
     *
     * @param   int pm_id
     * @return  int the previous value
     */
    public function setPm_id($pm_id) {
      return $this->_change('pm_id', $pm_id);
    }

    /**
     * Retrieves pmsub_id
     *
     * @return  int
     */
    public function getPmsub_id() {
      return $this->pmsub_id;
    }
      
    /**
     * Sets pmsub_id
     *
     * @param   int pmsub_id
     * @return  int the previous value
     */
    public function setPmsub_id($pmsub_id) {
      return $this->_change('pmsub_id', $pmsub_id);
    }

    /**
     * Retrieves pmdes_id
     *
     * @return  int
     */
    public function getPmdes_id() {
      return $this->pmdes_id;
    }
      
    /**
     * Sets pmdes_id
     *
     * @param   int pmdes_id
     * @return  int the previous value
     */
    public function setPmdes_id($pmdes_id) {
      return $this->_change('pmdes_id', $pmdes_id);
    }

    /**
     * Retrieves owner_id
     *
     * @return  int
     */
    public function getOwner_id() {
      return $this->owner_id;
    }
      
    /**
     * Sets owner_id
     *
     * @param   int owner_id
     * @return  int the previous value
     */
    public function setOwner_id($owner_id) {
      return $this->_change('owner_id', $owner_id);
    }

    /**
     * Retrieves department_id
     *
     * @return  int
     */
    public function getDepartment_id() {
      return $this->department_id;
    }
      
    /**
     * Sets department_id
     *
     * @param   int department_id
     * @return  int the previous value
     */
    public function setDepartment_id($department_id) {
      return $this->_change('department_id', $department_id);
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
     * Retrieves ts_schedule
     *
     * @return  util.Date
     */
    public function getTs_schedule() {
      return $this->ts_schedule;
    }
      
    /**
     * Sets ts_schedule
     *
     * @param   util.Date ts_schedule
     * @return  util.Date the previous value
     */
    public function setTs_schedule($ts_schedule) {
      return $this->_change('ts_schedule', $ts_schedule);
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
     * Retrieves the Employee entity
     * referenced by employee_id=>owner_id
     *
     * @return  de.schlund.db.methadon.Employee entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getOwner() {
      $r= XPClass::forName('de.schlund.db.methadon.Employee')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('employee_id', $this->getOwner_id(), EQUAL)
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
     * Retrieves the Department entity
     * referenced by department_id=>department_id
     *
     * @return  de.schlund.db.methadon.Department entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getDepartment() {
      $r= XPClass::forName('de.schlund.db.methadon.Department')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('department_id', $this->getDepartment_id(), EQUAL)
      ));
      return $r ? $r[0] : NULL;
    }

    /**
     * Retrieves an array of all Pm_projectcomment entities referencing
     * this entity by pm_project_id=>pm_project_id
     *
     * @return  de.schlund.db.methadon.Pm_projectcomment[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getPm_projectcommentPm_projectList() {
      return XPClass::forName('de.schlund.db.methadon.Pm_projectcomment')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('pm_project_id', $this->getPm_project_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Pm_projectcomment entities referencing
     * this entity by pm_project_id=>pm_project_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Pm_projectcomment>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getPm_projectcommentPm_projectIterator() {
      return XPClass::forName('de.schlund.db.methadon.Pm_projectcomment')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('pm_project_id', $this->getPm_project_id(), EQUAL)
      ));
    }
  }
?>