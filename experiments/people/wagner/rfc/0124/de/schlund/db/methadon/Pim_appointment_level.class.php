<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet');

  /**
   * Class wrapper for table pim_appointment_level, database METHADON
   * (Auto-generated on Wed, 04 Apr 2007 10:45:27 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class Pim_appointment_level extends DataSet {

    protected
      $_isLoaded= false,
      $_loadCrit= NULL;

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('METHADON..pim_appointment_level');
        $peer->setConnection('sybintern');
        $peer->setPrimary(array(''));
        $peer->setTypes(array(
          'name'                => array('%s', FieldType::VARCHAR, FALSE),
          'description'         => array('%s', FieldType::VARCHAR, TRUE),
          'appointment_level_id' => array('%d', FieldType::NUMERIC, FALSE)
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
     * Retrieves appointment_level_id
     *
     * @return  int
     */
    public function getAppointment_level_id() {
      return $this->appointment_level_id;
    }
      
    /**
     * Sets appointment_level_id
     *
     * @param   int appointment_level_id
     * @return  int the previous value
     */
    public function setAppointment_level_id($appointment_level_id) {
      return $this->_change('appointment_level_id', $appointment_level_id);
    }

    /**
     * Retrieves an array of all Pim_appointment entities referencing
     * this entity by appointment_level_id=>appointment_level_id
     *
     * @return  de.schlund.db.methadon.Pim_appointment[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getPim_appointmentAppointment_levelList() {
      return XPClass::forName('de.schlund.db.methadon.Pim_appointment')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('appointment_level_id', $this->getAppointment_level_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Pim_appointment entities referencing
     * this entity by appointment_level_id=>appointment_level_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Pim_appointment>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getPim_appointmentAppointment_levelIterator() {
      return XPClass::forName('de.schlund.db.methadon.Pim_appointment')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('appointment_level_id', $this->getAppointment_level_id(), EQUAL)
      ));
    }
  }
?>