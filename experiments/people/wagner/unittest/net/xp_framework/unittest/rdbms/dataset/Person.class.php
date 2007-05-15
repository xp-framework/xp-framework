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
   * Class wrapper for table person, database JOBS
   *
   * @purpose  Datasource accessor
   */
  class Person extends DataSet implements JoinExtractable {
    public
      $person_id          = 0,
      $name               = '',
      $job_id             = 0,
      $department_id      = 0;

    private
      $cache              = array(),
      $cached             = array();

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('JOBS.Person');
        $peer->setConnection('person');
        $peer->setIdentity('person_id');
        $peer->setPrimary(array('person_id'));
        $peer->setTypes(array(
          'person_id'     => array('%d', FieldType::NUMERIC, FALSE),
          'name'          => array('%s', FieldType::VARCHAR, FALSE),
          'job_id'        => array('%d', FieldType::NUMERIC, FALSE),
          'department_id' => array('%d', FieldType::NUMERIC, FALSE),
        ));
        $peer->setConstraints(array(
          'Job' => array(
            'classname' => 'net.xp_framework.unittest.rdbms.dataset.Job',
            'key'       => array(
              'job_id' => 'job_id',
            ),
          ),
          'Department' => array(
            'classname' => 'net.xp_framework.unittest.rdbms.dataset.Department',
            'key'       => array(
              'department_id' => 'department_id',
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
     * @param   int person_id
     * @return  net.xp_framework.unittest.rdbms.dataset.Person object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByPerson_id($person_id) {
      $r= self::getPeer()->doSelect(new Criteria(array('person_id', $person_id, EQUAL)));
      return $r ? $r[0] : NULL;
    }

    /**
     * Retrieves person_id
     *
     * @return  int
     */
    public function getPerson_id() {
      return $this->person_id;
    }
      
    /**
     * Sets person_id
     *
     * @param   int person_id
     * @return  int the previous value
     */
    public function setPerson_id($person_id) {
      return $this->_change('person_id', $person_id);
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
     * Retrieves job_id
     *
     * @return  int
     */
    public function getJob_id() {
      return $this->job_id;
    }
      
    /**
     * Sets job_id
     *
     * @param   int job_id
     * @return  int the previous value
     */
    public function setJob_id($job_id) {
      return $this->_change('job_id', $job_id);
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
  }
?>
