<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet');

  /**
   * Class wrapper for table end_device_property, database METHADON
   * (Auto-generated on Wed, 04 Apr 2007 10:45:27 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class End_device_property extends DataSet {

    protected
      $_isLoaded= false,
      $_loadCrit= NULL;

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('METHADON..end_device_property');
        $peer->setConnection('sybintern');
        $peer->setIdentity('end_device_property_id');
        $peer->setPrimary(array('end_device_property_id'));
        $peer->setTypes(array(
          'description'         => array('%s', FieldType::VARCHAR, TRUE),
          'end_device_property_id' => array('%d', FieldType::NUMERIC, FALSE),
          'end_device_type_id'  => array('%d', FieldType::NUMERIC, FALSE)
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
     * Gets an instance of this object by index "PK_END_DEVICE_PROPERTY"
     * 
     * @param   int end_device_property_id
     * @return  de.schlund.db.methadon.End_device_property entitiy object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByEnd_device_property_id($end_device_property_id) {
      return new self(array(
        'end_device_property_id'  => $end_device_property_id,
        '_loadCrit' => new Criteria(array('end_device_property_id', $end_device_property_id, EQUAL))
      ));
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
     * Retrieves end_device_property_id
     *
     * @return  int
     */
    public function getEnd_device_property_id() {
      return $this->end_device_property_id;
    }
      
    /**
     * Sets end_device_property_id
     *
     * @param   int end_device_property_id
     * @return  int the previous value
     */
    public function setEnd_device_property_id($end_device_property_id) {
      return $this->_change('end_device_property_id', $end_device_property_id);
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
     * Retrieves the End_device_type entity
     * referenced by end_device_type_id=>end_device_type_id
     *
     * @return  de.schlund.db.methadon.End_device_type entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEnd_device_type() {
      $r= XPClass::forName('de.schlund.db.methadon.End_device_type')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('end_device_type_id', $this->getEnd_device_type_id(), EQUAL)
      ));
      return $r ? $r[0] : NULL;
    }

    /**
     * Retrieves an array of all End_device_property_matrix entities referencing
     * this entity by end_device_property_id=>end_device_property_id
     *
     * @return  de.schlund.db.methadon.End_device_property_matrix[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEnd_device_property_matrixEnd_device_propertyList() {
      return XPClass::forName('de.schlund.db.methadon.End_device_property_matrix')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('end_device_property_id', $this->getEnd_device_property_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all End_device_property_matrix entities referencing
     * this entity by end_device_property_id=>end_device_property_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.End_device_property_matrix>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEnd_device_property_matrixEnd_device_propertyIterator() {
      return XPClass::forName('de.schlund.db.methadon.End_device_property_matrix')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('end_device_property_id', $this->getEnd_device_property_id(), EQUAL)
      ));
    }
  }
?>