<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet');

  /**
   * Class wrapper for table resourceng, database METHADON
   * (Auto-generated on Wed, 04 Apr 2007 10:45:27 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class Resourceng extends DataSet {

    protected
      $_isLoaded= false,
      $_loadCrit= NULL;

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('METHADON..resourceng');
        $peer->setConnection('sybintern');
        $peer->setIdentity('resourceng_id');
        $peer->setPrimary(array('resourceng_id'));
        $peer->setTypes(array(
          'name'                => array('%s', FieldType::VARCHAR, FALSE),
          'description'         => array('%s', FieldType::VARCHAR, FALSE),
          'changedby'           => array('%s', FieldType::VARCHAR, FALSE),
          'resourceng_id'       => array('%d', FieldType::NUMERIC, FALSE),
          'resource_category_id' => array('%d', FieldType::NUMERIC, FALSE),
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
     * Gets an instance of this object by index "resourceng_resour_1085243890"
     * 
     * @param   int resourceng_id
     * @return  de.schlund.db.methadon.Resourceng entitiy object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByResourceng_id($resourceng_id) {
      return new self(array(
        'resourceng_id'  => $resourceng_id,
        '_loadCrit' => new Criteria(array('resourceng_id', $resourceng_id, EQUAL))
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
     * Retrieves resourceng_id
     *
     * @return  int
     */
    public function getResourceng_id() {
      return $this->resourceng_id;
    }
      
    /**
     * Sets resourceng_id
     *
     * @param   int resourceng_id
     * @return  int the previous value
     */
    public function setResourceng_id($resourceng_id) {
      return $this->_change('resourceng_id', $resourceng_id);
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
     * Retrieves the Resource_category entity
     * referenced by resource_category_id=>resource_category_id
     *
     * @return  de.schlund.db.methadon.Resource_category entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getResource_category() {
      $r= XPClass::forName('de.schlund.db.methadon.Resource_category')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('resource_category_id', $this->getResource_category_id(), EQUAL)
      ));
      return $r ? $r[0] : NULL;
    }

    /**
     * Retrieves an array of all Resource_reservation entities referencing
     * this entity by resourceng_id=>resourceng_id
     *
     * @return  de.schlund.db.methadon.Resource_reservation[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getResource_reservationResourcengList() {
      return XPClass::forName('de.schlund.db.methadon.Resource_reservation')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('resourceng_id', $this->getResourceng_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Resource_reservation entities referencing
     * this entity by resourceng_id=>resourceng_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Resource_reservation>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getResource_reservationResourcengIterator() {
      return XPClass::forName('de.schlund.db.methadon.Resource_reservation')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('resourceng_id', $this->getResourceng_id(), EQUAL)
      ));
    }
  }
?>