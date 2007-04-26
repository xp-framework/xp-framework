<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet');

  /**
   * Class wrapper for table pm_projectcomment, database METHADON
   * (Auto-generated on Wed, 04 Apr 2007 10:45:27 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class Pm_projectcomment extends DataSet {

    protected
      $_isLoaded= false,
      $_loadCrit= NULL;

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('METHADON..pm_projectcomment');
        $peer->setConnection('sybintern');
        $peer->setIdentity('pm_projectcomment_id');
        $peer->setPrimary(array('pm_projectcomment_id'));
        $peer->setTypes(array(
          'name'                => array('%s', FieldType::VARCHAR, FALSE),
          'changedby'           => array('%s', FieldType::VARCHAR, FALSE),
          'pm_projectcomment_id' => array('%d', FieldType::NUMERIC, FALSE),
          'pm_project_id'       => array('%d', FieldType::NUMERIC, FALSE),
          'owner_id'            => array('%d', FieldType::NUMERIC, FALSE),
          'lastchange'          => array('%s', FieldType::DATETIME, FALSE),
          'continuous_text'     => array('%s', FieldType::TEXT, TRUE)
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
     * Gets an instance of this object by index "I1_PM_PROJECTCOMM"
     * 
     * @param   int pm_project_id
     * @return  de.schlund.db.methadon.Pm_projectcomment[] entity objects
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByPm_project_id($pm_project_id) {
      $r= self::getPeer()->doSelect(new Criteria(array('pm_project_id', $pm_project_id, EQUAL)));
      foreach ($r as $e) $e->_isLoaded= true;
      return $r;
    }

    /**
     * Gets an instance of this object by index "PK_PMPROJECTOMMENT"
     * 
     * @param   int pm_projectcomment_id
     * @return  de.schlund.db.methadon.Pm_projectcomment entitiy object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByPm_projectcomment_id($pm_projectcomment_id) {
      return new self(array(
        'pm_projectcomment_id'  => $pm_projectcomment_id,
        '_loadCrit' => new Criteria(array('pm_projectcomment_id', $pm_projectcomment_id, EQUAL))
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
     * Retrieves pm_projectcomment_id
     *
     * @return  int
     */
    public function getPm_projectcomment_id() {
      return $this->pm_projectcomment_id;
    }
      
    /**
     * Sets pm_projectcomment_id
     *
     * @param   int pm_projectcomment_id
     * @return  int the previous value
     */
    public function setPm_projectcomment_id($pm_projectcomment_id) {
      return $this->_change('pm_projectcomment_id', $pm_projectcomment_id);
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
     * Retrieves continuous_text
     *
     * @return  string
     */
    public function getContinuous_text() {
      return $this->continuous_text;
    }
      
    /**
     * Sets continuous_text
     *
     * @param   string continuous_text
     * @return  string the previous value
     */
    public function setContinuous_text($continuous_text) {
      return $this->_change('continuous_text', $continuous_text);
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
     * Retrieves the Pm_project entity
     * referenced by pm_project_id=>pm_project_id
     *
     * @return  de.schlund.db.methadon.Pm_project entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getPm_project() {
      $r= XPClass::forName('de.schlund.db.methadon.Pm_project')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('pm_project_id', $this->getPm_project_id(), EQUAL)
      ));
      return $r ? $r[0] : NULL;
    }
  }
?>