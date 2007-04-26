<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet');

  /**
   * Class wrapper for table pim_phone, database METHADON
   * (Auto-generated on Wed, 04 Apr 2007 10:45:27 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class Pim_phone extends DataSet {

    protected
      $_isLoaded= false,
      $_loadCrit= NULL;

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('METHADON..pim_phone');
        $peer->setConnection('sybintern');
        $peer->setIdentity('phone_id');
        $peer->setPrimary(array('phone_id'));
        $peer->setTypes(array(
          'phone_no'            => array('%s', FieldType::VARCHAR, FALSE),
          'description'         => array('%s', FieldType::VARCHAR, TRUE),
          'phone_id'            => array('%d', FieldType::NUMERIC, FALSE),
          'contact_id'          => array('%d', FieldType::NUMERIC, FALSE)
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
     * Gets an instance of this object by index "PK_PHONE"
     * 
     * @param   int phone_id
     * @return  de.schlund.db.methadon.Pim_phone entitiy object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByPhone_id($phone_id) {
      return new self(array(
        'phone_id'  => $phone_id,
        '_loadCrit' => new Criteria(array('phone_id', $phone_id, EQUAL))
      ));
    }

    /**
     * Retrieves phone_no
     *
     * @return  string
     */
    public function getPhone_no() {
      return $this->phone_no;
    }
      
    /**
     * Sets phone_no
     *
     * @param   string phone_no
     * @return  string the previous value
     */
    public function setPhone_no($phone_no) {
      return $this->_change('phone_no', $phone_no);
    }

    /**
     * Retrieves description
     *
     * @return  string
     */
    public function getDescription() {
      return $this->description;
    }
      
    /**
     * Sets description
     *
     * @param   string description
     * @return  string the previous value
     */
    public function setDescription($description) {
      return $this->_change('description', $description);
    }

    /**
     * Retrieves phone_id
     *
     * @return  int
     */
    public function getPhone_id() {
      return $this->phone_id;
    }
      
    /**
     * Sets phone_id
     *
     * @param   int phone_id
     * @return  int the previous value
     */
    public function setPhone_id($phone_id) {
      return $this->_change('phone_id', $phone_id);
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