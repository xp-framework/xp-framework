<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet');

  /**
   * Class wrapper for table plane_department_structure, database METHADON
   * (Auto-generated on Wed, 04 Apr 2007 10:45:27 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class Plane_department_structure extends DataSet {

    protected
      $_isLoaded= false,
      $_loadCrit= NULL;

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('METHADON..plane_department_structure');
        $peer->setConnection('sybintern');
        $peer->setIdentity('plane_department_structure_id');
        $peer->setPrimary(array(''));
        $peer->setTypes(array(
          'recursion_depth'     => array('%d', FieldType::INT, FALSE),
          'plane_department_structure_id' => array('%d', FieldType::NUMERIC, FALSE),
          'master_department_id' => array('%d', FieldType::NUMERIC, FALSE),
          'department_id'       => array('%d', FieldType::NUMERIC, FALSE)
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
     * Retrieves recursion_depth
     *
     * @return  int
     */
    public function getRecursion_depth() {
      return $this->recursion_depth;
    }
      
    /**
     * Sets recursion_depth
     *
     * @param   int recursion_depth
     * @return  int the previous value
     */
    public function setRecursion_depth($recursion_depth) {
      return $this->_change('recursion_depth', $recursion_depth);
    }

    /**
     * Retrieves plane_department_structure_id
     *
     * @return  int
     */
    public function getPlane_department_structure_id() {
      return $this->plane_department_structure_id;
    }
      
    /**
     * Sets plane_department_structure_id
     *
     * @param   int plane_department_structure_id
     * @return  int the previous value
     */
    public function setPlane_department_structure_id($plane_department_structure_id) {
      return $this->_change('plane_department_structure_id', $plane_department_structure_id);
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
     * Retrieves the Person entity
     * referenced by person_id=>department_id
     *
     * @return  de.schlund.db.methadon.Person entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getDepartment() {
      $r= XPClass::forName('de.schlund.db.methadon.Person')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('person_id', $this->getDepartment_id(), EQUAL)
      ));
      return $r ? $r[0] : NULL;
    }

    /**
     * Retrieves the Person entity
     * referenced by person_id=>master_department_id
     *
     * @return  de.schlund.db.methadon.Person entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getMaster_department() {
      $r= XPClass::forName('de.schlund.db.methadon.Person')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('person_id', $this->getMaster_department_id(), EQUAL)
      ));
      return $r ? $r[0] : NULL;
    }
  }
?>