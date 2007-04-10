<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet');

  /**
   * Class wrapper for table employee, database METHADON
   * (Auto-generated on Wed, 04 Apr 2007 10:45:27 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class Employee extends DataSet {

    protected
      $_isLoaded= false,
      $_loadCrit= NULL;

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('METHADON..employee');
        $peer->setConnection('sybintern');
        $peer->setPrimary(array('employee_id'));
        $peer->setTypes(array(
          'personnel_id'        => array('%s', FieldType::VARCHAR, FALSE),
          'privat_street'       => array('%s', FieldType::VARCHAR, TRUE),
          'privat_city'         => array('%s', FieldType::VARCHAR, TRUE),
          'privat_zip'          => array('%s', FieldType::VARCHAR, TRUE),
          'privat_phone'        => array('%s', FieldType::VARCHAR, TRUE),
          'privat_fax'          => array('%s', FieldType::VARCHAR, TRUE),
          'functions'           => array('%s', FieldType::VARCHAR, TRUE),
          'private_specification' => array('%s', FieldType::VARCHAR, TRUE),
          'comment'             => array('%s', FieldType::VARCHAR, TRUE),
          'changedby'           => array('%s', FieldType::VARCHAR, FALSE),
          'cost_center'         => array('%s', FieldType::VARCHAR, TRUE),
          'employee_id'         => array('%d', FieldType::NUMERIC, FALSE),
          'department_id'       => array('%d', FieldType::NUMERIC, FALSE),
          'company_id'          => array('%d', FieldType::NUMERIC, FALSE),
          'location_id'         => array('%d', FieldType::NUMERIC, FALSE),
          'bz_id'               => array('%d', FieldType::NUMERIC, FALSE),
          'lastchange'          => array('%s', FieldType::DATETIME, FALSE),
          'birthday'            => array('%s', FieldType::DATETIMN, TRUE),
          'adjustment_date'     => array('%s', FieldType::SMALLDATETIME, FALSE),
          'deputy_person_id'    => array('%d', FieldType::NUMERICN, TRUE)
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
     * Gets an instance of this object by index "PK_STAFF"
     * 
     * @param   int employee_id
     * @return  de.schlund.db.methadon.Employee entitiy object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByEmployee_id($employee_id) {
      return new self(array(
        'employee_id'  => $employee_id,
        '_loadCrit' => new Criteria(array('employee_id', $employee_id, EQUAL))
      ));
    }

    /**
     * Gets an instance of this object by index "EMPLOYEE_DEPARTMENT_I"
     * 
     * @param   int department_id
     * @return  de.schlund.db.methadon.Employee[] entity objects
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByDepartment_id($department_id) {
      $r= self::getPeer()->doSelect(new Criteria(array('department_id', $department_id, EQUAL)));
      foreach ($r as $e) $e->_isLoaded= true;
      return $r;
    }

    /**
     * Retrieves personnel_id
     *
     * @return  string
     */
    public function getPersonnel_id() {
      return $this->personnel_id;
    }
      
    /**
     * Sets personnel_id
     *
     * @param   string personnel_id
     * @return  string the previous value
     */
    public function setPersonnel_id($personnel_id) {
      return $this->_change('personnel_id', $personnel_id);
    }

    /**
     * Retrieves privat_street
     *
     * @return  string
     */
    public function getPrivat_street() {
      return $this->privat_street;
    }
      
    /**
     * Sets privat_street
     *
     * @param   string privat_street
     * @return  string the previous value
     */
    public function setPrivat_street($privat_street) {
      return $this->_change('privat_street', $privat_street);
    }

    /**
     * Retrieves privat_city
     *
     * @return  string
     */
    public function getPrivat_city() {
      return $this->privat_city;
    }
      
    /**
     * Sets privat_city
     *
     * @param   string privat_city
     * @return  string the previous value
     */
    public function setPrivat_city($privat_city) {
      return $this->_change('privat_city', $privat_city);
    }

    /**
     * Retrieves privat_zip
     *
     * @return  string
     */
    public function getPrivat_zip() {
      return $this->privat_zip;
    }
      
    /**
     * Sets privat_zip
     *
     * @param   string privat_zip
     * @return  string the previous value
     */
    public function setPrivat_zip($privat_zip) {
      return $this->_change('privat_zip', $privat_zip);
    }

    /**
     * Retrieves privat_phone
     *
     * @return  string
     */
    public function getPrivat_phone() {
      return $this->privat_phone;
    }
      
    /**
     * Sets privat_phone
     *
     * @param   string privat_phone
     * @return  string the previous value
     */
    public function setPrivat_phone($privat_phone) {
      return $this->_change('privat_phone', $privat_phone);
    }

    /**
     * Retrieves privat_fax
     *
     * @return  string
     */
    public function getPrivat_fax() {
      return $this->privat_fax;
    }
      
    /**
     * Sets privat_fax
     *
     * @param   string privat_fax
     * @return  string the previous value
     */
    public function setPrivat_fax($privat_fax) {
      return $this->_change('privat_fax', $privat_fax);
    }

    /**
     * Retrieves functions
     *
     * @return  string
     */
    public function getFunctions() {
      return $this->functions;
    }
      
    /**
     * Sets functions
     *
     * @param   string functions
     * @return  string the previous value
     */
    public function setFunctions($functions) {
      return $this->_change('functions', $functions);
    }

    /**
     * Retrieves private_specification
     *
     * @return  string
     */
    public function getPrivate_specification() {
      return $this->private_specification;
    }
      
    /**
     * Sets private_specification
     *
     * @param   string private_specification
     * @return  string the previous value
     */
    public function setPrivate_specification($private_specification) {
      return $this->_change('private_specification', $private_specification);
    }

    /**
     * Retrieves comment
     *
     * @return  string
     */
    public function getComment() {
      return $this->comment;
    }
      
    /**
     * Sets comment
     *
     * @param   string comment
     * @return  string the previous value
     */
    public function setComment($comment) {
      return $this->_change('comment', $comment);
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
     * Retrieves cost_center
     *
     * @return  string
     */
    public function getCost_center() {
      return $this->cost_center;
    }
      
    /**
     * Sets cost_center
     *
     * @param   string cost_center
     * @return  string the previous value
     */
    public function setCost_center($cost_center) {
      return $this->_change('cost_center', $cost_center);
    }

    /**
     * Retrieves employee_id
     *
     * @return  int
     */
    public function getEmployee_id() {
      return $this->employee_id;
    }
      
    /**
     * Sets employee_id
     *
     * @param   int employee_id
     * @return  int the previous value
     */
    public function setEmployee_id($employee_id) {
      return $this->_change('employee_id', $employee_id);
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
     * Retrieves company_id
     *
     * @return  int
     */
    public function getCompany_id() {
      return $this->company_id;
    }
      
    /**
     * Sets company_id
     *
     * @param   int company_id
     * @return  int the previous value
     */
    public function setCompany_id($company_id) {
      return $this->_change('company_id', $company_id);
    }

    /**
     * Retrieves location_id
     *
     * @return  int
     */
    public function getLocation_id() {
      return $this->location_id;
    }
      
    /**
     * Sets location_id
     *
     * @param   int location_id
     * @return  int the previous value
     */
    public function setLocation_id($location_id) {
      return $this->_change('location_id', $location_id);
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
     * Retrieves birthday
     *
     * @return  util.Date
     */
    public function getBirthday() {
      return $this->birthday;
    }
      
    /**
     * Sets birthday
     *
     * @param   util.Date birthday
     * @return  util.Date the previous value
     */
    public function setBirthday($birthday) {
      return $this->_change('birthday', $birthday);
    }

    /**
     * Retrieves adjustment_date
     *
     * @return  util.Date
     */
    public function getAdjustment_date() {
      return $this->adjustment_date;
    }
      
    /**
     * Sets adjustment_date
     *
     * @param   util.Date adjustment_date
     * @return  util.Date the previous value
     */
    public function setAdjustment_date($adjustment_date) {
      return $this->_change('adjustment_date', $adjustment_date);
    }

    /**
     * Retrieves deputy_person_id
     *
     * @return  int
     */
    public function getDeputy_person_id() {
      return $this->deputy_person_id;
    }
      
    /**
     * Sets deputy_person_id
     *
     * @param   int deputy_person_id
     * @return  int the previous value
     */
    public function setDeputy_person_id($deputy_person_id) {
      return $this->_change('deputy_person_id', $deputy_person_id);
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
     * referenced by person_id=>employee_id
     *
     * @return  de.schlund.db.methadon.Person entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEmployee() {
      $r= XPClass::forName('de.schlund.db.methadon.Person')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('person_id', $this->getEmployee_id(), EQUAL)
      ));
      return $r ? $r[0] : NULL;
    }

    /**
     * Retrieves the Company entity
     * referenced by company_id=>company_id
     *
     * @return  de.schlund.db.methadon.Company entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getCompany() {
      $r= XPClass::forName('de.schlund.db.methadon.Company')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('company_id', $this->getCompany_id(), EQUAL)
      ));
      return $r ? $r[0] : NULL;
    }

    /**
     * Retrieves the Location entity
     * referenced by location_id=>location_id
     *
     * @return  de.schlund.db.methadon.Location entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getLocation() {
      $r= XPClass::forName('de.schlund.db.methadon.Location')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('location_id', $this->getLocation_id(), EQUAL)
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
     * Retrieves an array of all Pm_project entities referencing
     * this entity by owner_id=>employee_id
     *
     * @return  de.schlund.db.methadon.Pm_project[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getPm_projectOwnerList() {
      return XPClass::forName('de.schlund.db.methadon.Pm_project')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('owner_id', $this->getEmployee_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Pm_project entities referencing
     * this entity by owner_id=>employee_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Pm_project>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getPm_projectOwnerIterator() {
      return XPClass::forName('de.schlund.db.methadon.Pm_project')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('owner_id', $this->getEmployee_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Pm_projectcomment entities referencing
     * this entity by owner_id=>employee_id
     *
     * @return  de.schlund.db.methadon.Pm_projectcomment[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getPm_projectcommentOwnerList() {
      return XPClass::forName('de.schlund.db.methadon.Pm_projectcomment')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('owner_id', $this->getEmployee_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Pm_projectcomment entities referencing
     * this entity by owner_id=>employee_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Pm_projectcomment>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getPm_projectcommentOwnerIterator() {
      return XPClass::forName('de.schlund.db.methadon.Pm_projectcomment')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('owner_id', $this->getEmployee_id(), EQUAL)
      ));
    }
  }
?>