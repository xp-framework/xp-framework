<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet');

  /**
   * Class wrapper for table resource, database METHADON
   * (Auto-generated on Wed, 04 Apr 2007 10:45:27 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class Resource extends DataSet {

    protected
      $_isLoaded= false,
      $_loadCrit= NULL;

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('METHADON..resource');
        $peer->setConnection('sybintern');
        $peer->setIdentity('resource_id');
        $peer->setPrimary(array('resource_id'));
        $peer->setTypes(array(
          'name'                => array('%s', FieldType::VARCHAR, FALSE),
          'description'         => array('%s', FieldType::VARCHAR, TRUE),
          'document_name'       => array('%s', FieldType::VARCHAR, TRUE),
          'changedby'           => array('%s', FieldType::VARCHAR, FALSE),
          'feature'             => array('%d', FieldType::INT, FALSE),
          'resource_id'         => array('%d', FieldType::NUMERIC, FALSE),
          'resource_type_id'    => array('%d', FieldType::NUMERIC, FALSE),
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
     * Gets an instance of this object by index "resource_I0"
     * 
     * @param   int resource_type_id
     * @return  de.schlund.db.methadon.Resource[] entity objects
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByResource_type_id($resource_type_id) {
      $r= self::getPeer()->doSelect(new Criteria(array('resource_type_id', $resource_type_id, EQUAL)));
      foreach ($r as $e) $e->_isLoaded= true;
      return $r;
    }

    /**
     * Gets an instance of this object by index "PK_RES"
     * 
     * @param   int resource_id
     * @return  de.schlund.db.methadon.Resource entitiy object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByResource_id($resource_id) {
      return new self(array(
        'resource_id'  => $resource_id,
        '_loadCrit' => new Criteria(array('resource_id', $resource_id, EQUAL))
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
     * Retrieves document_name
     *
     * @return  string
     */
    public function getDocument_name() {
      return $this->document_name;
    }
      
    /**
     * Sets document_name
     *
     * @param   string document_name
     * @return  string the previous value
     */
    public function setDocument_name($document_name) {
      return $this->_change('document_name', $document_name);
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
     * Retrieves feature
     *
     * @return  int
     */
    public function getFeature() {
      return $this->feature;
    }
      
    /**
     * Sets feature
     *
     * @param   int feature
     * @return  int the previous value
     */
    public function setFeature($feature) {
      return $this->_change('feature', $feature);
    }

    /**
     * Retrieves resource_id
     *
     * @return  int
     */
    public function getResource_id() {
      return $this->resource_id;
    }
      
    /**
     * Sets resource_id
     *
     * @param   int resource_id
     * @return  int the previous value
     */
    public function setResource_id($resource_id) {
      return $this->_change('resource_id', $resource_id);
    }

    /**
     * Retrieves resource_type_id
     *
     * @return  int
     */
    public function getResource_type_id() {
      return $this->resource_type_id;
    }
      
    /**
     * Sets resource_type_id
     *
     * @param   int resource_type_id
     * @return  int the previous value
     */
    public function setResource_type_id($resource_type_id) {
      return $this->_change('resource_type_id', $resource_type_id);
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
     * Retrieves the Resource_type entity
     * referenced by resource_type_id=>resource_type_id
     *
     * @return  de.schlund.db.methadon.Resource_type entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getResource_type() {
      $r= XPClass::forName('de.schlund.db.methadon.Resource_type')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('resource_type_id', $this->getResource_type_id(), EQUAL)
      ));
      return $r ? $r[0] : NULL;
    }

    /**
     * Retrieves an array of all Resource_allocation entities referencing
     * this entity by resource_id=>resource_id
     *
     * @return  de.schlund.db.methadon.Resource_allocation[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getResource_allocationResourceList() {
      return XPClass::forName('de.schlund.db.methadon.Resource_allocation')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('resource_id', $this->getResource_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Resource_allocation entities referencing
     * this entity by resource_id=>resource_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Resource_allocation>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getResource_allocationResourceIterator() {
      return XPClass::forName('de.schlund.db.methadon.Resource_allocation')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('resource_id', $this->getResource_id(), EQUAL)
      ));
    }
  }
?>