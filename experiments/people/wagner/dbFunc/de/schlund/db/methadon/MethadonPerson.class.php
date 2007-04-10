<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet');

  /**
   * Class wrapper for table person, database METHADON
   * (Auto-generated on Wed, 04 Apr 2007 10:45:27 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class MethadonPerson extends DataSet {

    protected
      $_isLoaded= false,
      $_loadCrit= NULL;

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('METHADON..person');
        $peer->setConnection('sybintern');
        $peer->setIdentity('person_id');
        $peer->setPrimary(array('person_id'));
        $peer->setTypes(array(
          'passwd'              => array('%s', FieldType::VARCHAR, FALSE),
          'username'            => array('%s', FieldType::VARCHAR, FALSE),
          'name'                => array('%s', FieldType::VARCHAR, FALSE),
          'firstname'           => array('%s', FieldType::VARCHAR, FALSE),
          'mnemonic'            => array('%s', FieldType::VARCHAR, FALSE),
          'email'               => array('%s', FieldType::VARCHAR, FALSE),
          'phone'               => array('%s', FieldType::VARCHAR, TRUE),
          'mobile_phone'        => array('%s', FieldType::VARCHAR, TRUE),
          'fax'                 => array('%s', FieldType::VARCHAR, TRUE),
          'changedby'           => array('%s', FieldType::VARCHAR, FALSE),
          'sex'                 => array('%d', FieldType::INT, FALSE),
          'person_id'           => array('%d', FieldType::NUMERIC, FALSE),
          'person_type_id'      => array('%d', FieldType::NUMERIC, FALSE),
          'bz_id'               => array('%d', FieldType::NUMERIC, FALSE),
          'created_by'          => array('%d', FieldType::NUMERIC, FALSE),
          'lastchange'          => array('%s', FieldType::DATETIME, FALSE)
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
     * Gets an instance of this object by index "UN_MNEMIONC"
     * 
     * @param   string mnemonic
     * @return  de.schlund.db.methadon.MethadonPerson entitiy object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByMnemonic($mnemonic) {
      return new self(array(
        'mnemonic'  => $mnemonic,
        '_loadCrit' => new Criteria(array('mnemonic', $mnemonic, EQUAL))
      ));
    }

    /**
     * Gets an instance of this object by index "UN_USERNAME"
     * 
     * @param   string username
     * @return  de.schlund.db.methadon.MethadonPerson entitiy object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByUsername($username) {
      return new self(array(
        'username'  => $username,
        '_loadCrit' => new Criteria(array('username', $username, EQUAL))
      ));
    }

    /**
     * Gets an instance of this object by index "PERSON_I0"
     * 
     * @param   int person_type_id
     * @return  de.schlund.db.methadon.MethadonPerson[] entity objects
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByPerson_type_id($person_type_id) {
      $r= self::getPeer()->doSelect(new Criteria(array('person_type_id', $person_type_id, EQUAL)));
      foreach ($r as $e) $e->_isLoaded= true;
      return $r;
    }

    /**
     * Gets an instance of this object by index "PK_PERSON"
     * 
     * @param   int person_id
     * @return  de.schlund.db.methadon.MethadonPerson entitiy object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByPerson_id($person_id) {
      return new self(array(
        'person_id'  => $person_id,
        '_loadCrit' => new Criteria(array('person_id', $person_id, EQUAL))
      ));
    }

    /**
     * Retrieves passwd
     *
     * @return  string
     */
    public function getPasswd() {
      return $this->passwd;
    }
      
    /**
     * Sets passwd
     *
     * @param   string passwd
     * @return  string the previous value
     */
    public function setPasswd($passwd) {
      return $this->_change('passwd', $passwd);
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
     * Retrieves mnemonic
     *
     * @return  string
     */
    public function getMnemonic() {
      return $this->mnemonic;
    }
      
    /**
     * Sets mnemonic
     *
     * @param   string mnemonic
     * @return  string the previous value
     */
    public function setMnemonic($mnemonic) {
      return $this->_change('mnemonic', $mnemonic);
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
     * Retrieves phone
     *
     * @return  string
     */
    public function getPhone() {
      return $this->phone;
    }
      
    /**
     * Sets phone
     *
     * @param   string phone
     * @return  string the previous value
     */
    public function setPhone($phone) {
      return $this->_change('phone', $phone);
    }

    /**
     * Retrieves mobile_phone
     *
     * @return  string
     */
    public function getMobile_phone() {
      return $this->mobile_phone;
    }
      
    /**
     * Sets mobile_phone
     *
     * @param   string mobile_phone
     * @return  string the previous value
     */
    public function setMobile_phone($mobile_phone) {
      return $this->_change('mobile_phone', $mobile_phone);
    }

    /**
     * Retrieves fax
     *
     * @return  string
     */
    public function getFax() {
      return $this->fax;
    }
      
    /**
     * Sets fax
     *
     * @param   string fax
     * @return  string the previous value
     */
    public function setFax($fax) {
      return $this->_change('fax', $fax);
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
     * Retrieves sex
     *
     * @return  int
     */
    public function getSex() {
      return $this->sex;
    }
      
    /**
     * Sets sex
     *
     * @param   int sex
     * @return  int the previous value
     */
    public function setSex($sex) {
      return $this->_change('sex', $sex);
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
     * Retrieves person_type_id
     *
     * @return  int
     */
    public function getPerson_type_id() {
      return $this->person_type_id;
    }
      
    /**
     * Sets person_type_id
     *
     * @param   int person_type_id
     * @return  int the previous value
     */
    public function setPerson_type_id($person_type_id) {
      return $this->_change('person_type_id', $person_type_id);
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
     * Retrieves created_by
     *
     * @return  int
     */
    public function getCreated_by() {
      return $this->created_by;
    }
      
    /**
     * Sets created_by
     *
     * @param   int created_by
     * @return  int the previous value
     */
    public function setCreated_by($created_by) {
      return $this->_change('created_by', $created_by);
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
     * Retrieves the Person_type entity
     * referenced by person_type_id=>person_type_id
     *
     * @return  de.schlund.db.methadon.Person_type entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getPerson_type() {
      $r= XPClass::forName('de.schlund.db.methadon.Person_type')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('person_type_id', $this->getPerson_type_id(), EQUAL)
      ));
      return $r ? $r[0] : NULL;
    }

    /**
     * Retrieves an array of all Eventng entities referencing
     * this entity by person_id=>person_id
     *
     * @return  de.schlund.db.methadon.Eventng[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEventngPersonList() {
      return XPClass::forName('de.schlund.db.methadon.Eventng')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('person_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Eventng entities referencing
     * this entity by person_id=>person_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Eventng>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEventngPersonIterator() {
      return XPClass::forName('de.schlund.db.methadon.Eventng')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('person_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Company entities referencing
     * this entity by company_id=>person_id
     *
     * @return  de.schlund.db.methadon.Company[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getCompanyList() {
      return XPClass::forName('de.schlund.db.methadon.Company')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('company_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Company entities referencing
     * this entity by company_id=>person_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Company>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getCompanyIterator() {
      return XPClass::forName('de.schlund.db.methadon.Company')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('company_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Email entities referencing
     * this entity by person_id=>person_id
     *
     * @return  de.schlund.db.methadon.Email[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEmailPersonList() {
      return XPClass::forName('de.schlund.db.methadon.Email')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('person_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Email entities referencing
     * this entity by person_id=>person_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Email>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEmailPersonIterator() {
      return XPClass::forName('de.schlund.db.methadon.Email')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('person_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Email entities referencing
     * this entity by creator_id=>person_id
     *
     * @return  de.schlund.db.methadon.Email[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEmailCreatorList() {
      return XPClass::forName('de.schlund.db.methadon.Email')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('creator_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Email entities referencing
     * this entity by creator_id=>person_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Email>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEmailCreatorIterator() {
      return XPClass::forName('de.schlund.db.methadon.Email')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('creator_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Program_schedule entities referencing
     * this entity by tool_id=>person_id
     *
     * @return  de.schlund.db.methadon.Program_schedule[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getProgram_scheduleToolList() {
      return XPClass::forName('de.schlund.db.methadon.Program_schedule')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('tool_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Program_schedule entities referencing
     * this entity by tool_id=>person_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Program_schedule>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getProgram_scheduleToolIterator() {
      return XPClass::forName('de.schlund.db.methadon.Program_schedule')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('tool_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Program_schedule entities referencing
     * this entity by maintainer_id=>person_id
     *
     * @return  de.schlund.db.methadon.Program_schedule[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getProgram_scheduleMaintainerList() {
      return XPClass::forName('de.schlund.db.methadon.Program_schedule')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('maintainer_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Program_schedule entities referencing
     * this entity by maintainer_id=>person_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Program_schedule>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getProgram_scheduleMaintainerIterator() {
      return XPClass::forName('de.schlund.db.methadon.Program_schedule')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('maintainer_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Ts_sheet entities referencing
     * this entity by person_id=>person_id
     *
     * @return  de.schlund.db.methadon.Ts_sheet[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getTs_sheetPersonList() {
      return XPClass::forName('de.schlund.db.methadon.Ts_sheet')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('person_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Ts_sheet entities referencing
     * this entity by person_id=>person_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Ts_sheet>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getTs_sheetPersonIterator() {
      return XPClass::forName('de.schlund.db.methadon.Ts_sheet')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('person_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Location entities referencing
     * this entity by location_id=>person_id
     *
     * @return  de.schlund.db.methadon.Location[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getLocationList() {
      return XPClass::forName('de.schlund.db.methadon.Location')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('location_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Location entities referencing
     * this entity by location_id=>person_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Location>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getLocationIterator() {
      return XPClass::forName('de.schlund.db.methadon.Location')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('location_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Heredity_history entities referencing
     * this entity by child_id=>person_id
     *
     * @return  de.schlund.db.methadon.Heredity_history[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getHeredity_historyChildList() {
      return XPClass::forName('de.schlund.db.methadon.Heredity_history')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('child_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Heredity_history entities referencing
     * this entity by child_id=>person_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Heredity_history>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getHeredity_historyChildIterator() {
      return XPClass::forName('de.schlund.db.methadon.Heredity_history')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('child_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Heredity_history entities referencing
     * this entity by child_id=>person_id
     *
     * @return  de.schlund.db.methadon.Heredity_history[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getHeredity_historyChildList() {
      return XPClass::forName('de.schlund.db.methadon.Heredity_history')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('child_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Heredity_history entities referencing
     * this entity by child_id=>person_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Heredity_history>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getHeredity_historyChildIterator() {
      return XPClass::forName('de.schlund.db.methadon.Heredity_history')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('child_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Heredity_history entities referencing
     * this entity by person_id=>person_id
     *
     * @return  de.schlund.db.methadon.Heredity_history[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getHeredity_historyPersonList() {
      return XPClass::forName('de.schlund.db.methadon.Heredity_history')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('person_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Heredity_history entities referencing
     * this entity by person_id=>person_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Heredity_history>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getHeredity_historyPersonIterator() {
      return XPClass::forName('de.schlund.db.methadon.Heredity_history')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('person_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Heredity_history entities referencing
     * this entity by parent_person_id=>person_id
     *
     * @return  de.schlund.db.methadon.Heredity_history[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getHeredity_historyParent_personList() {
      return XPClass::forName('de.schlund.db.methadon.Heredity_history')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('parent_person_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Heredity_history entities referencing
     * this entity by parent_person_id=>person_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Heredity_history>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getHeredity_historyParent_personIterator() {
      return XPClass::forName('de.schlund.db.methadon.Heredity_history')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('parent_person_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all End_device entities referencing
     * this entity by person_id=>person_id
     *
     * @return  de.schlund.db.methadon.End_device[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEnd_devicePersonList() {
      return XPClass::forName('de.schlund.db.methadon.End_device')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('person_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all End_device entities referencing
     * this entity by person_id=>person_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.End_device>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEnd_devicePersonIterator() {
      return XPClass::forName('de.schlund.db.methadon.End_device')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('person_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all End_device entities referencing
     * this entity by creator_id=>person_id
     *
     * @return  de.schlund.db.methadon.End_device[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEnd_deviceCreatorList() {
      return XPClass::forName('de.schlund.db.methadon.End_device')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('creator_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all End_device entities referencing
     * this entity by creator_id=>person_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.End_device>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEnd_deviceCreatorIterator() {
      return XPClass::forName('de.schlund.db.methadon.End_device')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('creator_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Bug_notify entities referencing
     * this entity by person_id=>person_id
     *
     * @return  de.schlund.db.methadon.Bug_notify[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getBug_notifyPersonList() {
      return XPClass::forName('de.schlund.db.methadon.Bug_notify')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('person_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Bug_notify entities referencing
     * this entity by person_id=>person_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Bug_notify>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getBug_notifyPersonIterator() {
      return XPClass::forName('de.schlund.db.methadon.Bug_notify')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('person_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Person_todo entities referencing
     * this entity by tool_id=>person_id
     *
     * @return  de.schlund.db.methadon.Person_todo[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getPerson_todoToolList() {
      return XPClass::forName('de.schlund.db.methadon.Person_todo')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('tool_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Person_todo entities referencing
     * this entity by tool_id=>person_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Person_todo>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getPerson_todoToolIterator() {
      return XPClass::forName('de.schlund.db.methadon.Person_todo')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('tool_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Person_todo entities referencing
     * this entity by person_id=>person_id
     *
     * @return  de.schlund.db.methadon.Person_todo[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getPerson_todoPersonList() {
      return XPClass::forName('de.schlund.db.methadon.Person_todo')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('person_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Person_todo entities referencing
     * this entity by person_id=>person_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Person_todo>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getPerson_todoPersonIterator() {
      return XPClass::forName('de.schlund.db.methadon.Person_todo')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('person_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Theme entities referencing
     * this entity by creator_id=>person_id
     *
     * @return  de.schlund.db.methadon.Theme[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getThemeCreatorList() {
      return XPClass::forName('de.schlund.db.methadon.Theme')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('creator_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Theme entities referencing
     * this entity by creator_id=>person_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Theme>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getThemeCreatorIterator() {
      return XPClass::forName('de.schlund.db.methadon.Theme')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('creator_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Category entities referencing
     * this entity by category_id=>person_id
     *
     * @return  de.schlund.db.methadon.Category[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getCategoryList() {
      return XPClass::forName('de.schlund.db.methadon.Category')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('category_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Category entities referencing
     * this entity by category_id=>person_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Category>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getCategoryIterator() {
      return XPClass::forName('de.schlund.db.methadon.Category')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('category_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Gulp_allocation entities referencing
     * this entity by person_id=>person_id
     *
     * @return  de.schlund.db.methadon.Gulp_allocation[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getGulp_allocationPersonList() {
      return XPClass::forName('de.schlund.db.methadon.Gulp_allocation')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('person_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Gulp_allocation entities referencing
     * this entity by person_id=>person_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Gulp_allocation>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getGulp_allocationPersonIterator() {
      return XPClass::forName('de.schlund.db.methadon.Gulp_allocation')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('person_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Email_alias entities referencing
     * this entity by creator_id=>person_id
     *
     * @return  de.schlund.db.methadon.Email_alias[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEmail_aliasCreatorList() {
      return XPClass::forName('de.schlund.db.methadon.Email_alias')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('creator_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Email_alias entities referencing
     * this entity by creator_id=>person_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Email_alias>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEmail_aliasCreatorIterator() {
      return XPClass::forName('de.schlund.db.methadon.Email_alias')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('creator_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Eventng_organizer_matrix entities referencing
     * this entity by organizer_id=>person_id
     *
     * @return  de.schlund.db.methadon.Eventng_organizer_matrix[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEventng_organizer_matrixOrganizerList() {
      return XPClass::forName('de.schlund.db.methadon.Eventng_organizer_matrix')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('organizer_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Eventng_organizer_matrix entities referencing
     * this entity by organizer_id=>person_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Eventng_organizer_matrix>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEventng_organizer_matrixOrganizerIterator() {
      return XPClass::forName('de.schlund.db.methadon.Eventng_organizer_matrix')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('organizer_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Event entities referencing
     * this entity by person_id=>person_id
     *
     * @return  de.schlund.db.methadon.Event[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEventPersonList() {
      return XPClass::forName('de.schlund.db.methadon.Event')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('person_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Event entities referencing
     * this entity by person_id=>person_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Event>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEventPersonIterator() {
      return XPClass::forName('de.schlund.db.methadon.Event')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('person_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Department entities referencing
     * this entity by head_person_id=>person_id
     *
     * @return  de.schlund.db.methadon.Department[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getDepartmentHead_personList() {
      return XPClass::forName('de.schlund.db.methadon.Department')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('head_person_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Department entities referencing
     * this entity by head_person_id=>person_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Department>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getDepartmentHead_personIterator() {
      return XPClass::forName('de.schlund.db.methadon.Department')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('head_person_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Person_category_todo entities referencing
     * this entity by person_id=>person_id
     *
     * @return  de.schlund.db.methadon.Person_category_todo[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getPerson_category_todoPersonList() {
      return XPClass::forName('de.schlund.db.methadon.Person_category_todo')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('person_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Person_category_todo entities referencing
     * this entity by person_id=>person_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Person_category_todo>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getPerson_category_todoPersonIterator() {
      return XPClass::forName('de.schlund.db.methadon.Person_category_todo')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('person_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Employee entities referencing
     * this entity by employee_id=>person_id
     *
     * @return  de.schlund.db.methadon.Employee[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEmployeeList() {
      return XPClass::forName('de.schlund.db.methadon.Employee')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('employee_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Employee entities referencing
     * this entity by employee_id=>person_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Employee>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEmployeeIterator() {
      return XPClass::forName('de.schlund.db.methadon.Employee')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('employee_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Plane_department_structure entities referencing
     * this entity by department_id=>person_id
     *
     * @return  de.schlund.db.methadon.Plane_department_structure[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getPlane_department_structureDepartmentList() {
      return XPClass::forName('de.schlund.db.methadon.Plane_department_structure')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('department_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Plane_department_structure entities referencing
     * this entity by department_id=>person_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Plane_department_structure>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getPlane_department_structureDepartmentIterator() {
      return XPClass::forName('de.schlund.db.methadon.Plane_department_structure')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('department_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Plane_department_structure entities referencing
     * this entity by master_department_id=>person_id
     *
     * @return  de.schlund.db.methadon.Plane_department_structure[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getPlane_department_structureMaster_departmentList() {
      return XPClass::forName('de.schlund.db.methadon.Plane_department_structure')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('master_department_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Plane_department_structure entities referencing
     * this entity by master_department_id=>person_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Plane_department_structure>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getPlane_department_structureMaster_departmentIterator() {
      return XPClass::forName('de.schlund.db.methadon.Plane_department_structure')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('master_department_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Person_category_matrix entities referencing
     * this entity by person_id=>person_id
     *
     * @return  de.schlund.db.methadon.Person_category_matrix[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getPerson_category_matrixPersonList() {
      return XPClass::forName('de.schlund.db.methadon.Person_category_matrix')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('person_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Person_category_matrix entities referencing
     * this entity by person_id=>person_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Person_category_matrix>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getPerson_category_matrixPersonIterator() {
      return XPClass::forName('de.schlund.db.methadon.Person_category_matrix')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('person_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Plane_right_matrix entities referencing
     * this entity by person_id=>person_id
     *
     * @return  de.schlund.db.methadon.Plane_right_matrix[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getPlane_right_matrixPersonList() {
      return XPClass::forName('de.schlund.db.methadon.Plane_right_matrix')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('person_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Plane_right_matrix entities referencing
     * this entity by person_id=>person_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Plane_right_matrix>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getPlane_right_matrixPersonIterator() {
      return XPClass::forName('de.schlund.db.methadon.Plane_right_matrix')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('person_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all External_employee entities referencing
     * this entity by officer_id=>person_id
     *
     * @return  de.schlund.db.methadon.External_employee[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getExternal_employeeOfficerList() {
      return XPClass::forName('de.schlund.db.methadon.External_employee')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('officer_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all External_employee entities referencing
     * this entity by officer_id=>person_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.External_employee>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getExternal_employeeOfficerIterator() {
      return XPClass::forName('de.schlund.db.methadon.External_employee')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('officer_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Abstract_right_matrix entities referencing
     * this entity by person_id=>person_id
     *
     * @return  de.schlund.db.methadon.Abstract_right_matrix[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getAbstract_right_matrixPersonList() {
      return XPClass::forName('de.schlund.db.methadon.Abstract_right_matrix')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('person_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Abstract_right_matrix entities referencing
     * this entity by person_id=>person_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Abstract_right_matrix>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getAbstract_right_matrixPersonIterator() {
      return XPClass::forName('de.schlund.db.methadon.Abstract_right_matrix')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('person_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Abstract_right_matrix entities referencing
     * this entity by person_id=>person_id
     *
     * @return  de.schlund.db.methadon.Abstract_right_matrix[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getAbstract_right_matrixPersonList() {
      return XPClass::forName('de.schlund.db.methadon.Abstract_right_matrix')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('person_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Abstract_right_matrix entities referencing
     * this entity by person_id=>person_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Abstract_right_matrix>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getAbstract_right_matrixPersonIterator() {
      return XPClass::forName('de.schlund.db.methadon.Abstract_right_matrix')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('person_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Ts_student_vacation entities referencing
     * this entity by person_id=>person_id
     *
     * @return  de.schlund.db.methadon.Ts_student_vacation[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getTs_student_vacationPersonList() {
      return XPClass::forName('de.schlund.db.methadon.Ts_student_vacation')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('person_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Ts_student_vacation entities referencing
     * this entity by person_id=>person_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Ts_student_vacation>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getTs_student_vacationPersonIterator() {
      return XPClass::forName('de.schlund.db.methadon.Ts_student_vacation')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('person_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Pim_todo entities referencing
     * this entity by person_id=>person_id
     *
     * @return  de.schlund.db.methadon.Pim_todo[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getPim_todoPersonList() {
      return XPClass::forName('de.schlund.db.methadon.Pim_todo')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('person_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Pim_todo entities referencing
     * this entity by person_id=>person_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Pim_todo>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getPim_todoPersonIterator() {
      return XPClass::forName('de.schlund.db.methadon.Pim_todo')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('person_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Person_resource_category entities referencing
     * this entity by person_id=>person_id
     *
     * @return  de.schlund.db.methadon.Person_resource_category[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getPerson_resource_categoryPersonList() {
      return XPClass::forName('de.schlund.db.methadon.Person_resource_category')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('person_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Person_resource_category entities referencing
     * this entity by person_id=>person_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Person_resource_category>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getPerson_resource_categoryPersonIterator() {
      return XPClass::forName('de.schlund.db.methadon.Person_resource_category')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('person_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Module entities referencing
     * this entity by maintainer_id=>person_id
     *
     * @return  de.schlund.db.methadon.Module[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getModuleMaintainerList() {
      return XPClass::forName('de.schlund.db.methadon.Module')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('maintainer_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Module entities referencing
     * this entity by maintainer_id=>person_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Module>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getModuleMaintainerIterator() {
      return XPClass::forName('de.schlund.db.methadon.Module')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('maintainer_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Plane_person_category_matrix entities referencing
     * this entity by person_id=>person_id
     *
     * @return  de.schlund.db.methadon.Plane_person_category_matrix[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getPlane_person_category_matrixPersonList() {
      return XPClass::forName('de.schlund.db.methadon.Plane_person_category_matrix')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('person_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Plane_person_category_matrix entities referencing
     * this entity by person_id=>person_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Plane_person_category_matrix>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getPlane_person_category_matrixPersonIterator() {
      return XPClass::forName('de.schlund.db.methadon.Plane_person_category_matrix')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('person_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Plane_person_category_matrix entities referencing
     * this entity by category_id=>person_id
     *
     * @return  de.schlund.db.methadon.Plane_person_category_matrix[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getPlane_person_category_matrixCategoryList() {
      return XPClass::forName('de.schlund.db.methadon.Plane_person_category_matrix')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('category_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Plane_person_category_matrix entities referencing
     * this entity by category_id=>person_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Plane_person_category_matrix>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getPlane_person_category_matrixCategoryIterator() {
      return XPClass::forName('de.schlund.db.methadon.Plane_person_category_matrix')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('category_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Plane_person_category_matrix entities referencing
     * this entity by subgroup_id=>person_id
     *
     * @return  de.schlund.db.methadon.Plane_person_category_matrix[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getPlane_person_category_matrixSubgroupList() {
      return XPClass::forName('de.schlund.db.methadon.Plane_person_category_matrix')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('subgroup_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Plane_person_category_matrix entities referencing
     * this entity by subgroup_id=>person_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Plane_person_category_matrix>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getPlane_person_category_matrixSubgroupIterator() {
      return XPClass::forName('de.schlund.db.methadon.Plane_person_category_matrix')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('subgroup_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Ims entities referencing
     * this entity by sender_id=>person_id
     *
     * @return  de.schlund.db.methadon.Ims[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getImsSenderList() {
      return XPClass::forName('de.schlund.db.methadon.Ims')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('sender_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Ims entities referencing
     * this entity by sender_id=>person_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Ims>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getImsSenderIterator() {
      return XPClass::forName('de.schlund.db.methadon.Ims')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('sender_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Ims entities referencing
     * this entity by recipient_id=>person_id
     *
     * @return  de.schlund.db.methadon.Ims[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getImsRecipientList() {
      return XPClass::forName('de.schlund.db.methadon.Ims')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('recipient_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Ims entities referencing
     * this entity by recipient_id=>person_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Ims>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getImsRecipientIterator() {
      return XPClass::forName('de.schlund.db.methadon.Ims')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('recipient_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Workflow entities referencing
     * this entity by maintainer_id=>person_id
     *
     * @return  de.schlund.db.methadon.Workflow[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getWorkflowMaintainerList() {
      return XPClass::forName('de.schlund.db.methadon.Workflow')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('maintainer_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Workflow entities referencing
     * this entity by maintainer_id=>person_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Workflow>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getWorkflowMaintainerIterator() {
      return XPClass::forName('de.schlund.db.methadon.Workflow')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('maintainer_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Ts_user_info entities referencing
     * this entity by hr_id=>person_id
     *
     * @return  de.schlund.db.methadon.Ts_user_info[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getTs_user_infoHrList() {
      return XPClass::forName('de.schlund.db.methadon.Ts_user_info')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('hr_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Ts_user_info entities referencing
     * this entity by hr_id=>person_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Ts_user_info>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getTs_user_infoHrIterator() {
      return XPClass::forName('de.schlund.db.methadon.Ts_user_info')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('hr_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Ts_user_info entities referencing
     * this entity by person_id=>person_id
     *
     * @return  de.schlund.db.methadon.Ts_user_info[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getTs_user_infoPersonList() {
      return XPClass::forName('de.schlund.db.methadon.Ts_user_info')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('person_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Ts_user_info entities referencing
     * this entity by person_id=>person_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Ts_user_info>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getTs_user_infoPersonIterator() {
      return XPClass::forName('de.schlund.db.methadon.Ts_user_info')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('person_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Vacation_delegation entities referencing
     * this entity by person_id=>person_id
     *
     * @return  de.schlund.db.methadon.Vacation_delegation[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getVacation_delegationPersonList() {
      return XPClass::forName('de.schlund.db.methadon.Vacation_delegation')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('person_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Vacation_delegation entities referencing
     * this entity by person_id=>person_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Vacation_delegation>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getVacation_delegationPersonIterator() {
      return XPClass::forName('de.schlund.db.methadon.Vacation_delegation')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('person_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Fileshare_file_acl entities referencing
     * this entity by person_id=>person_id
     *
     * @return  de.schlund.db.methadon.Fileshare_file_acl[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getFileshare_file_aclPersonList() {
      return XPClass::forName('de.schlund.db.methadon.Fileshare_file_acl')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('person_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Fileshare_file_acl entities referencing
     * this entity by person_id=>person_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Fileshare_file_acl>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getFileshare_file_aclPersonIterator() {
      return XPClass::forName('de.schlund.db.methadon.Fileshare_file_acl')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('person_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Pim_note entities referencing
     * this entity by person_id=>person_id
     *
     * @return  de.schlund.db.methadon.Pim_note[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getPim_notePersonList() {
      return XPClass::forName('de.schlund.db.methadon.Pim_note')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('person_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Pim_note entities referencing
     * this entity by person_id=>person_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Pim_note>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getPim_notePersonIterator() {
      return XPClass::forName('de.schlund.db.methadon.Pim_note')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('person_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Resource_reservation entities referencing
     * this entity by person_id=>person_id
     *
     * @return  de.schlund.db.methadon.Resource_reservation[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getResource_reservationPersonList() {
      return XPClass::forName('de.schlund.db.methadon.Resource_reservation')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('person_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Resource_reservation entities referencing
     * this entity by person_id=>person_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Resource_reservation>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getResource_reservationPersonIterator() {
      return XPClass::forName('de.schlund.db.methadon.Resource_reservation')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('person_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Message entities referencing
     * this entity by creator_id=>person_id
     *
     * @return  de.schlund.db.methadon.Message[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getMessageCreatorList() {
      return XPClass::forName('de.schlund.db.methadon.Message')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('creator_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Message entities referencing
     * this entity by creator_id=>person_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Message>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getMessageCreatorIterator() {
      return XPClass::forName('de.schlund.db.methadon.Message')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('creator_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Message entities referencing
     * this entity by receiver_id=>person_id
     *
     * @return  de.schlund.db.methadon.Message[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getMessageReceiverList() {
      return XPClass::forName('de.schlund.db.methadon.Message')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('receiver_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Message entities referencing
     * this entity by receiver_id=>person_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Message>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getMessageReceiverIterator() {
      return XPClass::forName('de.schlund.db.methadon.Message')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('receiver_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Ts_user_history entities referencing
     * this entity by person_id=>person_id
     *
     * @return  de.schlund.db.methadon.Ts_user_history[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getTs_user_historyPersonList() {
      return XPClass::forName('de.schlund.db.methadon.Ts_user_history')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('person_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Ts_user_history entities referencing
     * this entity by person_id=>person_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Ts_user_history>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getTs_user_historyPersonIterator() {
      return XPClass::forName('de.schlund.db.methadon.Ts_user_history')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('person_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Fileshare_folder entities referencing
     * this entity by owner_id=>person_id
     *
     * @return  de.schlund.db.methadon.Fileshare_folder[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getFileshare_folderOwnerList() {
      return XPClass::forName('de.schlund.db.methadon.Fileshare_folder')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('owner_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Fileshare_folder entities referencing
     * this entity by owner_id=>person_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Fileshare_folder>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getFileshare_folderOwnerIterator() {
      return XPClass::forName('de.schlund.db.methadon.Fileshare_folder')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('owner_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Pim_contact entities referencing
     * this entity by person_id=>person_id
     *
     * @return  de.schlund.db.methadon.Pim_contact[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getPim_contactPersonList() {
      return XPClass::forName('de.schlund.db.methadon.Pim_contact')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('person_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Pim_contact entities referencing
     * this entity by person_id=>person_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Pim_contact>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getPim_contactPersonIterator() {
      return XPClass::forName('de.schlund.db.methadon.Pim_contact')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('person_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Ebay_ad entities referencing
     * this entity by person_id=>person_id
     *
     * @return  de.schlund.db.methadon.Ebay_ad[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEbay_adPersonList() {
      return XPClass::forName('de.schlund.db.methadon.Ebay_ad')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('person_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Ebay_ad entities referencing
     * this entity by person_id=>person_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Ebay_ad>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEbay_adPersonIterator() {
      return XPClass::forName('de.schlund.db.methadon.Ebay_ad')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('person_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Fileshare_file_version entities referencing
     * this entity by owner_id=>person_id
     *
     * @return  de.schlund.db.methadon.Fileshare_file_version[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getFileshare_file_versionOwnerList() {
      return XPClass::forName('de.schlund.db.methadon.Fileshare_file_version')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('owner_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Fileshare_file_version entities referencing
     * this entity by owner_id=>person_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Fileshare_file_version>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getFileshare_file_versionOwnerIterator() {
      return XPClass::forName('de.schlund.db.methadon.Fileshare_file_version')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('owner_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Pim_appointment entities referencing
     * this entity by person_id=>person_id
     *
     * @return  de.schlund.db.methadon.Pim_appointment[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getPim_appointmentPersonList() {
      return XPClass::forName('de.schlund.db.methadon.Pim_appointment')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('person_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Pim_appointment entities referencing
     * this entity by person_id=>person_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Pim_appointment>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getPim_appointmentPersonIterator() {
      return XPClass::forName('de.schlund.db.methadon.Pim_appointment')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('person_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Mailforwarding entities referencing
     * this entity by releaser_id=>person_id
     *
     * @return  de.schlund.db.methadon.Mailforwarding[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getMailforwardingReleaserList() {
      return XPClass::forName('de.schlund.db.methadon.Mailforwarding')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('releaser_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Mailforwarding entities referencing
     * this entity by releaser_id=>person_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Mailforwarding>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getMailforwardingReleaserIterator() {
      return XPClass::forName('de.schlund.db.methadon.Mailforwarding')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('releaser_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Mailforwarding entities referencing
     * this entity by requester_id=>person_id
     *
     * @return  de.schlund.db.methadon.Mailforwarding[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getMailforwardingRequesterList() {
      return XPClass::forName('de.schlund.db.methadon.Mailforwarding')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('requester_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Mailforwarding entities referencing
     * this entity by requester_id=>person_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Mailforwarding>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getMailforwardingRequesterIterator() {
      return XPClass::forName('de.schlund.db.methadon.Mailforwarding')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('requester_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Project entities referencing
     * this entity by maintainer_id=>person_id
     *
     * @return  de.schlund.db.methadon.Project[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getProjectMaintainerList() {
      return XPClass::forName('de.schlund.db.methadon.Project')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('maintainer_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Project entities referencing
     * this entity by maintainer_id=>person_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Project>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getProjectMaintainerIterator() {
      return XPClass::forName('de.schlund.db.methadon.Project')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('maintainer_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Fileshare_folder_acl entities referencing
     * this entity by person_id=>person_id
     *
     * @return  de.schlund.db.methadon.Fileshare_folder_acl[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getFileshare_folder_aclPersonList() {
      return XPClass::forName('de.schlund.db.methadon.Fileshare_folder_acl')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('person_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Fileshare_folder_acl entities referencing
     * this entity by person_id=>person_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Fileshare_folder_acl>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getFileshare_folder_aclPersonIterator() {
      return XPClass::forName('de.schlund.db.methadon.Fileshare_folder_acl')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('person_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Plane_master_matrix entities referencing
     * this entity by slave_id=>person_id
     *
     * @return  de.schlund.db.methadon.Plane_master_matrix[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getPlane_master_matrixSlaveList() {
      return XPClass::forName('de.schlund.db.methadon.Plane_master_matrix')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('slave_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Plane_master_matrix entities referencing
     * this entity by slave_id=>person_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Plane_master_matrix>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getPlane_master_matrixSlaveIterator() {
      return XPClass::forName('de.schlund.db.methadon.Plane_master_matrix')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('slave_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Plane_master_matrix entities referencing
     * this entity by master_id=>person_id
     *
     * @return  de.schlund.db.methadon.Plane_master_matrix[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getPlane_master_matrixMasterList() {
      return XPClass::forName('de.schlund.db.methadon.Plane_master_matrix')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('master_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Plane_master_matrix entities referencing
     * this entity by master_id=>person_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Plane_master_matrix>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getPlane_master_matrixMasterIterator() {
      return XPClass::forName('de.schlund.db.methadon.Plane_master_matrix')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('master_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Person_property entities referencing
     * this entity by person_id=>person_id
     *
     * @return  de.schlund.db.methadon.Person_property[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getPerson_propertyPersonList() {
      return XPClass::forName('de.schlund.db.methadon.Person_property')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('person_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Person_property entities referencing
     * this entity by person_id=>person_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Person_property>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getPerson_propertyPersonIterator() {
      return XPClass::forName('de.schlund.db.methadon.Person_property')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('person_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Person_request_category entities referencing
     * this entity by person_id=>person_id
     *
     * @return  de.schlund.db.methadon.Person_request_category[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getPerson_request_categoryPersonList() {
      return XPClass::forName('de.schlund.db.methadon.Person_request_category')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('person_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Person_request_category entities referencing
     * this entity by person_id=>person_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Person_request_category>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getPerson_request_categoryPersonIterator() {
      return XPClass::forName('de.schlund.db.methadon.Person_request_category')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('person_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Event_template entities referencing
     * this entity by person_id=>person_id
     *
     * @return  de.schlund.db.methadon.Event_template[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEvent_templatePersonList() {
      return XPClass::forName('de.schlund.db.methadon.Event_template')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('person_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Event_template entities referencing
     * this entity by person_id=>person_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Event_template>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEvent_templatePersonIterator() {
      return XPClass::forName('de.schlund.db.methadon.Event_template')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('person_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Resource_allocation entities referencing
     * this entity by person_id=>person_id
     *
     * @return  de.schlund.db.methadon.Resource_allocation[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getResource_allocationPersonList() {
      return XPClass::forName('de.schlund.db.methadon.Resource_allocation')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('person_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Resource_allocation entities referencing
     * this entity by person_id=>person_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Resource_allocation>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getResource_allocationPersonIterator() {
      return XPClass::forName('de.schlund.db.methadon.Resource_allocation')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('person_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Requested_item entities referencing
     * this entity by master_id=>person_id
     *
     * @return  de.schlund.db.methadon.Requested_item[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getRequested_itemMasterList() {
      return XPClass::forName('de.schlund.db.methadon.Requested_item')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('master_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Requested_item entities referencing
     * this entity by master_id=>person_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Requested_item>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getRequested_itemMasterIterator() {
      return XPClass::forName('de.schlund.db.methadon.Requested_item')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('master_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Requested_item entities referencing
     * this entity by person_id=>person_id
     *
     * @return  de.schlund.db.methadon.Requested_item[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getRequested_itemPersonList() {
      return XPClass::forName('de.schlund.db.methadon.Requested_item')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('person_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Requested_item entities referencing
     * this entity by person_id=>person_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Requested_item>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getRequested_itemPersonIterator() {
      return XPClass::forName('de.schlund.db.methadon.Requested_item')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('person_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Bug entities referencing
     * this entity by reporter_id=>person_id
     *
     * @return  de.schlund.db.methadon.Bug[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getBugReporterList() {
      return XPClass::forName('de.schlund.db.methadon.Bug')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('reporter_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Bug entities referencing
     * this entity by reporter_id=>person_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Bug>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getBugReporterIterator() {
      return XPClass::forName('de.schlund.db.methadon.Bug')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('reporter_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Right_todo entities referencing
     * this entity by person_id=>person_id
     *
     * @return  de.schlund.db.methadon.Right_todo[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getRight_todoPersonList() {
      return XPClass::forName('de.schlund.db.methadon.Right_todo')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('person_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Right_todo entities referencing
     * this entity by person_id=>person_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Right_todo>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getRight_todoPersonIterator() {
      return XPClass::forName('de.schlund.db.methadon.Right_todo')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('person_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Bug_channel entities referencing
     * this entity by owner_id=>person_id
     *
     * @return  de.schlund.db.methadon.Bug_channel[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getBug_channelOwnerList() {
      return XPClass::forName('de.schlund.db.methadon.Bug_channel')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('owner_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Bug_channel entities referencing
     * this entity by owner_id=>person_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Bug_channel>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getBug_channelOwnerIterator() {
      return XPClass::forName('de.schlund.db.methadon.Bug_channel')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('owner_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all News entities referencing
     * this entity by owner_id=>person_id
     *
     * @return  de.schlund.db.methadon.News[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getNewsOwnerList() {
      return XPClass::forName('de.schlund.db.methadon.News')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('owner_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all News entities referencing
     * this entity by owner_id=>person_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.News>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getNewsOwnerIterator() {
      return XPClass::forName('de.schlund.db.methadon.News')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('owner_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Bug_history entities referencing
     * this entity by owner_id=>person_id
     *
     * @return  de.schlund.db.methadon.Bug_history[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getBug_historyOwnerList() {
      return XPClass::forName('de.schlund.db.methadon.Bug_history')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('owner_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Bug_history entities referencing
     * this entity by owner_id=>person_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Bug_history>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getBug_historyOwnerIterator() {
      return XPClass::forName('de.schlund.db.methadon.Bug_history')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('owner_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Bug_history entities referencing
     * this entity by owner_id=>person_id
     *
     * @return  de.schlund.db.methadon.Bug_history[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getBug_historyOwnerList() {
      return XPClass::forName('de.schlund.db.methadon.Bug_history')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('owner_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Bug_history entities referencing
     * this entity by owner_id=>person_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Bug_history>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getBug_historyOwnerIterator() {
      return XPClass::forName('de.schlund.db.methadon.Bug_history')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('owner_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Bug_history entities referencing
     * this entity by reporter_id=>person_id
     *
     * @return  de.schlund.db.methadon.Bug_history[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getBug_historyReporterList() {
      return XPClass::forName('de.schlund.db.methadon.Bug_history')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('reporter_id', $this->getPerson_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Bug_history entities referencing
     * this entity by reporter_id=>person_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Bug_history>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getBug_historyReporterIterator() {
      return XPClass::forName('de.schlund.db.methadon.Bug_history')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('reporter_id', $this->getPerson_id(), EQUAL)
      ));
    }
  }
?>