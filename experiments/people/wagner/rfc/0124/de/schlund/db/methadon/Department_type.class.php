<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet');

  /**
   * Class wrapper for table department_type, database METHADON
   * (Auto-generated on Wed, 04 Apr 2007 10:45:27 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class Department_type extends DataSet {

    protected
      $_isLoaded= false,
      $_loadCrit= NULL;

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('METHADON..department_type');
        $peer->setConnection('sybintern');
        $peer->setPrimary(array('department_type_id'));
        $peer->setTypes(array(
          'name'                => array('%s', FieldType::VARCHAR, FALSE),
          'description'         => array('%s', FieldType::VARCHAR, TRUE),
          'department_type_id'  => array('%d', FieldType::NUMERIC, FALSE)
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
     * Gets an instance of this object by index "PK_DEPARTMENT_TYPE"
     * 
     * @param   int department_type_id
     * @return  de.schlund.db.methadon.Department_type entitiy object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByDepartment_type_id($department_type_id) {
      return new self(array(
        'department_type_id'  => $department_type_id,
        '_loadCrit' => new Criteria(array('department_type_id', $department_type_id, EQUAL))
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
     * Retrieves an array of all Department entities referencing
     * this entity by department_type_id=>department_type_id
     *
     * @return  de.schlund.db.methadon.Department[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getDepartmentDepartment_typeList() {
      return XPClass::forName('de.schlund.db.methadon.Department')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('department_type_id', $this->getDepartment_type_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Department entities referencing
     * this entity by department_type_id=>department_type_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Department>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getDepartmentDepartment_typeIterator() {
      return XPClass::forName('de.schlund.db.methadon.Department')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('department_type_id', $this->getDepartment_type_id(), EQUAL)
      ));
    }
  }
?>