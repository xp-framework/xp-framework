<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet');

  /**
   * Class wrapper for table end_device_type, database METHADON
   * (Auto-generated on Wed, 04 Apr 2007 10:45:27 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class End_device_type extends DataSet {

    protected
      $_isLoaded= false,
      $_loadCrit= NULL;

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('METHADON..end_device_type');
        $peer->setConnection('sybintern');
        $peer->setPrimary(array('end_device_type_id'));
        $peer->setTypes(array(
          'hostname'            => array('%s', FieldType::VARCHAR, FALSE),
          'description'         => array('%s', FieldType::VARCHAR, TRUE),
          'end_device_type_id'  => array('%d', FieldType::NUMERIC, FALSE),
          'feature'             => array('%d', FieldType::INTN, TRUE)
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
     * Gets an instance of this object by index "PK_END_DEVICE_TYPE"
     * 
     * @param   int end_device_type_id
     * @return  de.schlund.db.methadon.End_device_type entitiy object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByEnd_device_type_id($end_device_type_id) {
      return new self(array(
        'end_device_type_id'  => $end_device_type_id,
        '_loadCrit' => new Criteria(array('end_device_type_id', $end_device_type_id, EQUAL))
      ));
    }

    /**
     * Retrieves hostname
     *
     * @return  string
     */
    public function getHostname() {
      return $this->hostname;
    }
      
    /**
     * Sets hostname
     *
     * @param   string hostname
     * @return  string the previous value
     */
    public function setHostname($hostname) {
      return $this->_change('hostname', $hostname);
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
     * Retrieves end_device_type_id
     *
     * @return  int
     */
    public function getEnd_device_type_id() {
      return $this->end_device_type_id;
    }
      
    /**
     * Sets end_device_type_id
     *
     * @param   int end_device_type_id
     * @return  int the previous value
     */
    public function setEnd_device_type_id($end_device_type_id) {
      return $this->_change('end_device_type_id', $end_device_type_id);
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
     * Retrieves an array of all End_device entities referencing
     * this entity by end_device_type_id=>end_device_type_id
     *
     * @return  de.schlund.db.methadon.End_device[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEnd_deviceEnd_device_typeList() {
      return XPClass::forName('de.schlund.db.methadon.End_device')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('end_device_type_id', $this->getEnd_device_type_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all End_device entities referencing
     * this entity by end_device_type_id=>end_device_type_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.End_device>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEnd_deviceEnd_device_typeIterator() {
      return XPClass::forName('de.schlund.db.methadon.End_device')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('end_device_type_id', $this->getEnd_device_type_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all End_device_property entities referencing
     * this entity by end_device_type_id=>end_device_type_id
     *
     * @return  de.schlund.db.methadon.End_device_property[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEnd_device_propertyEnd_device_typeList() {
      return XPClass::forName('de.schlund.db.methadon.End_device_property')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('end_device_type_id', $this->getEnd_device_type_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all End_device_property entities referencing
     * this entity by end_device_type_id=>end_device_type_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.End_device_property>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEnd_device_propertyEnd_device_typeIterator() {
      return XPClass::forName('de.schlund.db.methadon.End_device_property')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('end_device_type_id', $this->getEnd_device_type_id(), EQUAL)
      ));
    }
  }
?>