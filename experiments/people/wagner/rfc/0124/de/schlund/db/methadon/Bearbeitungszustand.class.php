<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet');

  /**
   * Class wrapper for table bearbeitungszustand, database METHADON
   * (Auto-generated on Wed, 04 Apr 2007 10:45:27 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class Bearbeitungszustand extends DataSet {

    protected
      $_isLoaded= false,
      $_loadCrit= NULL;

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('METHADON..bearbeitungszustand');
        $peer->setConnection('sybintern');
        $peer->setPrimary(array('bz_id'));
        $peer->setTypes(array(
          'description'         => array('%s', FieldType::VARCHAR, FALSE),
          'bz_id'               => array('%d', FieldType::NUMERIC, FALSE)
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
     * Gets an instance of this object by index "PK_BEARBEITUNGSZUSTAND"
     * 
     * @param   int bz_id
     * @return  de.schlund.db.methadon.Bearbeitungszustand entitiy object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByBz_id($bz_id) {
      return new self(array(
        'bz_id'  => $bz_id,
        '_loadCrit' => new Criteria(array('bz_id', $bz_id, EQUAL))
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
     * Retrieves an array of all Eventng entities referencing
     * this entity by bz_id=>bz_id
     *
     * @return  de.schlund.db.methadon.Eventng[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEventngBzList() {
      return XPClass::forName('de.schlund.db.methadon.Eventng')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('bz_id', $this->getBz_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Eventng entities referencing
     * this entity by bz_id=>bz_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Eventng>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEventngBzIterator() {
      return XPClass::forName('de.schlund.db.methadon.Eventng')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('bz_id', $this->getBz_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Email entities referencing
     * this entity by bz_id=>bz_id
     *
     * @return  de.schlund.db.methadon.Email[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEmailBzList() {
      return XPClass::forName('de.schlund.db.methadon.Email')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('bz_id', $this->getBz_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Email entities referencing
     * this entity by bz_id=>bz_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Email>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEmailBzIterator() {
      return XPClass::forName('de.schlund.db.methadon.Email')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('bz_id', $this->getBz_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Program_schedule entities referencing
     * this entity by bz_id=>bz_id
     *
     * @return  de.schlund.db.methadon.Program_schedule[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getProgram_scheduleBzList() {
      return XPClass::forName('de.schlund.db.methadon.Program_schedule')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('bz_id', $this->getBz_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Program_schedule entities referencing
     * this entity by bz_id=>bz_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Program_schedule>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getProgram_scheduleBzIterator() {
      return XPClass::forName('de.schlund.db.methadon.Program_schedule')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('bz_id', $this->getBz_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Ts_sheet entities referencing
     * this entity by bz_id=>bz_id
     *
     * @return  de.schlund.db.methadon.Ts_sheet[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getTs_sheetBzList() {
      return XPClass::forName('de.schlund.db.methadon.Ts_sheet')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('bz_id', $this->getBz_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Ts_sheet entities referencing
     * this entity by bz_id=>bz_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Ts_sheet>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getTs_sheetBzIterator() {
      return XPClass::forName('de.schlund.db.methadon.Ts_sheet')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('bz_id', $this->getBz_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Heredity_history entities referencing
     * this entity by bz_id=>bz_id
     *
     * @return  de.schlund.db.methadon.Heredity_history[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getHeredity_historyBzList() {
      return XPClass::forName('de.schlund.db.methadon.Heredity_history')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('bz_id', $this->getBz_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Heredity_history entities referencing
     * this entity by bz_id=>bz_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Heredity_history>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getHeredity_historyBzIterator() {
      return XPClass::forName('de.schlund.db.methadon.Heredity_history')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('bz_id', $this->getBz_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all End_device entities referencing
     * this entity by bz_id=>bz_id
     *
     * @return  de.schlund.db.methadon.End_device[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEnd_deviceBzList() {
      return XPClass::forName('de.schlund.db.methadon.End_device')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('bz_id', $this->getBz_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all End_device entities referencing
     * this entity by bz_id=>bz_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.End_device>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEnd_deviceBzIterator() {
      return XPClass::forName('de.schlund.db.methadon.End_device')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('bz_id', $this->getBz_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Theme entities referencing
     * this entity by bz_id=>bz_id
     *
     * @return  de.schlund.db.methadon.Theme[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getThemeBzList() {
      return XPClass::forName('de.schlund.db.methadon.Theme')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('bz_id', $this->getBz_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Theme entities referencing
     * this entity by bz_id=>bz_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Theme>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getThemeBzIterator() {
      return XPClass::forName('de.schlund.db.methadon.Theme')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('bz_id', $this->getBz_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Eventng_history entities referencing
     * this entity by bz_id=>bz_id
     *
     * @return  de.schlund.db.methadon.Eventng_history[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEventng_historyBzList() {
      return XPClass::forName('de.schlund.db.methadon.Eventng_history')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('bz_id', $this->getBz_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Eventng_history entities referencing
     * this entity by bz_id=>bz_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Eventng_history>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEventng_historyBzIterator() {
      return XPClass::forName('de.schlund.db.methadon.Eventng_history')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('bz_id', $this->getBz_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Person entities referencing
     * this entity by bz_id=>bz_id
     *
     * @return  de.schlund.db.methadon.Person[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getPersonBzList() {
      return XPClass::forName('de.schlund.db.methadon.Person')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('bz_id', $this->getBz_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Person entities referencing
     * this entity by bz_id=>bz_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Person>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getPersonBzIterator() {
      return XPClass::forName('de.schlund.db.methadon.Person')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('bz_id', $this->getBz_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Right entities referencing
     * this entity by bz_id=>bz_id
     *
     * @return  de.schlund.db.methadon.Right[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getRightBzList() {
      return XPClass::forName('de.schlund.db.methadon.Right')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('bz_id', $this->getBz_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Right entities referencing
     * this entity by bz_id=>bz_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Right>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getRightBzIterator() {
      return XPClass::forName('de.schlund.db.methadon.Right')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('bz_id', $this->getBz_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Event entities referencing
     * this entity by bz_id=>bz_id
     *
     * @return  de.schlund.db.methadon.Event[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEventBzList() {
      return XPClass::forName('de.schlund.db.methadon.Event')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('bz_id', $this->getBz_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Event entities referencing
     * this entity by bz_id=>bz_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Event>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEventBzIterator() {
      return XPClass::forName('de.schlund.db.methadon.Event')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('bz_id', $this->getBz_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Eventng_recurrence_criteria entities referencing
     * this entity by bz_id=>bz_id
     *
     * @return  de.schlund.db.methadon.Eventng_recurrence_criteria[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEventng_recurrence_criteriaBzList() {
      return XPClass::forName('de.schlund.db.methadon.Eventng_recurrence_criteria')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('bz_id', $this->getBz_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Eventng_recurrence_criteria entities referencing
     * this entity by bz_id=>bz_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Eventng_recurrence_criteria>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEventng_recurrence_criteriaBzIterator() {
      return XPClass::forName('de.schlund.db.methadon.Eventng_recurrence_criteria')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('bz_id', $this->getBz_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Department entities referencing
     * this entity by bz_id=>bz_id
     *
     * @return  de.schlund.db.methadon.Department[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getDepartmentBzList() {
      return XPClass::forName('de.schlund.db.methadon.Department')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('bz_id', $this->getBz_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Department entities referencing
     * this entity by bz_id=>bz_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Department>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getDepartmentBzIterator() {
      return XPClass::forName('de.schlund.db.methadon.Department')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('bz_id', $this->getBz_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Textpart entities referencing
     * this entity by bz_id=>bz_id
     *
     * @return  de.schlund.db.methadon.Textpart[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getTextpartBzList() {
      return XPClass::forName('de.schlund.db.methadon.Textpart')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('bz_id', $this->getBz_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Textpart entities referencing
     * this entity by bz_id=>bz_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Textpart>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getTextpartBzIterator() {
      return XPClass::forName('de.schlund.db.methadon.Textpart')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('bz_id', $this->getBz_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Event_slot entities referencing
     * this entity by bz_id=>bz_id
     *
     * @return  de.schlund.db.methadon.Event_slot[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEvent_slotBzList() {
      return XPClass::forName('de.schlund.db.methadon.Event_slot')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('bz_id', $this->getBz_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Event_slot entities referencing
     * this entity by bz_id=>bz_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Event_slot>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEvent_slotBzIterator() {
      return XPClass::forName('de.schlund.db.methadon.Event_slot')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('bz_id', $this->getBz_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Employee entities referencing
     * this entity by bz_id=>bz_id
     *
     * @return  de.schlund.db.methadon.Employee[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEmployeeBzList() {
      return XPClass::forName('de.schlund.db.methadon.Employee')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('bz_id', $this->getBz_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Employee entities referencing
     * this entity by bz_id=>bz_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Employee>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEmployeeBzIterator() {
      return XPClass::forName('de.schlund.db.methadon.Employee')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('bz_id', $this->getBz_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Textpart_matrix entities referencing
     * this entity by bz_id=>bz_id
     *
     * @return  de.schlund.db.methadon.Textpart_matrix[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getTextpart_matrixBzList() {
      return XPClass::forName('de.schlund.db.methadon.Textpart_matrix')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('bz_id', $this->getBz_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Textpart_matrix entities referencing
     * this entity by bz_id=>bz_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Textpart_matrix>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getTextpart_matrixBzIterator() {
      return XPClass::forName('de.schlund.db.methadon.Textpart_matrix')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('bz_id', $this->getBz_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Eventng_recurrence_matrix entities referencing
     * this entity by bz_id=>bz_id
     *
     * @return  de.schlund.db.methadon.Eventng_recurrence_matrix[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEventng_recurrence_matrixBzList() {
      return XPClass::forName('de.schlund.db.methadon.Eventng_recurrence_matrix')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('bz_id', $this->getBz_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Eventng_recurrence_matrix entities referencing
     * this entity by bz_id=>bz_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Eventng_recurrence_matrix>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEventng_recurrence_matrixBzIterator() {
      return XPClass::forName('de.schlund.db.methadon.Eventng_recurrence_matrix')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('bz_id', $this->getBz_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Event_person_matrix entities referencing
     * this entity by bz_id=>bz_id
     *
     * @return  de.schlund.db.methadon.Event_person_matrix[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEvent_person_matrixBzList() {
      return XPClass::forName('de.schlund.db.methadon.Event_person_matrix')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('bz_id', $this->getBz_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Event_person_matrix entities referencing
     * this entity by bz_id=>bz_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Event_person_matrix>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEvent_person_matrixBzIterator() {
      return XPClass::forName('de.schlund.db.methadon.Event_person_matrix')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('bz_id', $this->getBz_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Resource_category entities referencing
     * this entity by bz_id=>bz_id
     *
     * @return  de.schlund.db.methadon.Resource_category[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getResource_categoryBzList() {
      return XPClass::forName('de.schlund.db.methadon.Resource_category')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('bz_id', $this->getBz_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Resource_category entities referencing
     * this entity by bz_id=>bz_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Resource_category>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getResource_categoryBzIterator() {
      return XPClass::forName('de.schlund.db.methadon.Resource_category')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('bz_id', $this->getBz_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Ts_student_vacation entities referencing
     * this entity by bz_id=>bz_id
     *
     * @return  de.schlund.db.methadon.Ts_student_vacation[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getTs_student_vacationBzList() {
      return XPClass::forName('de.schlund.db.methadon.Ts_student_vacation')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('bz_id', $this->getBz_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Ts_student_vacation entities referencing
     * this entity by bz_id=>bz_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Ts_student_vacation>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getTs_student_vacationBzIterator() {
      return XPClass::forName('de.schlund.db.methadon.Ts_student_vacation')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('bz_id', $this->getBz_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Eventng_exception entities referencing
     * this entity by bz_id=>bz_id
     *
     * @return  de.schlund.db.methadon.Eventng_exception[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEventng_exceptionBzList() {
      return XPClass::forName('de.schlund.db.methadon.Eventng_exception')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('bz_id', $this->getBz_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Eventng_exception entities referencing
     * this entity by bz_id=>bz_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Eventng_exception>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEventng_exceptionBzIterator() {
      return XPClass::forName('de.schlund.db.methadon.Eventng_exception')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('bz_id', $this->getBz_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Person_resource_category entities referencing
     * this entity by bz_id=>bz_id
     *
     * @return  de.schlund.db.methadon.Person_resource_category[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getPerson_resource_categoryBzList() {
      return XPClass::forName('de.schlund.db.methadon.Person_resource_category')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('bz_id', $this->getBz_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Person_resource_category entities referencing
     * this entity by bz_id=>bz_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Person_resource_category>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getPerson_resource_categoryBzIterator() {
      return XPClass::forName('de.schlund.db.methadon.Person_resource_category')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('bz_id', $this->getBz_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Module entities referencing
     * this entity by bz_id=>bz_id
     *
     * @return  de.schlund.db.methadon.Module[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getModuleBzList() {
      return XPClass::forName('de.schlund.db.methadon.Module')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('bz_id', $this->getBz_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Module entities referencing
     * this entity by bz_id=>bz_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Module>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getModuleBzIterator() {
      return XPClass::forName('de.schlund.db.methadon.Module')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('bz_id', $this->getBz_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Ims entities referencing
     * this entity by bz_id=>bz_id
     *
     * @return  de.schlund.db.methadon.Ims[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getImsBzList() {
      return XPClass::forName('de.schlund.db.methadon.Ims')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('bz_id', $this->getBz_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Ims entities referencing
     * this entity by bz_id=>bz_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Ims>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getImsBzIterator() {
      return XPClass::forName('de.schlund.db.methadon.Ims')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('bz_id', $this->getBz_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Ts_user_info entities referencing
     * this entity by bz_id=>bz_id
     *
     * @return  de.schlund.db.methadon.Ts_user_info[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getTs_user_infoBzList() {
      return XPClass::forName('de.schlund.db.methadon.Ts_user_info')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('bz_id', $this->getBz_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Ts_user_info entities referencing
     * this entity by bz_id=>bz_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Ts_user_info>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getTs_user_infoBzIterator() {
      return XPClass::forName('de.schlund.db.methadon.Ts_user_info')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('bz_id', $this->getBz_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Fileshare_file entities referencing
     * this entity by bz_id=>bz_id
     *
     * @return  de.schlund.db.methadon.Fileshare_file[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getFileshare_fileBzList() {
      return XPClass::forName('de.schlund.db.methadon.Fileshare_file')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('bz_id', $this->getBz_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Fileshare_file entities referencing
     * this entity by bz_id=>bz_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Fileshare_file>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getFileshare_fileBzIterator() {
      return XPClass::forName('de.schlund.db.methadon.Fileshare_file')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('bz_id', $this->getBz_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Resourceng entities referencing
     * this entity by bz_id=>bz_id
     *
     * @return  de.schlund.db.methadon.Resourceng[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getResourcengBzList() {
      return XPClass::forName('de.schlund.db.methadon.Resourceng')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('bz_id', $this->getBz_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Resourceng entities referencing
     * this entity by bz_id=>bz_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Resourceng>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getResourcengBzIterator() {
      return XPClass::forName('de.schlund.db.methadon.Resourceng')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('bz_id', $this->getBz_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Reservation_criteria entities referencing
     * this entity by bz_id=>bz_id
     *
     * @return  de.schlund.db.methadon.Reservation_criteria[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getReservation_criteriaBzList() {
      return XPClass::forName('de.schlund.db.methadon.Reservation_criteria')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('bz_id', $this->getBz_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Reservation_criteria entities referencing
     * this entity by bz_id=>bz_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Reservation_criteria>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getReservation_criteriaBzIterator() {
      return XPClass::forName('de.schlund.db.methadon.Reservation_criteria')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('bz_id', $this->getBz_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Ebay_category entities referencing
     * this entity by bz_id=>bz_id
     *
     * @return  de.schlund.db.methadon.Ebay_category[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEbay_categoryBzList() {
      return XPClass::forName('de.schlund.db.methadon.Ebay_category')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('bz_id', $this->getBz_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Ebay_category entities referencing
     * this entity by bz_id=>bz_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Ebay_category>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEbay_categoryBzIterator() {
      return XPClass::forName('de.schlund.db.methadon.Ebay_category')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('bz_id', $this->getBz_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Resource_reservation entities referencing
     * this entity by bz_id=>bz_id
     *
     * @return  de.schlund.db.methadon.Resource_reservation[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getResource_reservationBzList() {
      return XPClass::forName('de.schlund.db.methadon.Resource_reservation')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('bz_id', $this->getBz_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Resource_reservation entities referencing
     * this entity by bz_id=>bz_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Resource_reservation>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getResource_reservationBzIterator() {
      return XPClass::forName('de.schlund.db.methadon.Resource_reservation')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('bz_id', $this->getBz_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Message entities referencing
     * this entity by bz_id=>bz_id
     *
     * @return  de.schlund.db.methadon.Message[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getMessageBzList() {
      return XPClass::forName('de.schlund.db.methadon.Message')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('bz_id', $this->getBz_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Message entities referencing
     * this entity by bz_id=>bz_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Message>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getMessageBzIterator() {
      return XPClass::forName('de.schlund.db.methadon.Message')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('bz_id', $this->getBz_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Ts_user_history entities referencing
     * this entity by bz_id=>bz_id
     *
     * @return  de.schlund.db.methadon.Ts_user_history[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getTs_user_historyBzList() {
      return XPClass::forName('de.schlund.db.methadon.Ts_user_history')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('bz_id', $this->getBz_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Ts_user_history entities referencing
     * this entity by bz_id=>bz_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Ts_user_history>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getTs_user_historyBzIterator() {
      return XPClass::forName('de.schlund.db.methadon.Ts_user_history')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('bz_id', $this->getBz_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Fileshare_folder entities referencing
     * this entity by bz_id=>bz_id
     *
     * @return  de.schlund.db.methadon.Fileshare_folder[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getFileshare_folderBzList() {
      return XPClass::forName('de.schlund.db.methadon.Fileshare_folder')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('bz_id', $this->getBz_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Fileshare_folder entities referencing
     * this entity by bz_id=>bz_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Fileshare_folder>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getFileshare_folderBzIterator() {
      return XPClass::forName('de.schlund.db.methadon.Fileshare_folder')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('bz_id', $this->getBz_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Ebay_ad entities referencing
     * this entity by bz_id=>bz_id
     *
     * @return  de.schlund.db.methadon.Ebay_ad[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEbay_adBzList() {
      return XPClass::forName('de.schlund.db.methadon.Ebay_ad')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('bz_id', $this->getBz_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Ebay_ad entities referencing
     * this entity by bz_id=>bz_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Ebay_ad>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEbay_adBzIterator() {
      return XPClass::forName('de.schlund.db.methadon.Ebay_ad')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('bz_id', $this->getBz_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Pm_project entities referencing
     * this entity by bz_id=>bz_id
     *
     * @return  de.schlund.db.methadon.Pm_project[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getPm_projectBzList() {
      return XPClass::forName('de.schlund.db.methadon.Pm_project')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('bz_id', $this->getBz_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Pm_project entities referencing
     * this entity by bz_id=>bz_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Pm_project>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getPm_projectBzIterator() {
      return XPClass::forName('de.schlund.db.methadon.Pm_project')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('bz_id', $this->getBz_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Fileshare_file_version entities referencing
     * this entity by bz_id=>bz_id
     *
     * @return  de.schlund.db.methadon.Fileshare_file_version[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getFileshare_file_versionBzList() {
      return XPClass::forName('de.schlund.db.methadon.Fileshare_file_version')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('bz_id', $this->getBz_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Fileshare_file_version entities referencing
     * this entity by bz_id=>bz_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Fileshare_file_version>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getFileshare_file_versionBzIterator() {
      return XPClass::forName('de.schlund.db.methadon.Fileshare_file_version')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('bz_id', $this->getBz_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all External_password entities referencing
     * this entity by bz_id=>bz_id
     *
     * @return  de.schlund.db.methadon.External_password[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getExternal_passwordBzList() {
      return XPClass::forName('de.schlund.db.methadon.External_password')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('bz_id', $this->getBz_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all External_password entities referencing
     * this entity by bz_id=>bz_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.External_password>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getExternal_passwordBzIterator() {
      return XPClass::forName('de.schlund.db.methadon.External_password')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('bz_id', $this->getBz_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Ebay_ad_category entities referencing
     * this entity by bz_id=>bz_id
     *
     * @return  de.schlund.db.methadon.Ebay_ad_category[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEbay_ad_categoryBzList() {
      return XPClass::forName('de.schlund.db.methadon.Ebay_ad_category')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('bz_id', $this->getBz_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Ebay_ad_category entities referencing
     * this entity by bz_id=>bz_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Ebay_ad_category>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEbay_ad_categoryBzIterator() {
      return XPClass::forName('de.schlund.db.methadon.Ebay_ad_category')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('bz_id', $this->getBz_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Pim_appointment entities referencing
     * this entity by bz_id=>bz_id
     *
     * @return  de.schlund.db.methadon.Pim_appointment[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getPim_appointmentBzList() {
      return XPClass::forName('de.schlund.db.methadon.Pim_appointment')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('bz_id', $this->getBz_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Pim_appointment entities referencing
     * this entity by bz_id=>bz_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Pim_appointment>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getPim_appointmentBzIterator() {
      return XPClass::forName('de.schlund.db.methadon.Pim_appointment')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('bz_id', $this->getBz_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Project entities referencing
     * this entity by bz_id=>bz_id
     *
     * @return  de.schlund.db.methadon.Project[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getProjectBzList() {
      return XPClass::forName('de.schlund.db.methadon.Project')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('bz_id', $this->getBz_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Project entities referencing
     * this entity by bz_id=>bz_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Project>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getProjectBzIterator() {
      return XPClass::forName('de.schlund.db.methadon.Project')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('bz_id', $this->getBz_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Request_category entities referencing
     * this entity by bz_id=>bz_id
     *
     * @return  de.schlund.db.methadon.Request_category[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getRequest_categoryBzList() {
      return XPClass::forName('de.schlund.db.methadon.Request_category')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('bz_id', $this->getBz_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Request_category entities referencing
     * this entity by bz_id=>bz_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Request_category>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getRequest_categoryBzIterator() {
      return XPClass::forName('de.schlund.db.methadon.Request_category')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('bz_id', $this->getBz_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Resource entities referencing
     * this entity by bz_id=>bz_id
     *
     * @return  de.schlund.db.methadon.Resource[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getResourceBzList() {
      return XPClass::forName('de.schlund.db.methadon.Resource')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('bz_id', $this->getBz_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Resource entities referencing
     * this entity by bz_id=>bz_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Resource>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getResourceBzIterator() {
      return XPClass::forName('de.schlund.db.methadon.Resource')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('bz_id', $this->getBz_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Person_request_category entities referencing
     * this entity by bz_id=>bz_id
     *
     * @return  de.schlund.db.methadon.Person_request_category[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getPerson_request_categoryBzList() {
      return XPClass::forName('de.schlund.db.methadon.Person_request_category')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('bz_id', $this->getBz_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Person_request_category entities referencing
     * this entity by bz_id=>bz_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Person_request_category>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getPerson_request_categoryBzIterator() {
      return XPClass::forName('de.schlund.db.methadon.Person_request_category')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('bz_id', $this->getBz_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Event_template entities referencing
     * this entity by bz_id=>bz_id
     *
     * @return  de.schlund.db.methadon.Event_template[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEvent_templateBzList() {
      return XPClass::forName('de.schlund.db.methadon.Event_template')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('bz_id', $this->getBz_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Event_template entities referencing
     * this entity by bz_id=>bz_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Event_template>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getEvent_templateBzIterator() {
      return XPClass::forName('de.schlund.db.methadon.Event_template')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('bz_id', $this->getBz_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Resource_allocation entities referencing
     * this entity by bz_id=>bz_id
     *
     * @return  de.schlund.db.methadon.Resource_allocation[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getResource_allocationBzList() {
      return XPClass::forName('de.schlund.db.methadon.Resource_allocation')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('bz_id', $this->getBz_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Resource_allocation entities referencing
     * this entity by bz_id=>bz_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Resource_allocation>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getResource_allocationBzIterator() {
      return XPClass::forName('de.schlund.db.methadon.Resource_allocation')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('bz_id', $this->getBz_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Requested_item entities referencing
     * this entity by bz_id=>bz_id
     *
     * @return  de.schlund.db.methadon.Requested_item[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getRequested_itemBzList() {
      return XPClass::forName('de.schlund.db.methadon.Requested_item')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('bz_id', $this->getBz_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Requested_item entities referencing
     * this entity by bz_id=>bz_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Requested_item>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getRequested_itemBzIterator() {
      return XPClass::forName('de.schlund.db.methadon.Requested_item')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('bz_id', $this->getBz_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Bug entities referencing
     * this entity by bz_id=>bz_id
     *
     * @return  de.schlund.db.methadon.Bug[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getBugBzList() {
      return XPClass::forName('de.schlund.db.methadon.Bug')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('bz_id', $this->getBz_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Bug entities referencing
     * this entity by bz_id=>bz_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Bug>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getBugBzIterator() {
      return XPClass::forName('de.schlund.db.methadon.Bug')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('bz_id', $this->getBz_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Document entities referencing
     * this entity by bz_id=>bz_id
     *
     * @return  de.schlund.db.methadon.Document[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getDocumentBzList() {
      return XPClass::forName('de.schlund.db.methadon.Document')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('bz_id', $this->getBz_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Document entities referencing
     * this entity by bz_id=>bz_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Document>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getDocumentBzIterator() {
      return XPClass::forName('de.schlund.db.methadon.Document')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('bz_id', $this->getBz_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Bug_channel entities referencing
     * this entity by bz_id=>bz_id
     *
     * @return  de.schlund.db.methadon.Bug_channel[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getBug_channelBzList() {
      return XPClass::forName('de.schlund.db.methadon.Bug_channel')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('bz_id', $this->getBz_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Bug_channel entities referencing
     * this entity by bz_id=>bz_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Bug_channel>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getBug_channelBzIterator() {
      return XPClass::forName('de.schlund.db.methadon.Bug_channel')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('bz_id', $this->getBz_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all News entities referencing
     * this entity by bz_id=>bz_id
     *
     * @return  de.schlund.db.methadon.News[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getNewsBzList() {
      return XPClass::forName('de.schlund.db.methadon.News')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('bz_id', $this->getBz_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all News entities referencing
     * this entity by bz_id=>bz_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.News>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getNewsBzIterator() {
      return XPClass::forName('de.schlund.db.methadon.News')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('bz_id', $this->getBz_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Bug_history entities referencing
     * this entity by bz_id=>bz_id
     *
     * @return  de.schlund.db.methadon.Bug_history[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getBug_historyBzList() {
      return XPClass::forName('de.schlund.db.methadon.Bug_history')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('bz_id', $this->getBz_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Bug_history entities referencing
     * this entity by bz_id=>bz_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Bug_history>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getBug_historyBzIterator() {
      return XPClass::forName('de.schlund.db.methadon.Bug_history')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('bz_id', $this->getBz_id(), EQUAL)
      ));
    }
  }
?>