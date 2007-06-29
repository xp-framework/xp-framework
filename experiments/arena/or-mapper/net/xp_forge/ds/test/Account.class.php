<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses('rdbms.DataSet', 'util.HashmapIterator');

  /**
   * Class wrapper for table account, database test
   * (Auto-generated on Fri, 29 Jun 2007 13:41:44 +0200 by friebe)
   *
   * @purpose  Datasource accessor
   */
  class Account extends DataSet {
    public
      $account_id         = 0,
      $person_id          = 0,
      $username           = '',
      $password           = '',
      $lastchange         = NULL,
      $changedby          = '',
      $bz_id              = 0;
  
    protected
      $cache= array(
        'Person' => array(),
      );

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('test.account');
        $peer->setConnection('test-ds');
        $peer->setIdentity('account_id');
        $peer->setPrimary(array('account_id'));
        $peer->setTypes(array(
          'account_id'          => array('%d', FieldType::INT, FALSE),
          'person_id'           => array('%d', FieldType::INT, FALSE),
          'username'            => array('%s', FieldType::VARCHAR, FALSE),
          'password'            => array('%s', FieldType::VARCHAR, FALSE),
          'lastchange'          => array('%s', FieldType::DATETIME, FALSE),
          'changedby'           => array('%s', FieldType::VARCHAR, FALSE),
          'bz_id'               => array('%d', FieldType::INT, FALSE)
        ));
        $peer->setRelations(array(
          'Person' => array(
            'classname' => 'net.xp_forge.ds.test.Person',
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
     * @param   int account_id
     * @return  net.xp_forge.ds.test.Account entitiy object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByAccount_id($account_id) {
      $r= self::getPeer()->doSelect(new Criteria(array('account_id', $account_id, EQUAL)));
      return $r ? $r[0] : NULL;
    }

    /**
     * Gets an instance of this object by index "FK_accountperson"
     * 
     * @param   int person_id
     * @return  net.xp_forge.ds.test.Account[] entity objects
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByPerson_id($person_id) {
      return self::getPeer()->doSelect(new Criteria(array('person_id', $person_id, EQUAL)));
    }

    /**
     * Retrieves account_id
     *
     * @return  int
     */
    public function getAccount_id() {
      return $this->account_id;
    }
      
    /**
     * Sets account_id
     *
     * @param   int account_id
     * @return  int the previous value
     */
    public function setAccount_id($account_id) {
      return $this->_change('account_id', $account_id);
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
     * Retrieves username
     *
     * @return  string
     */
    public function getUsername() {
      return $this->username;
    }
      
    /**
     * Sets username
     *
     * @param   string username
     * @return  string the previous value
     */
    public function setUsername($username) {
      return $this->_change('username', $username);
    }

    /**
     * Retrieves password
     *
     * @return  string
     */
    public function getPassword() {
      return $this->password;
    }
      
    /**
     * Sets password
     *
     * @param   string password
     * @return  string the previous value
     */
    public function setPassword($password) {
      return $this->_change('password', $password);
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
     * Retrieves the Person entity
     * referenced by person_id=>person_id
     *
     * @return  net.xp_forge.ds.test.Person entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getPerson() {
      $r= ($this->cached['Person']) ?
        array_values($this->cache['Person']) :
        XPClass::forName('net.xp_forge.ds.test.Person')
          ->getMethod('getPeer')
          ->invoke()
          ->doSelect(new Criteria(
          array('person_id', $this->getPerson_id(), EQUAL)
      ));
      return $r ? $r[0] : NULL;
    }
  }
?>