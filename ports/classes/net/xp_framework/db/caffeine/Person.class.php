<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses('rdbms.DataSet', 'util.HashmapIterator');

  /**
   * Class wrapper for table person, database CAFFEINE
   * (This class was auto-generated, so please do not change manually)
   *
   * @purpose  Datasource accessor
   */
  class Person extends DataSet {
    public
      $person_id          = 0,
      $cn                 = '',
      $realname           = '',
      $email              = '',
      $lastchange         = NULL,
      $changedby          = '',
      $bz_id              = 0;
  
    protected
      $cache= array(
        'ContributorPerson' => array(),
        'RfcAuthor' => array(),
      );

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('CAFFEINE.person');
        $peer->setConnection('caffeine');
        $peer->setIdentity('person_id');
        $peer->setPrimary(array('person_id'));
        $peer->setTypes(array(
          'person_id'           => array('%d', FieldType::INT, FALSE),
          'cn'                  => array('%s', FieldType::VARCHAR, FALSE),
          'realname'            => array('%s', FieldType::VARCHAR, FALSE),
          'email'               => array('%s', FieldType::VARCHAR, FALSE),
          'lastchange'          => array('%s', FieldType::DATETIME, FALSE),
          'changedby'           => array('%s', FieldType::VARCHAR, FALSE),
          'bz_id'               => array('%d', FieldType::INT, FALSE)
        ));
        $peer->setRelations(array(
          'ContributorPerson' => array(
            'classname' => 'net.xp_framework.db.caffeine.Contributor',
            'key'       => array(
              'person_id' => 'person_id',
            ),
          ),
          'RfcAuthor' => array(
            'classname' => 'net.xp_framework.db.caffeine.Rfc',
            'key'       => array(
              'person_id' => 'author_id',
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
     * @return  net.xp_framework.db.caffeine.Person entity object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByPerson_id($person_id) {
      $r= self::getPeer()->doSelect(new Criteria(array('person_id', $person_id, EQUAL)));
      return $r ? $r[0] : NULL;
    }

    /**
     * Gets an instance of this object by index "user_Kcn"
     * 
     * @param   string cn
     * @return  net.xp_framework.db.caffeine.Person entity object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByCn($cn) {
      $r= self::getPeer()->doSelect(new Criteria(array('cn', $cn, EQUAL)));
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
     * Retrieves cn
     *
     * @return  string
     */
    public function getCn() {
      return $this->cn;
    }
      
    /**
     * Sets cn
     *
     * @param   string cn
     * @return  string the previous value
     */
    public function setCn($cn) {
      return $this->_change('cn', $cn);
    }

    /**
     * Retrieves realname
     *
     * @return  string
     */
    public function getRealname() {
      return $this->realname;
    }
      
    /**
     * Sets realname
     *
     * @param   string realname
     * @return  string the previous value
     */
    public function setRealname($realname) {
      return $this->_change('realname', $realname);
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
     * Retrieves an array of all Contributor entities referencing
     * this entity by person_id=>person_id
     *
     * @return  net.xp_framework.db.caffeine.Contributor[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getContributorPersonList() {
      if ($this->cached['ContributorPerson']) return array_values($this->cache['ContributorPerson']);
      return XPClass::forName('net.xp_framework.db.caffeine.Contributor')
        ->getMethod('getPeer')
        ->invoke(NULL)
        ->doSelect(new Criteria(
          array('person_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Contributor entities referencing
     * this entity by person_id=>person_id
     *
     * @return  rdbms.ResultIterator<net.xp_framework.db.caffeine.Contributor>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getContributorPersonIterator() {
      if ($this->cached['ContributorPerson']) return new HashmapIterator($this->cache['ContributorPerson']);
      return XPClass::forName('net.xp_framework.db.caffeine.Contributor')
        ->getMethod('getPeer')
        ->invoke(NULL)
        ->iteratorFor(new Criteria(
          array('person_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Rfc entities referencing
     * this entity by author_id=>person_id
     *
     * @return  net.xp_framework.db.caffeine.Rfc[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getRfcAuthorList() {
      if ($this->cached['RfcAuthor']) return array_values($this->cache['RfcAuthor']);
      return XPClass::forName('net.xp_framework.db.caffeine.Rfc')
        ->getMethod('getPeer')
        ->invoke(NULL)
        ->doSelect(new Criteria(
          array('author_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Rfc entities referencing
     * this entity by author_id=>person_id
     *
     * @return  rdbms.ResultIterator<net.xp_framework.db.caffeine.Rfc>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getRfcAuthorIterator() {
      if ($this->cached['RfcAuthor']) return new HashmapIterator($this->cache['RfcAuthor']);
      return XPClass::forName('net.xp_framework.db.caffeine.Rfc')
        ->getMethod('getPeer')
        ->invoke(NULL)
        ->iteratorFor(new Criteria(
          array('author_id', $this->getPerson_id(), EQUAL)
      ));
    }
  }
?>