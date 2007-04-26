<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet');

  /**
   * Class wrapper for table project, database METHADON
   * (Auto-generated on Wed, 04 Apr 2007 10:45:27 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class Project extends DataSet {

    protected
      $_isLoaded= false,
      $_loadCrit= NULL;

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('METHADON..project');
        $peer->setConnection('sybintern');
        $peer->setPrimary(array('project_id'));
        $peer->setTypes(array(
          'name'                => array('%s', FieldType::VARCHAR, FALSE),
          'description'         => array('%s', FieldType::VARCHAR, TRUE),
          'sess_prefix'         => array('%s', FieldType::VARCHAR, FALSE),
          'default_url'         => array('%s', FieldType::VARCHAR, TRUE),
          'stage_url'           => array('%s', FieldType::VARCHAR, TRUE),
          'changedby'           => array('%s', FieldType::VARCHAR, FALSE),
          'project_id'          => array('%d', FieldType::NUMERIC, FALSE),
          'maintainer_id'       => array('%d', FieldType::NUMERIC, FALSE),
          'bz_id'               => array('%d', FieldType::NUMERIC, FALSE),
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
     * Gets an instance of this object by index "PK_PROJECT"
     * 
     * @param   int project_id
     * @return  de.schlund.db.methadon.Project entitiy object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByProject_id($project_id) {
      return new self(array(
        'project_id'  => $project_id,
        '_loadCrit' => new Criteria(array('project_id', $project_id, EQUAL))
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
     * Retrieves sess_prefix
     *
     * @return  string
     */
    public function getSess_prefix() {
      return $this->sess_prefix;
    }
      
    /**
     * Sets sess_prefix
     *
     * @param   string sess_prefix
     * @return  string the previous value
     */
    public function setSess_prefix($sess_prefix) {
      return $this->_change('sess_prefix', $sess_prefix);
    }

    /**
     * Retrieves default_url
     *
     * @return  string
     */
    public function getDefault_url() {
      return $this->default_url;
    }
      
    /**
     * Sets default_url
     *
     * @param   string default_url
     * @return  string the previous value
     */
    public function setDefault_url($default_url) {
      return $this->_change('default_url', $default_url);
    }

    /**
     * Retrieves stage_url
     *
     * @return  string
     */
    public function getStage_url() {
      return $this->stage_url;
    }
      
    /**
     * Sets stage_url
     *
     * @param   string stage_url
     * @return  string the previous value
     */
    public function setStage_url($stage_url) {
      return $this->_change('stage_url', $stage_url);
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
     * Retrieves project_id
     *
     * @return  int
     */
    public function getProject_id() {
      return $this->project_id;
    }
      
    /**
     * Sets project_id
     *
     * @param   int project_id
     * @return  int the previous value
     */
    public function setProject_id($project_id) {
      return $this->_change('project_id', $project_id);
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
     * Retrieves an array of all Textpart_matrix entities referencing
     * this entity by project_id=>project_id
     *
     * @return  de.schlund.db.methadon.Textpart_matrix[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getTextpart_matrixProjectList() {
      return XPClass::forName('de.schlund.db.methadon.Textpart_matrix')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('project_id', $this->getProject_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Textpart_matrix entities referencing
     * this entity by project_id=>project_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Textpart_matrix>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getTextpart_matrixProjectIterator() {
      return XPClass::forName('de.schlund.db.methadon.Textpart_matrix')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('project_id', $this->getProject_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Project_right_type_matrix entities referencing
     * this entity by project_id=>project_id
     *
     * @return  de.schlund.db.methadon.Project_right_type_matrix[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getProject_right_type_matrixProjectList() {
      return XPClass::forName('de.schlund.db.methadon.Project_right_type_matrix')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('project_id', $this->getProject_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Project_right_type_matrix entities referencing
     * this entity by project_id=>project_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Project_right_type_matrix>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getProject_right_type_matrixProjectIterator() {
      return XPClass::forName('de.schlund.db.methadon.Project_right_type_matrix')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('project_id', $this->getProject_id(), EQUAL)
      ));
    }
  }
?>