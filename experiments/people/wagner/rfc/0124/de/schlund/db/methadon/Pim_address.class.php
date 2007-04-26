<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet');

  /**
   * Class wrapper for table pim_address, database METHADON
   * (Auto-generated on Wed, 04 Apr 2007 10:45:27 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class Pim_address extends DataSet {

    protected
      $_isLoaded= false,
      $_loadCrit= NULL;

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('METHADON..pim_address');
        $peer->setConnection('sybintern');
        $peer->setPrimary(array('address_id'));
        $peer->setTypes(array(
          'street1'             => array('%s', FieldType::VARCHAR, TRUE),
          'street2'             => array('%s', FieldType::VARCHAR, TRUE),
          'city'                => array('%s', FieldType::VARCHAR, TRUE),
          'state'               => array('%s', FieldType::VARCHAR, TRUE),
          'zip'                 => array('%s', FieldType::VARCHAR, TRUE),
          'address_id'          => array('%d', FieldType::NUMERIC, FALSE),
          'contact_id'          => array('%d', FieldType::NUMERIC, FALSE),
          'country_id'          => array('%d', FieldType::NUMERICN, TRUE)
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
     * Gets an instance of this object by index "PK_ADDRESS"
     * 
     * @param   int address_id
     * @return  de.schlund.db.methadon.Pim_address entitiy object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByAddress_id($address_id) {
      return new self(array(
        'address_id'  => $address_id,
        '_loadCrit' => new Criteria(array('address_id', $address_id, EQUAL))
      ));
    }

    /**
     * Retrieves street1
     *
     * @return  string
     */
    public function getStreet1() {
      return $this->street1;
    }
      
    /**
     * Sets street1
     *
     * @param   string street1
     * @return  string the previous value
     */
    public function setStreet1($street1) {
      return $this->_change('street1', $street1);
    }

    /**
     * Retrieves street2
     *
     * @return  string
     */
    public function getStreet2() {
      return $this->street2;
    }
      
    /**
     * Sets street2
     *
     * @param   string street2
     * @return  string the previous value
     */
    public function setStreet2($street2) {
      return $this->_change('street2', $street2);
    }

    /**
     * Retrieves city
     *
     * @return  string
     */
    public function getCity() {
      return $this->city;
    }
      
    /**
     * Sets city
     *
     * @param   string city
     * @return  string the previous value
     */
    public function setCity($city) {
      return $this->_change('city', $city);
    }

    /**
     * Retrieves state
     *
     * @return  string
     */
    public function getState() {
      return $this->state;
    }
      
    /**
     * Sets state
     *
     * @param   string state
     * @return  string the previous value
     */
    public function setState($state) {
      return $this->_change('state', $state);
    }

    /**
     * Retrieves zip
     *
     * @return  string
     */
    public function getZip() {
      return $this->zip;
    }
      
    /**
     * Sets zip
     *
     * @param   string zip
     * @return  string the previous value
     */
    public function setZip($zip) {
      return $this->_change('zip', $zip);
    }

    /**
     * Retrieves address_id
     *
     * @return  int
     */
    public function getAddress_id() {
      return $this->address_id;
    }
      
    /**
     * Sets address_id
     *
     * @param   int address_id
     * @return  int the previous value
     */
    public function setAddress_id($address_id) {
      return $this->_change('address_id', $address_id);
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
     * Retrieves country_id
     *
     * @return  int
     */
    public function getCountry_id() {
      return $this->country_id;
    }
      
    /**
     * Sets country_id
     *
     * @param   int country_id
     * @return  int the previous value
     */
    public function setCountry_id($country_id) {
      return $this->_change('country_id', $country_id);
    }

    /**
     * Retrieves the Pim_contact entity
     * referenced by contact_id=>contact_id
     *
     * @return  de.schlund.db.methadon.Pim_contact entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getContact() {
      $r= XPClass::forName('de.schlund.db.methadon.Pim_contact')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('contact_id', $this->getContact_id(), EQUAL)
      ));
      return $r ? $r[0] : NULL;
    }
  }
?>