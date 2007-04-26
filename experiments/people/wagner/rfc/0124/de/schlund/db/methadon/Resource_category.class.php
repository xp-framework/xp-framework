<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet');

  /**
   * Class wrapper for table resource_category, database METHADON
   * (Auto-generated on Wed, 04 Apr 2007 10:45:27 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class Resource_category extends DataSet {

    protected
      $_isLoaded= false,
      $_loadCrit= NULL;

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('METHADON..resource_category');
        $peer->setConnection('sybintern');
        $peer->setIdentity('resource_category_id');
        $peer->setPrimary(array('resource_category_id'));
        $peer->setTypes(array(
          'name'                => array('%s', FieldType::VARCHAR, FALSE),
          'changedby'           => array('%s', FieldType::VARCHAR, FALSE),
          'resource_category_id' => array('%d', FieldType::NUMERIC, FALSE),
          'location_id'         => array('%d', FieldType::NUMERIC, FALSE),
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
     * Gets an instance of this object by index "resource_c_resour_8292429781"
     * 
     * @param   int resource_category_id
     * @return  de.schlund.db.methadon.Resource_category entitiy object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByResource_category_id($resource_category_id) {
      return new self(array(
        'resource_category_id'  => $resource_category_id,
        '_loadCrit' => new Criteria(array('resource_category_id', $resource_category_id, EQUAL))
      ));
    }

    /**
     * Gets an instance of this object by index "i_rescat_location_table"
     * 
     * @param   int location_id
     * @return  de.schlund.db.methadon.Resource_category[] entity objects
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByLocation_id($location_id) {
      $r= self::getPeer()->doSelect(new Criteria(array('location_id', $location_id, EQUAL)));
      foreach ($r as $e) $e->_isLoaded= true;
      return $r;
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
     * Retrieves resource_category_id
     *
     * @return  int
     */
    public function getResource_category_id() {
      return $this->resource_category_id;
    }
      
    /**
     * Sets resource_category_id
     *
     * @param   int resource_category_id
     * @return  int the previous value
     */
    public function setResource_category_id($resource_category_id) {
      return $this->_change('resource_category_id', $resource_category_id);
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
     * Retrieves the Location entity
     * referenced by location_id=>location_id
     *
     * @return  de.schlund.db.methadon.Location entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getLocation() {
      $r= XPClass::forName('de.schlund.db.methadon.Location')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('location_id', $this->getLocation_id(), EQUAL)
      ));
      return $r ? $r[0] : NULL;
    }

    /**
     * Retrieves an array of all Person_resource_category entities referencing
     * this entity by resource_category_id=>resource_category_id
     *
     * @return  de.schlund.db.methadon.Person_resource_category[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getPerson_resource_categoryResource_categoryList() {
      return XPClass::forName('de.schlund.db.methadon.Person_resource_category')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('resource_category_id', $this->getResource_category_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Person_resource_category entities referencing
     * this entity by resource_category_id=>resource_category_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Person_resource_category>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getPerson_resource_categoryResource_categoryIterator() {
      return XPClass::forName('de.schlund.db.methadon.Person_resource_category')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('resource_category_id', $this->getResource_category_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Resourceng entities referencing
     * this entity by resource_category_id=>resource_category_id
     *
     * @return  de.schlund.db.methadon.Resourceng[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getResourcengResource_categoryList() {
      return XPClass::forName('de.schlund.db.methadon.Resourceng')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('resource_category_id', $this->getResource_category_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Resourceng entities referencing
     * this entity by resource_category_id=>resource_category_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Resourceng>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getResourcengResource_categoryIterator() {
      return XPClass::forName('de.schlund.db.methadon.Resourceng')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('resource_category_id', $this->getResource_category_id(), EQUAL)
      ));
    }
  }
?>