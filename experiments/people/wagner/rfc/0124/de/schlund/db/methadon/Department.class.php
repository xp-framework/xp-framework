<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet');

  /**
   * Class wrapper for table department, database METHADON
   * (Auto-generated on Wed, 04 Apr 2007 10:45:27 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class Department extends DataSet {

    protected
      $_isLoaded= false,
      $_loadCrit= NULL;

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('METHADON..department');
        $peer->setConnection('sybintern');
        $peer->setPrimary(array('department_id'));
        $peer->setTypes(array(
          'name'                => array('%s', FieldType::VARCHAR, FALSE),
          'description'         => array('%s', FieldType::VARCHAR, TRUE),
          'changedby'           => array('%s', FieldType::VARCHAR, FALSE),
          'internal_name'       => array('%s', FieldType::VARCHAR, TRUE),
          'department_id'       => array('%d', FieldType::NUMERIC, FALSE),
          'department_type_id'  => array('%d', FieldType::NUMERIC, FALSE),
          'head_person_id'      => array('%d', FieldType::NUMERIC, FALSE),
          'bz_id'               => array('%d', FieldType::NUMERIC, FALSE),
          'lastchange'          => array('%s', FieldType::DATETIME, FALSE),
          'master_department_id' => array('%d', FieldType::NUMERICN, TRUE)
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
     * Gets an instance of this object by index "PK_DEPARTMENT"
     * 
     * @param   int department_id
     * @return  de.schlund.db.methadon.Department entitiy object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByDepartment_id($department_id) {
      return new self(array(
        'department_id'  => $department_id,
        '_loadCrit' => new Criteria(array('department_id', $department_id, EQUAL))
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
     * Retrieves internal_name
     *
     * @return  string
     */
    public function getInternal_name() {
      return $this->internal_name;
    }
      
    /**
     * Sets internal_name
     *
     * @param   string internal_name
     * @return  string the previous value
     */
    public function setInternal_name($internal_name) {
      return $this->_change('internal_name', $internal_name);
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
     * Retrieves department_type_id
     *
     * @return  int
     */
    public function getDepartment_type_id() {
      return $this->department_type_id;
    }
      
    /**
     * Sets department_type_id
     *
     * @param   int department_type_id
     * @return  int the previous value
     */
    public function setDepartment_type_id($department_type_id) {
      return $this->_change('department_type_id', $department_type_id);
    }

    /**
     * Retrieves head_person_id
     *
     * @return  int
     */
    public function getHead_person_id() {
      return $this->head_person_id;
    }
      
    /**
     * Sets head_person_id
     *
     * @param   int head_person_id
     * @return  int the previous value
     */
    public function setHead_person_id($head_person_id) {
      return $this->_change('head_person_id', $head_person_id);
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
     * Retrieves master_department_id
     *
     * @return  int
     */
    public function getMaster_department_id() {
      return $this->master_department_id;
    }
      
    /**
     * Sets master_department_id
     *
     * @param   int master_department_id
     * @return  int the previous value
     */
    public function setMaster_department_id($master_department_id) {
      return $this->_change('master_department_id', $master_department_id);
    }

    /**
     * Retrieves the Person entity
     * referenced by person_id=>head_person_id
     *
     * @return  de.schlund.db.methadon.Person entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getHead_person() {
      $r= XPClass::forName('de.schlund.db.methadon.Person')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('person_id', $this->getHead_person_id(), EQUAL)
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
     * Retrieves the Department_type entity
     * referenced by department_type_id=>department_type_id
     *
     * @return  de.schlund.db.methadon.Department_type entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getDepartment_type() {
      $r= XPClass::forName('de.schlund.db.methadon.Department_type')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('department_type_id', $this->getDepartment_type_id(), EQUAL)
      ));
      return $r ? $r[0] : NULL;
    }

    /**
     * Retrieves an array of all Employee entities referencing
     * this entity by department_id=>department_id
     *
     * @return  de.schlund.db.methadon.Employee[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEmployeeDepartmentList() {
      return XPClass::forName('de.schlund.db.methadon.Employee')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('department_id', $this->getDepartment_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Employee entities referencing
     * this entity by department_id=>department_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Employee>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEmployeeDepartmentIterator() {
      return XPClass::forName('de.schlund.db.methadon.Employee')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('department_id', $this->getDepartment_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Vacation_delegation entities referencing
     * this entity by department_id=>department_id
     *
     * @return  de.schlund.db.methadon.Vacation_delegation[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getVacation_delegationDepartmentList() {
      return XPClass::forName('de.schlund.db.methadon.Vacation_delegation')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('department_id', $this->getDepartment_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Vacation_delegation entities referencing
     * this entity by department_id=>department_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Vacation_delegation>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getVacation_delegationDepartmentIterator() {
      return XPClass::forName('de.schlund.db.methadon.Vacation_delegation')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('department_id', $this->getDepartment_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Pm_project entities referencing
     * this entity by department_id=>department_id
     *
     * @return  de.schlund.db.methadon.Pm_project[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getPm_projectDepartmentList() {
      return XPClass::forName('de.schlund.db.methadon.Pm_project')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('department_id', $this->getDepartment_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Pm_project entities referencing
     * this entity by department_id=>department_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Pm_project>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getPm_projectDepartmentIterator() {
      return XPClass::forName('de.schlund.db.methadon.Pm_project')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('department_id', $this->getDepartment_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Plane_master_matrix entities referencing
     * this entity by master_department_id=>department_id
     *
     * @return  de.schlund.db.methadon.Plane_master_matrix[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getPlane_master_matrixMaster_departmentList() {
      return XPClass::forName('de.schlund.db.methadon.Plane_master_matrix')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('master_department_id', $this->getDepartment_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Plane_master_matrix entities referencing
     * this entity by master_department_id=>department_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Plane_master_matrix>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getPlane_master_matrixMaster_departmentIterator() {
      return XPClass::forName('de.schlund.db.methadon.Plane_master_matrix')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('master_department_id', $this->getDepartment_id(), EQUAL)
      ));
    }
  }
?>