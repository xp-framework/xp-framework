<?php
/* This class is part of the XP framework
 *
 * $Id: Job.class.php 9512 2007-02-27 17:36:28Z friebe $
 */
 
  uses(
    'rdbms.join.JoinExtractable',
    'rdbms.DataSet'
  );
 
 
  /**
   * Class wrapper for table dapartment, database JOBS
   *
   * @purpose  Datasource accessor
   */
  class Department extends DataSet implements JoinExtractable {
    public
      $department_id      = 0,
      $name               = '',
      $chief_id           = 0;

    private
      $cache              = array(),
      $cached             = array();

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('JOBS.Department');
        $peer->setConnection('dapartment');
        $peer->setIdentity('department_id');
        $peer->setPrimary(array('department_id'));
        $peer->setTypes(array(
          'department_id' => array('%d', FieldType::NUMERIC, FALSE),
          'name'          => array('%s', FieldType::VARCHAR, FALSE),
          'chief_id'      => array('%d', FieldType::NUMERIC, FALSE),
        ));
        $peer->setConstraints(array(
          'DepartmentChief' => array(
            'classname' => 'net.xp_framework.unittest.rdbms.dataset.Person',
            'key'       => array(
              'chief_id' => 'person_id',
            ),
          ),
        ));
      }
    }  
  
    public function setCachedObj($role, $key, $obj) { $this->chache[$role][$key]= $obj; }
    public function getCachedObj($role, $key) { return $this->chache[$role][$key]; }
    public function hasCachedObj($role, $key) { return isset($this->chache[$role][$key]); }
    public function markAsCached($role) { $this->cached[$role]= TRUE; }

    /**
     * column factory
     *
     * @param   string name
     * @return  rdbms.Column
     * @throws  lang.IllegalArumentException
     */
    static public function column($name) {
      return self::getPeer()->column($name);
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
     * Gets an instance of this object by index "PRIMARY"
     *
     * @param   int department_id
     * @return  net.xp_framework.unittest.rdbms.dataset.Department object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByDepartment_id($department_id) {
      $r= self::getPeer()->doSelect(new Criteria(array('department_id', $department_id, EQUAL)));
      return $r ? $r[0] : NULL;
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
     * Retrieves chief_id
     *
     * @return  string
     */
    public function getChief_id() {
      return $this->chief_id;
    }
      
    /**
     * Sets chief_id
     *
     * @param   string chief_id
     * @return  string the previous value
     */
    public function setChief_id($chief_id) {
      return $this->_change('chief_id', $chief_id);
    }
  }
?>
