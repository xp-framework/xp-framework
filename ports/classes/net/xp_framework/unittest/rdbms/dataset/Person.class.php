<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet', 'rdbms.join.JoinExtractable', 'util.HashmapIterator');

  /**
   * Class wrapper for table person, database JOBS
   * (Auto-generated on Wed, 16 May 2007 14:44:35 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class Person extends DataSet implements JoinExtractable {
    public
      $person_id          = 0,
      $name               = '',
      $job_id             = 0,
      $department_id      = 0;
  
    protected
      $cache= array(
        'Department' => array(),
        'Job' => array(),
        'DepartmentChief' => array(),
      );

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('JOBS.Person');
        $peer->setConnection('jobs');
        $peer->setIdentity('person_id');
        $peer->setPrimary(array('person_id'));
        $peer->setTypes(array(
          'person_id'     => array('%d', FieldType::NUMERIC, FALSE),
          'name'          => array('%s', FieldType::VARCHAR, FALSE),
          'job_id'        => array('%d', FieldType::NUMERIC, FALSE),
          'department_id' => array('%d', FieldType::NUMERIC, FALSE),
        ));
        $peer->setRelations(array(
          'Department' => array(
            'classname' => 'net.xp_framework.unittest.rdbms.dataset.Department',
            'key'       => array(
              'department_id' => 'department_id',
            ),
          ),
          'Job' => array(
            'classname' => 'net.xp_framework.unittest.rdbms.dataset.Job',
            'key'       => array(
              'job_id' => 'job_id',
            ),
          ),
          'DepartmentChief' => array(
            'classname' => 'net.xp_framework.unittest.rdbms.dataset.Department',
            'key'       => array(
              'person_id' => 'chief_id',
            ),
          ),
        ));
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
     * column factory
     *
     * @param   string name
     * @return  rdbms.Column
     * @throws  lang.IllegalArgumentException
     */
    static public function column($name) {
      return self::getPeer()->column($name);
    }
  
    /**
     * Gets an instance of this object by index "PRIMARY"
     * 
     * @param   int person_id
     * @return  net.xp_framework.unittest.rdbms.dataset.Person entitiy object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByPerson_id($person_id) {
      $r= self::getPeer()->doSelect(new Criteria(array('person_id', $person_id, EQUAL)));
      return $r ? $r[0] : NULL;    }

    /**
     * Gets an instance of this object by index "job"
     * 
     * @param   int job_id
     * @return  net.xp_framework.unittest.rdbms.dataset.Person[] entity objects
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByJob_id($job_id) {
      return self::getPeer()->doSelect(new Criteria(array('job_id', $job_id, EQUAL)));    }

    /**
     * Gets an instance of this object by index "department"
     * 
     * @param   int department_id
     * @return  net.xp_framework.unittest.rdbms.dataset.Person[] entity objects
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByDepartment_id($department_id) {
      return self::getPeer()->doSelect(new Criteria(array('department_id', $department_id, EQUAL)));    }

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

    /**
     * Retrieves the Department entity
     * referenced by department_id=>department_id
     *
     * @return  net.xp_framework.unittest.rdbms.dataset.Department entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getDepartment() {
      $r= ($this->cached['Department']) ?
        array_values($this->cache['Department']) :
        XPClass::forName('net.xp_framework.unittest.rdbms.dataset.Department')
          ->getMethod('getPeer')
          ->invoke()
          ->doSelect(new Criteria(
          array('department_id', $this->getDepartment_id(), EQUAL)
      ));
      return $r ? $r[0] : NULL;
    }

    /**
     * Retrieves the Job entity
     * referenced by job_id=>job_id
     *
     * @return  net.xp_framework.unittest.rdbms.dataset.Job entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getJob() {
      $r= ($this->cached['Job']) ?
        array_values($this->cache['Job']) :
        XPClass::forName('net.xp_framework.unittest.rdbms.dataset.Job')
          ->getMethod('getPeer')
          ->invoke()
          ->doSelect(new Criteria(
          array('job_id', $this->getJob_id(), EQUAL)
      ));
      return $r ? $r[0] : NULL;
    }

    /**
     * Retrieves an array of all Department entities referencing
     * this entity by chief_id=>person_id
     *
     * @return  net.xp_framework.unittest.rdbms.dataset.Department[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getDepartmentChiefList() {
      if ($this->cached['DepartmentChief']) return array_values($this->cache['DepartmentChief']);
      return XPClass::forName('net.xp_framework.unittest.rdbms.dataset.Department')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('chief_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Department entities referencing
     * this entity by chief_id=>person_id
     *
     * @return  rdbms.ResultIterator<net.xp_framework.unittest.rdbms.dataset.Department>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getDepartmentChiefIterator() {
      if ($this->cached['DepartmentChief']) return new HashmapIterator($this->cache['DepartmentChief']);
      return XPClass::forName('net.xp_framework.unittest.rdbms.dataset.Department')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('chief_id', $this->getPerson_id(), EQUAL)
      ));
    }
  }
?>
