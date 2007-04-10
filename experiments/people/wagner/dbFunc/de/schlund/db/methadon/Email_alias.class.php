<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet');

  /**
   * Class wrapper for table email_alias, database METHADON
   * (Auto-generated on Wed, 04 Apr 2007 10:45:27 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class Email_alias extends DataSet {

    protected
      $_isLoaded= false,
      $_loadCrit= NULL;

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('METHADON..email_alias');
        $peer->setConnection('sybintern');
        $peer->setPrimary(array('address'));
        $peer->setTypes(array(
          'address'             => array('%s', FieldType::VARCHAR, FALSE),
          'changedby'           => array('%s', FieldType::VARCHAR, FALSE),
          'email_id'            => array('%d', FieldType::NUMERIC, FALSE),
          'creator_id'          => array('%d', FieldType::NUMERIC, FALSE),
          'bz_id'               => array('%d', FieldType::NUMERIC, FALSE),
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
     * Gets an instance of this object by index "email_alia_addres_4668136941"
     * 
     * @param   string address
     * @return  de.schlund.db.methadon.Email_alias entitiy object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByAddress($address) {
      return new self(array(
        'address'  => $address,
        '_loadCrit' => new Criteria(array('address', $address, EQUAL))
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
     * Retrieves the Email entity
     * referenced by email_id=>email_id
     *
     * @return  de.schlund.db.methadon.Email entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEmail() {
      $r= XPClass::forName('de.schlund.db.methadon.Email')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('email_id', $this->getEmail_id(), EQUAL)
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
  }
?>