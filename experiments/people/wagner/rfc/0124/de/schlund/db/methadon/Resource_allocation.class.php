<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet');

  /**
   * Class wrapper for table resource_allocation, database METHADON
   * (Auto-generated on Wed, 04 Apr 2007 10:45:27 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class Resource_allocation extends DataSet {

    protected
      $_isLoaded= false,
      $_loadCrit= NULL;

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('METHADON..resource_allocation');
        $peer->setConnection('sybintern');
        $peer->setIdentity('resource_allocation_id');
        $peer->setPrimary(array('resource_allocation_id'));
        $peer->setTypes(array(
          'motivation'          => array('%s', FieldType::VARCHAR, FALSE),
          'changedby'           => array('%s', FieldType::VARCHAR, FALSE),
          'feature'             => array('%d', FieldType::INT, FALSE),
          'resource_allocation_id' => array('%d', FieldType::NUMERIC, FALSE),
          'resource_id'         => array('%d', FieldType::NUMERIC, FALSE),
          'person_id'           => array('%d', FieldType::NUMERIC, FALSE),
          'bz_id'               => array('%d', FieldType::NUMERIC, FALSE),
          'ts_start'            => array('%s', FieldType::DATETIME, FALSE),
          'ts_end'              => array('%s', FieldType::DATETIME, FALSE),
          'ts_init'             => array('%s', FieldType::DATETIME, FALSE),
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
     * Gets an instance of this object by index "resource_allocation_I0"
     * 
     * @param   int resource_id
     * @return  de.schlund.db.methadon.Resource_allocation[] entity objects
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByResource_id($resource_id) {
      $r= self::getPeer()->doSelect(new Criteria(array('resource_id', $resource_id, EQUAL)));
      foreach ($r as $e) $e->_isLoaded= true;
      return $r;
    }

    /**
     * Gets an instance of this object by index "resource_allocation_I1"
     * 
     * @param   int person_id
     * @return  de.schlund.db.methadon.Resource_allocation[] entity objects
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByPerson_id($person_id) {
      $r= self::getPeer()->doSelect(new Criteria(array('person_id', $person_id, EQUAL)));
      foreach ($r as $e) $e->_isLoaded= true;
      return $r;
    }

    /**
     * Gets an instance of this object by index "PK_RESALLOC"
     * 
     * @param   int resource_allocation_id
     * @return  de.schlund.db.methadon.Resource_allocation entitiy object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByResource_allocation_id($resource_allocation_id) {
      return new self(array(
        'resource_allocation_id'  => $resource_allocation_id,
        '_loadCrit' => new Criteria(array('resource_allocation_id', $resource_allocation_id, EQUAL))
      ));
    }

    /**
     * Retrieves motivation
     *
     * @return  string
     */
    public function getMotivation() {
      return $this->motivation;
    }
      
    /**
     * Sets motivation
     *
     * @param   string motivation
     * @return  string the previous value
     */
    public function setMotivation($motivation) {
      return $this->_change('motivation', $motivation);
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
     * Retrieves resource_allocation_id
     *
     * @return  int
     */
    public function getResource_allocation_id() {
      return $this->resource_allocation_id;
    }
      
    /**
     * Sets resource_allocation_id
     *
     * @param   int resource_allocation_id
     * @return  int the previous value
     */
    public function setResource_allocation_id($resource_allocation_id) {
      return $this->_change('resource_allocation_id', $resource_allocation_id);
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
     * Retrieves ts_start
     *
     * @return  util.Date
     */
    public function getTs_start() {
      return $this->ts_start;
    }
      
    /**
     * Sets ts_start
     *
     * @param   util.Date ts_start
     * @return  util.Date the previous value
     */
    public function setTs_start($ts_start) {
      return $this->_change('ts_start', $ts_start);
    }

    /**
     * Retrieves ts_end
     *
     * @return  util.Date
     */
    public function getTs_end() {
      return $this->ts_end;
    }
      
    /**
     * Sets ts_end
     *
     * @param   util.Date ts_end
     * @return  util.Date the previous value
     */
    public function setTs_end($ts_end) {
      return $this->_change('ts_end', $ts_end);
    }

    /**
     * Retrieves ts_init
     *
     * @return  util.Date
     */
    public function getTs_init() {
      return $this->ts_init;
    }
      
    /**
     * Sets ts_init
     *
     * @param   util.Date ts_init
     * @return  util.Date the previous value
     */
    public function setTs_init($ts_init) {
      return $this->_change('ts_init', $ts_init);
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
     * Retrieves the Resource entity
     * referenced by resource_id=>resource_id
     *
     * @return  de.schlund.db.methadon.Resource entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getResource() {
      $r= XPClass::forName('de.schlund.db.methadon.Resource')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('resource_id', $this->getResource_id(), EQUAL)
      ));
      return $r ? $r[0] : NULL;
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
  }
?>