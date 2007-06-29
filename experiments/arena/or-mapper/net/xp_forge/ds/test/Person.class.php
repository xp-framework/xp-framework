<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses('rdbms.DataSet', 'util.HashmapIterator');

  /**
   * Class wrapper for table person, database test
   * (Auto-generated on Thu, 28 Jun 2007 22:23:46 +0200 by Timm Friebe)
   *
   * @purpose  Datasource accessor
   */
  class Person extends DataSet {
    public
      $person_id          = 0,
      $firstname          = '',
      $lastname           = '',
      $email              = '',
      $lastchange         = NULL,
      $changedby          = '',
      $bz_id              = 0;
  
    protected
      $cache= array(
        'AccountPerson' => array(),
      );

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('test.person');
        $peer->setConnection('test-ds');
        $peer->setIdentity('person_id');
        $peer->setPrimary(array('person_id'));
        $peer->setTypes(array(
          'person_id'           => array('%d', FieldType::INT, FALSE),
          'firstname'           => array('%s', FieldType::VARCHAR, FALSE),
          'lastname'            => array('%s', FieldType::VARCHAR, FALSE),
          'email'               => array('%s', FieldType::VARCHAR, FALSE),
          'lastchange'          => array('%s', FieldType::DATETIME, FALSE),
          'changedby'           => array('%s', FieldType::VARCHAR, FALSE),
          'bz_id'               => array('%d', FieldType::INT, FALSE)
        ));
        $peer->setRelations(array(
          'AccountPerson' => array(
            'classname' => 'net.xp_forge.ds.test.Account',
            'key'       => array(
              'person_id' => 'person_id',
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
    public static function column($name) {
      return Peer::forName(__CLASS__)->column($name);
    }
  
    /**
     * Gets an instance of this object by index "PRIMARY"
     * 
     * @param   int person_id
     * @return  net.xp_forge.ds.test.Person entitiy object
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
     * Retrieves firstname
     *
     * @return  string
     */
    public function getFirstname() {
      return $this->firstname;
    }
      
    /**
     * Sets firstname
     *
     * @param   string firstname
     * @return  string the previous value
     */
    public function setFirstname($firstname) {
      return $this->_change('firstname', $firstname);
    }

    /**
     * Retrieves lastname
     *
     * @return  string
     */
    public function getLastname() {
      return $this->lastname;
    }
      
    /**
     * Sets lastname
     *
     * @param   string lastname
     * @return  string the previous value
     */
    public function setLastname($lastname) {
      return $this->_change('lastname', $lastname);
    }

    /**
     * Retrieves email
     *
     * @return  string
     */
    public function getEmail() {
      return $this->email;
    }
      
    /**
     * Sets email
     *
     * @param   string email
     * @return  string the previous value
     */
    public function setEmail($email) {
      return $this->_change('email', $email);
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
     * Retrieves an array of all Account entities referencing
     * this entity by person_id=>person_id
     *
     * @return  net.xp_forge.ds.test.Account[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getAccountPersonList() {
      if ($this->cached['AccountPerson']) return array_values($this->cache['AccountPerson']);
      return XPClass::forName('net.xp_forge.ds.test.Account')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('person_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Account entities referencing
     * this entity by person_id=>person_id
     *
     * @return  rdbms.ResultIterator<net.xp_forge.ds.test.Account>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getAccountPersonIterator() {
      if ($this->cached['AccountPerson']) return new HashmapIterator($this->cache['AccountPerson']);
      return XPClass::forName('net.xp_forge.ds.test.Account')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('person_id', $this->getPerson_id(), EQUAL)
      ));
    }
  }
?>