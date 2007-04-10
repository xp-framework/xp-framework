<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet');

  /**
   * Class wrapper for table end_device_property_matrix, database METHADON
   * (Auto-generated on Wed, 04 Apr 2007 10:45:27 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class End_device_property_matrix extends DataSet {

    protected
      $_isLoaded= false,
      $_loadCrit= NULL;

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('METHADON..end_device_property_matrix');
        $peer->setConnection('sybintern');
        $peer->setPrimary(array(''));
        $peer->setTypes(array(
          'string_value'        => array('%s', FieldType::VARCHAR, TRUE),
          'end_device_property_id' => array('%d', FieldType::NUMERIC, FALSE),
          'end_device_id'       => array('%d', FieldType::NUMERIC, FALSE),
          'int_value'           => array('%d', FieldType::INTN, TRUE)
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
     * Retrieves string_value
     *
     * @return  string
     */
    public function getString_value() {
      return $this->string_value;
    }
      
    /**
     * Sets string_value
     *
     * @param   string string_value
     * @return  string the previous value
     */
    public function setString_value($string_value) {
      return $this->_change('string_value', $string_value);
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
     * Retrieves end_device_id
     *
     * @return  int
     */
    public function getEnd_device_id() {
      return $this->end_device_id;
    }
      
    /**
     * Sets end_device_id
     *
     * @param   int end_device_id
     * @return  int the previous value
     */
    public function setEnd_device_id($end_device_id) {
      return $this->_change('end_device_id', $end_device_id);
    }

    /**
     * Retrieves int_value
     *
     * @return  int
     */
    public function getInt_value() {
      return $this->int_value;
    }
      
    /**
     * Sets int_value
     *
     * @param   int int_value
     * @return  int the previous value
     */
    public function setInt_value($int_value) {
      return $this->_change('int_value', $int_value);
    }

    /**
     * Retrieves the End_device entity
     * referenced by end_device_id=>end_device_id
     *
     * @return  de.schlund.db.methadon.End_device entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEnd_device() {
      $r= XPClass::forName('de.schlund.db.methadon.End_device')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('end_device_id', $this->getEnd_device_id(), EQUAL)
      ));
      return $r ? $r[0] : NULL;
    }

    /**
     * Retrieves the End_device_property entity
     * referenced by end_device_property_id=>end_device_property_id
     *
     * @return  de.schlund.db.methadon.End_device_property entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEnd_device_property() {
      $r= XPClass::forName('de.schlund.db.methadon.End_device_property')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('end_device_property_id', $this->getEnd_device_property_id(), EQUAL)
      ));
      return $r ? $r[0] : NULL;
    }
  }
?>