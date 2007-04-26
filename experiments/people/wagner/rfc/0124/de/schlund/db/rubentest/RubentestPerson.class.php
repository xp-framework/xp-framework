<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet');

  /**
   * Class wrapper for table person, database Ruben_Test_PS
   * (Auto-generated on Wed, 04 Apr 2007 10:45:30 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class RubentestPerson extends DataSet {

    protected
      $_isLoaded= false,
      $_loadCrit= NULL;

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('Ruben_Test_PS.person');
        $peer->setConnection('localhost');
        $peer->setIdentity('person_id');
        $peer->setPrimary(array('person_id'));
        $peer->setTypes(array(
          'person_id'           => array('%d', FieldType::INT, FALSE),
          'name'                => array('%s', FieldType::VARCHAR, FALSE)
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
     * Gets an instance of this object by index "PRIMARY"
     * 
     * @param   int person_id
     * @return  de.schlund.db.rubentest.RubentestPerson entitiy object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByPerson_id($person_id) {
      return new self(array(
        'person_id'  => $person_id,
        '_loadCrit' => new Criteria(array('person_id', $person_id, EQUAL))
      ));
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
     * Retrieves an array of all Job entities referencing
     * this entity by person_id=>person_id
     *
     * @return  de.schlund.db.rubentest.RubentestJob[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getJobPersonList() {
      return XPClass::forName('de.schlund.db.rubentest.RubentestJob')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('person_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Job entities referencing
     * this entity by person_id=>person_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.rubentest.RubentestJob>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getJobPersonIterator() {
      return XPClass::forName('de.schlund.db.rubentest.RubentestJob')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('person_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves the Toilette entity referencing
     * this entity by person_id=>person_id
     *
     * @return  de.schlund.db.rubentest.RubentestToilette entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getToilettePerson() {
      $r= XPClass::forName('de.schlund.db.rubentest.RubentestToilette')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('person_id', $this->getPerson_id(), EQUAL)
      ));
      return $r ? $r[0] : NULL;
    }
  }
?>