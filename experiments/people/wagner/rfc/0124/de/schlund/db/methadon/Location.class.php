<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet');

  /**
   * Class wrapper for table location, database METHADON
   * (Auto-generated on Wed, 04 Apr 2007 10:45:27 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class Location extends DataSet {

    protected
      $_isLoaded= false,
      $_loadCrit= NULL;

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('METHADON..location');
        $peer->setConnection('sybintern');
        $peer->setPrimary(array('location_id'));
        $peer->setTypes(array(
          'name'                => array('%s', FieldType::VARCHAR, FALSE),
          'description'         => array('%s', FieldType::VARCHAR, TRUE),
          'countrycode'         => array('%s', FieldType::VARCHAR, TRUE),
          'timezone'            => array('%s', FieldType::VARCHAR, TRUE),
          'location_id'         => array('%d', FieldType::NUMERIC, FALSE)
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
     * Gets an instance of this object by index "PK_LOCATION"
     * 
     * @param   int location_id
     * @return  de.schlund.db.methadon.Location entitiy object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByLocation_id($location_id) {
      return new self(array(
        'location_id'  => $location_id,
        '_loadCrit' => new Criteria(array('location_id', $location_id, EQUAL))
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
     * Retrieves countrycode
     *
     * @return  string
     */
    public function getCountrycode() {
      return $this->countrycode;
    }
      
    /**
     * Sets countrycode
     *
     * @param   string countrycode
     * @return  string the previous value
     */
    public function setCountrycode($countrycode) {
      return $this->_change('countrycode', $countrycode);
    }

    /**
     * Retrieves timezone
     *
     * @return  string
     */
    public function getTimezone() {
      return $this->timezone;
    }
      
    /**
     * Sets timezone
     *
     * @param   string timezone
     * @return  string the previous value
     */
    public function setTimezone($timezone) {
      return $this->_change('timezone', $timezone);
    }

    /**
     * Retrieves location_id
     *
     * @return  int
     */
    public function getLocation_id() {
      return $this->location_id;
    }
      
    /**
     * Sets location_id
     *
     * @param   int location_id
     * @return  int the previous value
     */
    public function setLocation_id($location_id) {
      return $this->_change('location_id', $location_id);
    }

    /**
     * Retrieves the Person entity
     * referenced by person_id=>location_id
     *
     * @return  de.schlund.db.methadon.Person entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getLocation() {
      $r= XPClass::forName('de.schlund.db.methadon.Person')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('person_id', $this->getLocation_id(), EQUAL)
      ));
      return $r ? $r[0] : NULL;
    }

    /**
     * Retrieves an array of all Eventng entities referencing
     * this entity by location_id=>location_id
     *
     * @return  de.schlund.db.methadon.Eventng[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEventngLocationList() {
      return XPClass::forName('de.schlund.db.methadon.Eventng')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('location_id', $this->getLocation_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Eventng entities referencing
     * this entity by location_id=>location_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Eventng>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEventngLocationIterator() {
      return XPClass::forName('de.schlund.db.methadon.Eventng')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('location_id', $this->getLocation_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Employee entities referencing
     * this entity by location_id=>location_id
     *
     * @return  de.schlund.db.methadon.Employee[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEmployeeLocationList() {
      return XPClass::forName('de.schlund.db.methadon.Employee')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('location_id', $this->getLocation_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Employee entities referencing
     * this entity by location_id=>location_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Employee>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEmployeeLocationIterator() {
      return XPClass::forName('de.schlund.db.methadon.Employee')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('location_id', $this->getLocation_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Resource_category entities referencing
     * this entity by location_id=>location_id
     *
     * @return  de.schlund.db.methadon.Resource_category[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getResource_categoryLocationList() {
      return XPClass::forName('de.schlund.db.methadon.Resource_category')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('location_id', $this->getLocation_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Resource_category entities referencing
     * this entity by location_id=>location_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Resource_category>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getResource_categoryLocationIterator() {
      return XPClass::forName('de.schlund.db.methadon.Resource_category')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('location_id', $this->getLocation_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Ebay_ad entities referencing
     * this entity by location_id=>location_id
     *
     * @return  de.schlund.db.methadon.Ebay_ad[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEbay_adLocationList() {
      return XPClass::forName('de.schlund.db.methadon.Ebay_ad')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('location_id', $this->getLocation_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Ebay_ad entities referencing
     * this entity by location_id=>location_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Ebay_ad>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEbay_adLocationIterator() {
      return XPClass::forName('de.schlund.db.methadon.Ebay_ad')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('location_id', $this->getLocation_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Request_category entities referencing
     * this entity by loc_id=>location_id
     *
     * @return  de.schlund.db.methadon.Request_category[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getRequest_categoryLocList() {
      return XPClass::forName('de.schlund.db.methadon.Request_category')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('loc_id', $this->getLocation_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Request_category entities referencing
     * this entity by loc_id=>location_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Request_category>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getRequest_categoryLocIterator() {
      return XPClass::forName('de.schlund.db.methadon.Request_category')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('loc_id', $this->getLocation_id(), EQUAL)
      ));
    }
  }
?>