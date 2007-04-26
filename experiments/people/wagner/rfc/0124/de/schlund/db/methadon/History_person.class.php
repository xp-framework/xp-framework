<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet');

  /**
   * Class wrapper for table history_person, database METHADON
   * (Auto-generated on Wed, 04 Apr 2007 10:45:27 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class History_person extends DataSet {

    protected
      $_isLoaded= false,
      $_loadCrit= NULL;

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('METHADON..history_person');
        $peer->setConnection('sybintern');
        $peer->setIdentity('history_id');
        $peer->setPrimary(array('history_id'));
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
          'changedby2'          => array('%s', FieldType::VARCHAR, FALSE),
          'sex'                 => array('%d', FieldType::INT, FALSE),
          'history_id'          => array('%d', FieldType::NUMERIC, FALSE),
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
     * Gets an instance of this object by index "PK_PERSON_HISTORY"
     * 
     * @param   int history_id
     * @return  de.schlund.db.methadon.History_person entitiy object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByHistory_id($history_id) {
      return new self(array(
        'history_id'  => $history_id,
        '_loadCrit' => new Criteria(array('history_id', $history_id, EQUAL))
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
     * Retrieves changedby2
     *
     * @return  string
     */
    public function getChangedby2() {
      return $this->changedby2;
    }
      
    /**
     * Sets changedby2
     *
     * @param   string changedby2
     * @return  string the previous value
     */
    public function setChangedby2($changedby2) {
      return $this->_change('changedby2', $changedby2);
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
     * Retrieves history_id
     *
     * @return  int
     */
    public function getHistory_id() {
      return $this->history_id;
    }
      
    /**
     * Sets history_id
     *
     * @param   int history_id
     * @return  int the previous value
     */
    public function setHistory_id($history_id) {
      return $this->_change('history_id', $history_id);
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
  }
?>