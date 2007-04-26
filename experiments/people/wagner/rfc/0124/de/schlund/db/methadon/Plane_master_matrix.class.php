<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet');

  /**
   * Class wrapper for table plane_master_matrix, database METHADON
   * (Auto-generated on Wed, 04 Apr 2007 10:45:27 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class Plane_master_matrix extends DataSet {

    protected
      $_isLoaded= false,
      $_loadCrit= NULL;

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('METHADON..plane_master_matrix');
        $peer->setConnection('sybintern');
        $peer->setIdentity('plane_master_matrix_id');
        $peer->setPrimary(array('plane_master_matrix_id'));
        $peer->setTypes(array(
          'recursion_depth'     => array('%d', FieldType::INT, FALSE),
          'plane_master_matrix_id' => array('%d', FieldType::NUMERIC, FALSE),
          'master_id'           => array('%d', FieldType::NUMERIC, FALSE),
          'master_department_id' => array('%d', FieldType::NUMERIC, FALSE),
          'slave_id'            => array('%d', FieldType::NUMERIC, FALSE),
          'bz_id'               => array('%d', FieldType::NUMERICN, TRUE)
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
     * Gets an instance of this object by index "PK_PMM"
     * 
     * @param   int plane_master_matrix_id
     * @return  de.schlund.db.methadon.Plane_master_matrix entitiy object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByPlane_master_matrix_id($plane_master_matrix_id) {
      return new self(array(
        'plane_master_matrix_id'  => $plane_master_matrix_id,
        '_loadCrit' => new Criteria(array('plane_master_matrix_id', $plane_master_matrix_id, EQUAL))
      ));
    }

    /**
     * Gets an instance of this object by index "plane_master_matrix_i1"
     * 
     * @param   int master_id
     * @return  de.schlund.db.methadon.Plane_master_matrix[] entity objects
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByMaster_id($master_id) {
      $r= self::getPeer()->doSelect(new Criteria(array('master_id', $master_id, EQUAL)));
      foreach ($r as $e) $e->_isLoaded= true;
      return $r;
    }

    /**
     * Gets an instance of this object by index "plane_master_matrix_i2"
     * 
     * @param   int slave_id
     * @return  de.schlund.db.methadon.Plane_master_matrix[] entity objects
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getBySlave_id($slave_id) {
      $r= self::getPeer()->doSelect(new Criteria(array('slave_id', $slave_id, EQUAL)));
      foreach ($r as $e) $e->_isLoaded= true;
      return $r;
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
     * Retrieves plane_master_matrix_id
     *
     * @return  int
     */
    public function getPlane_master_matrix_id() {
      return $this->plane_master_matrix_id;
    }
      
    /**
     * Sets plane_master_matrix_id
     *
     * @param   int plane_master_matrix_id
     * @return  int the previous value
     */
    public function setPlane_master_matrix_id($plane_master_matrix_id) {
      return $this->_change('plane_master_matrix_id', $plane_master_matrix_id);
    }

    /**
     * Retrieves master_id
     *
     * @return  int
     */
    public function getMaster_id() {
      return $this->master_id;
    }
      
    /**
     * Sets master_id
     *
     * @param   int master_id
     * @return  int the previous value
     */
    public function setMaster_id($master_id) {
      return $this->_change('master_id', $master_id);
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
     * Retrieves slave_id
     *
     * @return  int
     */
    public function getSlave_id() {
      return $this->slave_id;
    }
      
    /**
     * Sets slave_id
     *
     * @param   int slave_id
     * @return  int the previous value
     */
    public function setSlave_id($slave_id) {
      return $this->_change('slave_id', $slave_id);
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
     * Retrieves the Person entity
     * referenced by person_id=>slave_id
     *
     * @return  de.schlund.db.methadon.Person entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getSlave() {
      $r= XPClass::forName('de.schlund.db.methadon.Person')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('person_id', $this->getSlave_id(), EQUAL)
      ));
      return $r ? $r[0] : NULL;
    }

    /**
     * Retrieves the Person entity
     * referenced by person_id=>master_id
     *
     * @return  de.schlund.db.methadon.Person entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getMaster() {
      $r= XPClass::forName('de.schlund.db.methadon.Person')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('person_id', $this->getMaster_id(), EQUAL)
      ));
      return $r ? $r[0] : NULL;
    }

    /**
     * Retrieves the Department entity
     * referenced by department_id=>master_department_id
     *
     * @return  de.schlund.db.methadon.Department entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getMaster_department() {
      $r= XPClass::forName('de.schlund.db.methadon.Department')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('department_id', $this->getMaster_department_id(), EQUAL)
      ));
      return $r ? $r[0] : NULL;
    }
  }
?>