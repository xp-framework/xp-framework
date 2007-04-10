<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet');

  /**
   * Class wrapper for table pim_appointment, database METHADON
   * (Auto-generated on Wed, 04 Apr 2007 10:45:27 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class Pim_appointment extends DataSet {

    protected
      $_isLoaded= false,
      $_loadCrit= NULL;

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('METHADON..pim_appointment');
        $peer->setConnection('sybintern');
        $peer->setIdentity('appointment_id');
        $peer->setPrimary(array('appointment_id'));
        $peer->setTypes(array(
          'subject'             => array('%s', FieldType::VARCHAR, FALSE),
          'location'            => array('%s', FieldType::VARCHAR, TRUE),
          'appointment_id'      => array('%d', FieldType::NUMERIC, FALSE),
          'person_id'           => array('%d', FieldType::NUMERIC, FALSE),
          'type_id'             => array('%d', FieldType::NUMERIC, FALSE),
          'appointment_level_id' => array('%d', FieldType::NUMERIC, FALSE),
          'bz_id'               => array('%d', FieldType::NUMERIC, FALSE),
          'appointment_start'   => array('%s', FieldType::DATETIME, FALSE),
          'rightfeature'        => array('%d', FieldType::INTN, TRUE),
          'appointment_end'     => array('%s', FieldType::DATETIMN, TRUE)
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
     * Gets an instance of this object by index "PK_APPOINTMENT"
     * 
     * @param   int appointment_id
     * @return  de.schlund.db.methadon.Pim_appointment entitiy object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByAppointment_id($appointment_id) {
      return new self(array(
        'appointment_id'  => $appointment_id,
        '_loadCrit' => new Criteria(array('appointment_id', $appointment_id, EQUAL))
      ));
    }

    /**
     * Retrieves subject
     *
     * @return  string
     */
    public function getSubject() {
      return $this->subject;
    }
      
    /**
     * Sets subject
     *
     * @param   string subject
     * @return  string the previous value
     */
    public function setSubject($subject) {
      return $this->_change('subject', $subject);
    }

    /**
     * Retrieves location
     *
     * @return  string
     */
    public function getLocation() {
      return $this->location;
    }
      
    /**
     * Sets location
     *
     * @param   string location
     * @return  string the previous value
     */
    public function setLocation($location) {
      return $this->_change('location', $location);
    }

    /**
     * Retrieves appointment_id
     *
     * @return  int
     */
    public function getAppointment_id() {
      return $this->appointment_id;
    }
      
    /**
     * Sets appointment_id
     *
     * @param   int appointment_id
     * @return  int the previous value
     */
    public function setAppointment_id($appointment_id) {
      return $this->_change('appointment_id', $appointment_id);
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
     * Retrieves type_id
     *
     * @return  int
     */
    public function getType_id() {
      return $this->type_id;
    }
      
    /**
     * Sets type_id
     *
     * @param   int type_id
     * @return  int the previous value
     */
    public function setType_id($type_id) {
      return $this->_change('type_id', $type_id);
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
     * Retrieves appointment_start
     *
     * @return  util.Date
     */
    public function getAppointment_start() {
      return $this->appointment_start;
    }
      
    /**
     * Sets appointment_start
     *
     * @param   util.Date appointment_start
     * @return  util.Date the previous value
     */
    public function setAppointment_start($appointment_start) {
      return $this->_change('appointment_start', $appointment_start);
    }

    /**
     * Retrieves rightfeature
     *
     * @return  int
     */
    public function getRightfeature() {
      return $this->rightfeature;
    }
      
    /**
     * Sets rightfeature
     *
     * @param   int rightfeature
     * @return  int the previous value
     */
    public function setRightfeature($rightfeature) {
      return $this->_change('rightfeature', $rightfeature);
    }

    /**
     * Retrieves appointment_end
     *
     * @return  util.Date
     */
    public function getAppointment_end() {
      return $this->appointment_end;
    }
      
    /**
     * Sets appointment_end
     *
     * @param   util.Date appointment_end
     * @return  util.Date the previous value
     */
    public function setAppointment_end($appointment_end) {
      return $this->_change('appointment_end', $appointment_end);
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
     * Retrieves the Pim_type entity
     * referenced by pim_type_id=>type_id
     *
     * @return  de.schlund.db.methadon.Pim_type entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getType() {
      $r= XPClass::forName('de.schlund.db.methadon.Pim_type')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('pim_type_id', $this->getType_id(), EQUAL)
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

    /**
     * Retrieves an array of all Pim_appointment_level entities
     * referenced by appointment_level_id=>appointment_level_id
     *
     * @return  de.schlund.db.methadon.Pim_appointment_level[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getAppointment_levelList() {
      return XPClass::forName('de.schlund.db.methadon.Pim_appointment_level')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
        array('appointment_level_id', $this->getAppointment_level_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Pim_appointment_level entities
     * referenced by appointment_level_id=>appointment_level_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Pim_appointment_level
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getAppointment_levelIterator() {
      return XPClass::forName('de.schlund.db.methadon.Pim_appointment_level')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
        array('appointment_level_id', $this->getAppointment_level_id(), EQUAL)
      ));
    }
  }
?>