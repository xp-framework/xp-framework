<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet');

  /**
   * Class wrapper for table company, database METHADON
   * (Auto-generated on Wed, 04 Apr 2007 10:45:27 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class Company extends DataSet {

    protected
      $_isLoaded= false,
      $_loadCrit= NULL;

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('METHADON..company');
        $peer->setConnection('sybintern');
        $peer->setPrimary(array('company_id'));
        $peer->setTypes(array(
          'name'                => array('%s', FieldType::VARCHAR, FALSE),
          'description'         => array('%s', FieldType::VARCHAR, TRUE),
          'company_id'          => array('%d', FieldType::NUMERIC, FALSE),
          'feature'             => array('%d', FieldType::INTN, TRUE)
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
     * Gets an instance of this object by index "PK_COMBINE"
     * 
     * @param   int company_id
     * @return  de.schlund.db.methadon.Company entitiy object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByCompany_id($company_id) {
      return new self(array(
        'company_id'  => $company_id,
        '_loadCrit' => new Criteria(array('company_id', $company_id, EQUAL))
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
     * Retrieves the Person entity
     * referenced by person_id=>company_id
     *
     * @return  de.schlund.db.methadon.Person entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getCompany() {
      $r= XPClass::forName('de.schlund.db.methadon.Person')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('person_id', $this->getCompany_id(), EQUAL)
      ));
      return $r ? $r[0] : NULL;
    }

    /**
     * Retrieves an array of all Employee entities referencing
     * this entity by company_id=>company_id
     *
     * @return  de.schlund.db.methadon.Employee[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEmployeeCompanyList() {
      return XPClass::forName('de.schlund.db.methadon.Employee')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('company_id', $this->getCompany_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Employee entities referencing
     * this entity by company_id=>company_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Employee>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEmployeeCompanyIterator() {
      return XPClass::forName('de.schlund.db.methadon.Employee')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('company_id', $this->getCompany_id(), EQUAL)
      ));
    }
  }
?>