<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet');

  /**
   * Class wrapper for table email, database METHADON
   * (Auto-generated on Wed, 04 Apr 2007 10:45:27 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class Email extends DataSet {

    protected
      $_isLoaded= false,
      $_loadCrit= NULL;

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('METHADON..email');
        $peer->setConnection('sybintern');
        $peer->setIdentity('email_id');
        $peer->setPrimary(array('email_id'));
        $peer->setTypes(array(
          'address'             => array('%s', FieldType::VARCHAR, FALSE),
          'changedby'           => array('%s', FieldType::VARCHAR, FALSE),
          'email_id'            => array('%d', FieldType::NUMERIC, FALSE),
          'email_type_id'       => array('%d', FieldType::NUMERIC, FALSE),
          'person_id'           => array('%d', FieldType::NUMERIC, FALSE),
          'creator_id'          => array('%d', FieldType::NUMERIC, FALSE),
          'bz_id'               => array('%d', FieldType::NUMERIC, FALSE),
          'lastchange'          => array('%s', FieldType::DATETIME, FALSE),
          'invalid_at'          => array('%s', FieldType::DATETIMN, TRUE)
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
     * Gets an instance of this object by index "PK_EMAIL"
     * 
     * @param   int email_id
     * @return  de.schlund.db.methadon.Email entitiy object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByEmail_id($email_id) {
      return new self(array(
        'email_id'  => $email_id,
        '_loadCrit' => new Criteria(array('email_id', $email_id, EQUAL))
      ));
    }

    /**
     * Retrieves address
     *
     * @return  string
     */
    public function getAddress() {
      return $this->address;
    }
      
    /**
     * Sets address
     *
     * @param   string address
     * @return  string the previous value
     */
    public function setAddress($address) {
      return $this->_change('address', $address);
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
     * Retrieves email_id
     *
     * @return  int
     */
    public function getEmail_id() {
      return $this->email_id;
    }
      
    /**
     * Sets email_id
     *
     * @param   int email_id
     * @return  int the previous value
     */
    public function setEmail_id($email_id) {
      return $this->_change('email_id', $email_id);
    }

    /**
     * Retrieves email_type_id
     *
     * @return  int
     */
    public function getEmail_type_id() {
      return $this->email_type_id;
    }
      
    /**
     * Sets email_type_id
     *
     * @param   int email_type_id
     * @return  int the previous value
     */
    public function setEmail_type_id($email_type_id) {
      return $this->_change('email_type_id', $email_type_id);
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
     * Retrieves creator_id
     *
     * @return  int
     */
    public function getCreator_id() {
      return $this->creator_id;
    }
      
    /**
     * Sets creator_id
     *
     * @param   int creator_id
     * @return  int the previous value
     */
    public function setCreator_id($creator_id) {
      return $this->_change('creator_id', $creator_id);
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
     * Retrieves invalid_at
     *
     * @return  util.Date
     */
    public function getInvalid_at() {
      return $this->invalid_at;
    }
      
    /**
     * Sets invalid_at
     *
     * @param   util.Date invalid_at
     * @return  util.Date the previous value
     */
    public function setInvalid_at($invalid_at) {
      return $this->_change('invalid_at', $invalid_at);
    }

    /**
     * Retrieves the Person entity
     * referenced by person_id=>person_id
     *
     * @return  de.schlund.db.methadon.Person entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getPerson() {
      $r= XPClass::forName('de.schlund.db.methadon.Person')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('person_id', $this->getPerson_id(), EQUAL)
      ));
      return $r ? $r[0] : NULL;
    }

    /**
     * Retrieves the Bearbeitungszustand entity
     * referenced by bz_id=>bz_id
     *
     * @return  de.schlund.db.methadon.Bearbeitungszustand entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getBz() {
      $r= XPClass::forName('de.schlund.db.methadon.Bearbeitungszustand')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('bz_id', $this->getBz_id(), EQUAL)
      ));
      return $r ? $r[0] : NULL;
    }

    /**
     * Retrieves the Person entity
     * referenced by person_id=>creator_id
     *
     * @return  de.schlund.db.methadon.Person entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getCreator() {
      $r= XPClass::forName('de.schlund.db.methadon.Person')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('person_id', $this->getCreator_id(), EQUAL)
      ));
      return $r ? $r[0] : NULL;
    }

    /**
     * Retrieves the Email_type entity
     * referenced by email_type_id=>email_type_id
     *
     * @return  de.schlund.db.methadon.Email_type entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEmail_type() {
      $r= XPClass::forName('de.schlund.db.methadon.Email_type')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('email_type_id', $this->getEmail_type_id(), EQUAL)
      ));
      return $r ? $r[0] : NULL;
    }

    /**
     * Retrieves an array of all End_device entities referencing
     * this entity by email_id=>email_id
     *
     * @return  de.schlund.db.methadon.End_device[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEnd_deviceEmailList() {
      return XPClass::forName('de.schlund.db.methadon.End_device')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('email_id', $this->getEmail_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all End_device entities referencing
     * this entity by email_id=>email_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.End_device>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEnd_deviceEmailIterator() {
      return XPClass::forName('de.schlund.db.methadon.End_device')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('email_id', $this->getEmail_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Email_alias entities referencing
     * this entity by email_id=>email_id
     *
     * @return  de.schlund.db.methadon.Email_alias[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEmail_aliasEmailList() {
      return XPClass::forName('de.schlund.db.methadon.Email_alias')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('email_id', $this->getEmail_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Email_alias entities referencing
     * this entity by email_id=>email_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Email_alias>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEmail_aliasEmailIterator() {
      return XPClass::forName('de.schlund.db.methadon.Email_alias')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('email_id', $this->getEmail_id(), EQUAL)
      ));
    }
  }
?>