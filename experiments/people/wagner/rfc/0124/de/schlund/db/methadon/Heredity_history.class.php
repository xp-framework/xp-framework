<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet');

  /**
   * Class wrapper for table heredity_history, database METHADON
   * (Auto-generated on Wed, 04 Apr 2007 10:45:27 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class Heredity_history extends DataSet {

    protected
      $_isLoaded= false,
      $_loadCrit= NULL;

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('METHADON..heredity_history');
        $peer->setConnection('sybintern');
        $peer->setIdentity('heredity_history_id');
        $peer->setPrimary(array('heredity_history_id'));
        $peer->setTypes(array(
          'changedby'           => array('%s', FieldType::VARCHAR, FALSE),
          'heredityfeature'     => array('%d', FieldType::INT, FALSE),
          'heredity_history_id' => array('%d', FieldType::NUMERIC, FALSE),
          'person_id'           => array('%d', FieldType::NUMERIC, FALSE),
          'child_id'            => array('%d', FieldType::NUMERIC, FALSE),
          'parent_person_id'    => array('%d', FieldType::NUMERIC, FALSE),
          'bz_id'               => array('%d', FieldType::NUMERIC, FALSE),
          'creation_date'       => array('%s', FieldType::DATETIME, FALSE),
          'lastchange'          => array('%s', FieldType::DATETIME, FALSE),
          'parent_right_id'     => array('%d', FieldType::NUMERICN, TRUE),
          'parent_right_type_id' => array('%d', FieldType::NUMERICN, TRUE),
          'parent_right_level_id' => array('%d', FieldType::NUMERICN, TRUE)
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
     * Gets an instance of this object by index "PK_HEREDITY_HISTORY"
     * 
     * @param   int heredity_history_id
     * @return  de.schlund.db.methadon.Heredity_history entitiy object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByHeredity_history_id($heredity_history_id) {
      return new self(array(
        'heredity_history_id'  => $heredity_history_id,
        '_loadCrit' => new Criteria(array('heredity_history_id', $heredity_history_id, EQUAL))
      ));
    }

    /**
     * Gets an instance of this object by index "HH_CHILD_I"
     * 
     * @param   int child_id
     * @return  de.schlund.db.methadon.Heredity_history[] entity objects
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByChild_id($child_id) {
      $r= self::getPeer()->doSelect(new Criteria(array('child_id', $child_id, EQUAL)));
      foreach ($r as $e) $e->_isLoaded= true;
      return $r;
    }

    /**
     * Gets an instance of this object by index "HH_PARENT_I"
     * 
     * @param   int parent_person_id
     * @return  de.schlund.db.methadon.Heredity_history[] entity objects
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByParent_person_id($parent_person_id) {
      $r= self::getPeer()->doSelect(new Criteria(array('parent_person_id', $parent_person_id, EQUAL)));
      foreach ($r as $e) $e->_isLoaded= true;
      return $r;
    }

    /**
     * Gets an instance of this object by index "HH_BZ_I"
     * 
     * @param   int bz_id
     * @return  de.schlund.db.methadon.Heredity_history[] entity objects
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByBz_id($bz_id) {
      $r= self::getPeer()->doSelect(new Criteria(array('bz_id', $bz_id, EQUAL)));
      foreach ($r as $e) $e->_isLoaded= true;
      return $r;
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
     * Retrieves heredityfeature
     *
     * @return  int
     */
    public function getHeredityfeature() {
      return $this->heredityfeature;
    }
      
    /**
     * Sets heredityfeature
     *
     * @param   int heredityfeature
     * @return  int the previous value
     */
    public function setHeredityfeature($heredityfeature) {
      return $this->_change('heredityfeature', $heredityfeature);
    }

    /**
     * Retrieves heredity_history_id
     *
     * @return  int
     */
    public function getHeredity_history_id() {
      return $this->heredity_history_id;
    }
      
    /**
     * Sets heredity_history_id
     *
     * @param   int heredity_history_id
     * @return  int the previous value
     */
    public function setHeredity_history_id($heredity_history_id) {
      return $this->_change('heredity_history_id', $heredity_history_id);
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
     * Retrieves child_id
     *
     * @return  int
     */
    public function getChild_id() {
      return $this->child_id;
    }
      
    /**
     * Sets child_id
     *
     * @param   int child_id
     * @return  int the previous value
     */
    public function setChild_id($child_id) {
      return $this->_change('child_id', $child_id);
    }

    /**
     * Retrieves parent_person_id
     *
     * @return  int
     */
    public function getParent_person_id() {
      return $this->parent_person_id;
    }
      
    /**
     * Sets parent_person_id
     *
     * @param   int parent_person_id
     * @return  int the previous value
     */
    public function setParent_person_id($parent_person_id) {
      return $this->_change('parent_person_id', $parent_person_id);
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
     * Retrieves creation_date
     *
     * @return  util.Date
     */
    public function getCreation_date() {
      return $this->creation_date;
    }
      
    /**
     * Sets creation_date
     *
     * @param   util.Date creation_date
     * @return  util.Date the previous value
     */
    public function setCreation_date($creation_date) {
      return $this->_change('creation_date', $creation_date);
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
     * Retrieves parent_right_id
     *
     * @return  int
     */
    public function getParent_right_id() {
      return $this->parent_right_id;
    }
      
    /**
     * Sets parent_right_id
     *
     * @param   int parent_right_id
     * @return  int the previous value
     */
    public function setParent_right_id($parent_right_id) {
      return $this->_change('parent_right_id', $parent_right_id);
    }

    /**
     * Retrieves parent_right_type_id
     *
     * @return  int
     */
    public function getParent_right_type_id() {
      return $this->parent_right_type_id;
    }
      
    /**
     * Sets parent_right_type_id
     *
     * @param   int parent_right_type_id
     * @return  int the previous value
     */
    public function setParent_right_type_id($parent_right_type_id) {
      return $this->_change('parent_right_type_id', $parent_right_type_id);
    }

    /**
     * Retrieves parent_right_level_id
     *
     * @return  int
     */
    public function getParent_right_level_id() {
      return $this->parent_right_level_id;
    }
      
    /**
     * Sets parent_right_level_id
     *
     * @param   int parent_right_level_id
     * @return  int the previous value
     */
    public function setParent_right_level_id($parent_right_level_id) {
      return $this->_change('parent_right_level_id', $parent_right_level_id);
    }

    /**
     * Retrieves the Person entity
     * referenced by person_id=>child_id
     *
     * @return  de.schlund.db.methadon.Person entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getChild() {
      $r= XPClass::forName('de.schlund.db.methadon.Person')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('person_id', $this->getChild_id(), EQUAL)
      ));
      return $r ? $r[0] : NULL;
    }

    /**
     * Retrieves the Person entity
     * referenced by person_id=>child_id
     *
     * @return  de.schlund.db.methadon.Person entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getChild() {
      $r= XPClass::forName('de.schlund.db.methadon.Person')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('person_id', $this->getChild_id(), EQUAL)
      ));
      return $r ? $r[0] : NULL;
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
     * Retrieves the Person entity
     * referenced by person_id=>parent_person_id
     *
     * @return  de.schlund.db.methadon.Person entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getParent_person() {
      $r= XPClass::forName('de.schlund.db.methadon.Person')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('person_id', $this->getParent_person_id(), EQUAL)
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
     * Retrieves an array of all Plane_right_matrix entities referencing
     * this entity by heredity_history_id=>heredity_history_id
     *
     * @return  de.schlund.db.methadon.Plane_right_matrix[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getPlane_right_matrixHeredity_historyList() {
      return XPClass::forName('de.schlund.db.methadon.Plane_right_matrix')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('heredity_history_id', $this->getHeredity_history_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Plane_right_matrix entities referencing
     * this entity by heredity_history_id=>heredity_history_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Plane_right_matrix>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getPlane_right_matrixHeredity_historyIterator() {
      return XPClass::forName('de.schlund.db.methadon.Plane_right_matrix')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('heredity_history_id', $this->getHeredity_history_id(), EQUAL)
      ));
    }
  }
?>