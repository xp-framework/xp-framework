<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet', 'rdbms.join.JoinExtractable', 'util.HashmapIterator');

  /**
   * Class wrapper for table department, database JOBS
   * (Auto-generated on Wed, 16 May 2007 14:44:35 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class Department extends DataSet implements JoinExtractable {
    public
      $department_id      = 0,
      $name               = '',
      $chief_id           = 0;
  
    protected
      $cache= array(
        'Chief' => array(),
        'PersonDepartment' => array(),
      );

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('JOBS.Department');
        $peer->setConnection('jobs');
        $peer->setIdentity('department_id');
        $peer->setPrimary(array('department_id'));
        $peer->setTypes(array(
          'department_id' => array('%d', FieldType::NUMERIC, FALSE),
          'name'          => array('%s', FieldType::VARCHAR, FALSE),
          'chief_id'      => array('%d', FieldType::NUMERIC, FALSE),
        ));
        $peer->setRelations(array(
          'Chief' => array(
            'classname' => 'net.xp_framework.unittest.rdbms.dataset.Person',
            'key'       => array(
              'chief_id' => 'person_id',
            ),
          ),
          'PersonDepartment' => array(
            'classname' => 'net.xp_framework.unittest.rdbms.dataset.Person',
            'key'       => array(
              'department_id' => 'department_id',
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
     * @throws  lang.IllegalArumentException
     */
    static public function column($name) {
      return self::getPeer()->column($name);
    }
  
    /**
     * Gets an instance of this object by index "PRIMARY"
     * 
     * @param   int department_id
     * @return  net.xp_framework.unittest.rdbms.dataset.Department entitiy object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByDepartment_id($department_id) {
      $r= self::getPeer()->doSelect(new Criteria(array('department_id', $department_id, EQUAL)));
      return $r ? $r[0] : NULL;    }

    /**
     * Gets an instance of this object by index "chief"
     * 
     * @param   int chief_id
     * @return  net.xp_framework.unittest.rdbms.dataset.Department[] entity objects
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByChief_id($chief_id) {
      return self::getPeer()->doSelect(new Criteria(array('chief_id', $chief_id, EQUAL)));    }

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
     * @return  int
     */
    public function getChief_id() {
      return $this->chief_id;
    }
      
    /**
     * Sets chief_id
     *
     * @param   int chief_id
     * @return  int the previous value
     */
    public function setChief_id($chief_id) {
      return $this->_change('chief_id', $chief_id);
    }

    /**
     * Retrieves the Person entity
     * referenced by person_id=>chief_id
     *
     * @return  net.xp_framework.unittest.rdbms.dataset.Person entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getChief() {
      $r= ($this->cached['Chief']) ?
        array_values($this->cache['Chief']) :
        XPClass::forName('net.xp_framework.unittest.rdbms.dataset.Person')
          ->getMethod('getPeer')
          ->invoke()
          ->doSelect(new Criteria(
          array('person_id', $this->getChief_id(), EQUAL)
      ));
      return $r ? $r[0] : NULL;
    }

    /**
     * Retrieves an array of all Person entities referencing
     * this entity by department_id=>department_id
     *
     * @return  net.xp_framework.unittest.rdbms.dataset.Person[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getPersonDepartmentList() {
      if ($this->cached['PersonDepartment']) return array_values($this->cache['PersonDepartment']);
      return XPClass::forName('net.xp_framework.unittest.rdbms.dataset.Person')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('department_id', $this->getDepartment_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Person entities referencing
     * this entity by department_id=>department_id
     *
     * @return  rdbms.ResultIterator<net.xp_framework.unittest.rdbms.dataset.Person>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getPersonDepartmentIterator() {
      if ($this->cached['PersonDepartment']) return new HashmapIterator($this->cache['PersonDepartment']);
      return XPClass::forName('net.xp_framework.unittest.rdbms.dataset.Person')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('department_id', $this->getDepartment_id(), EQUAL)
      ));
    }
  }
?>
