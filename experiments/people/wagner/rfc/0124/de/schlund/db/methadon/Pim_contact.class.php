<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet');

  /**
   * Class wrapper for table pim_contact, database METHADON
   * (Auto-generated on Wed, 04 Apr 2007 10:45:27 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class Pim_contact extends DataSet {

    protected
      $_isLoaded= false,
      $_loadCrit= NULL;

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('METHADON..pim_contact');
        $peer->setConnection('sybintern');
        $peer->setIdentity('contact_id');
        $peer->setPrimary(array('contact_id'));
        $peer->setTypes(array(
          'lastname'            => array('%s', FieldType::VARCHAR, FALSE),
          'firstname'           => array('%s', FieldType::VARCHAR, TRUE),
          'title'               => array('%s', FieldType::VARCHAR, TRUE),
          'sex'                 => array('%d', FieldType::INT, FALSE),
          'contact_id'          => array('%d', FieldType::NUMERIC, FALSE),
          'person_id'           => array('%d', FieldType::NUMERIC, FALSE)
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
     * Gets an instance of this object by index "PK_CONTACT"
     * 
     * @param   int contact_id
     * @return  de.schlund.db.methadon.Pim_contact entitiy object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByContact_id($contact_id) {
      return new self(array(
        'contact_id'  => $contact_id,
        '_loadCrit' => new Criteria(array('contact_id', $contact_id, EQUAL))
      ));
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
     * Retrieves title
     *
     * @return  string
     */
    public function getTitle() {
      return $this->title;
    }
      
    /**
     * Sets title
     *
     * @param   string title
     * @return  string the previous value
     */
    public function setTitle($title) {
      return $this->_change('title', $title);
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
     * Retrieves contact_id
     *
     * @return  int
     */
    public function getContact_id() {
      return $this->contact_id;
    }
      
    /**
     * Sets contact_id
     *
     * @param   int contact_id
     * @return  int the previous value
     */
    public function setContact_id($contact_id) {
      return $this->_change('contact_id', $contact_id);
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
     * Retrieves an array of all Pim_address entities referencing
     * this entity by contact_id=>contact_id
     *
     * @return  de.schlund.db.methadon.Pim_address[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getPim_addressContactList() {
      return XPClass::forName('de.schlund.db.methadon.Pim_address')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('contact_id', $this->getContact_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Pim_address entities referencing
     * this entity by contact_id=>contact_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Pim_address>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getPim_addressContactIterator() {
      return XPClass::forName('de.schlund.db.methadon.Pim_address')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('contact_id', $this->getContact_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Pim_email entities referencing
     * this entity by contact_id=>contact_id
     *
     * @return  de.schlund.db.methadon.Pim_email[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getPim_emailContactList() {
      return XPClass::forName('de.schlund.db.methadon.Pim_email')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('contact_id', $this->getContact_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Pim_email entities referencing
     * this entity by contact_id=>contact_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Pim_email>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getPim_emailContactIterator() {
      return XPClass::forName('de.schlund.db.methadon.Pim_email')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('contact_id', $this->getContact_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Pim_phone entities referencing
     * this entity by contact_id=>contact_id
     *
     * @return  de.schlund.db.methadon.Pim_phone[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getPim_phoneContactList() {
      return XPClass::forName('de.schlund.db.methadon.Pim_phone')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('contact_id', $this->getContact_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Pim_phone entities referencing
     * this entity by contact_id=>contact_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Pim_phone>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getPim_phoneContactIterator() {
      return XPClass::forName('de.schlund.db.methadon.Pim_phone')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('contact_id', $this->getContact_id(), EQUAL)
      ));
    }
  }
?>