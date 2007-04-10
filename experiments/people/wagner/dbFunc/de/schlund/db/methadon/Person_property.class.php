<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet');

  /**
   * Class wrapper for table person_property, database METHADON
   * (Auto-generated on Wed, 04 Apr 2007 10:45:27 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class Person_property extends DataSet {

    protected
      $_isLoaded= false,
      $_loadCrit= NULL;

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('METHADON..person_property');
        $peer->setConnection('sybintern');
        $peer->setPrimary(array('person_id', 'property_type_id', 'name'));
        $peer->setTypes(array(
          'name'                => array('%s', FieldType::VARCHAR, FALSE),
          'changedby'           => array('%s', FieldType::VARCHAR, FALSE),
          'person_id'           => array('%d', FieldType::NUMERIC, FALSE),
          'property_type_id'    => array('%d', FieldType::NUMERIC, FALSE),
          'bz_id'               => array('%d', FieldType::NUMERIC, FALSE),
          'lastchange'          => array('%s', FieldType::DATETIME, FALSE),
          'value'               => array('%s', FieldType::TEXT, TRUE)
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
     * Gets an instance of this object by index "PK_PERSON_PROPERTY"
     * 
     * @param   int person_id
     * @param   int property_type_id
     * @param   string name
     * @return  de.schlund.db.methadon.Person_property entitiy object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByPerson_idProperty_type_idName($person_id, $property_type_id, $name) {
      return new self(array(
        'person_id'  => $person_id,
        'property_type_id'  => $property_type_id,
        'name'  => $name,
        '_loadCrit' => new Criteria(
          array('person_id', $person_id, EQUAL),
          array('property_type_id', $property_type_id, EQUAL),
          array('name', $name, EQUAL)
        )
      ));
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
     * Retrieves property_type_id
     *
     * @return  int
     */
    public function getProperty_type_id() {
      return $this->property_type_id;
    }
      
    /**
     * Sets property_type_id
     *
     * @param   int property_type_id
     * @return  int the previous value
     */
    public function setProperty_type_id($property_type_id) {
      return $this->_change('property_type_id', $property_type_id);
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
     * Retrieves value
     *
     * @return  string
     */
    public function getValue() {
      return $this->value;
    }
      
    /**
     * Sets value
     *
     * @param   string value
     * @return  string the previous value
     */
    public function setValue($value) {
      return $this->_change('value', $value);
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
     * Retrieves the Property_type entity
     * referenced by property_type_id=>property_type_id
     *
     * @return  de.schlund.db.methadon.Property_type entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getProperty_type() {
      $r= XPClass::forName('de.schlund.db.methadon.Property_type')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('property_type_id', $this->getProperty_type_id(), EQUAL)
      ));
      return $r ? $r[0] : NULL;
    }
  }
?>