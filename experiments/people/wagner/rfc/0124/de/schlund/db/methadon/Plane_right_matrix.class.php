<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet');

  /**
   * Class wrapper for table plane_right_matrix, database METHADON
   * (Auto-generated on Wed, 04 Apr 2007 10:45:27 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class Plane_right_matrix extends DataSet {

    protected
      $_isLoaded= false,
      $_loadCrit= NULL;

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('METHADON..plane_right_matrix');
        $peer->setConnection('sybintern');
        $peer->setPrimary(array('heredity_history_id', 'right_id', 'person_id'));
        $peer->setTypes(array(
          'heredityfeature'     => array('%d', FieldType::INT, FALSE),
          'heredity_history_id' => array('%d', FieldType::NUMERIC, FALSE),
          'right_id'            => array('%d', FieldType::NUMERIC, FALSE),
          'person_id'           => array('%d', FieldType::NUMERIC, FALSE),
          'right_type_id'       => array('%d', FieldType::NUMERIC, FALSE),
          'right_level_id'      => array('%d', FieldType::NUMERIC, FALSE)
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
     * Gets an instance of this object by index "PLANE_RIGHT_MATRIX_I1"
     * 
     * @param   int person_id
     * @return  de.schlund.db.methadon.Plane_right_matrix[] entity objects
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByPerson_id($person_id) {
      $r= self::getPeer()->doSelect(new Criteria(array('person_id', $person_id, EQUAL)));
      foreach ($r as $e) $e->_isLoaded= true;
      return $r;
    }

    /**
     * Gets an instance of this object by index "bla_tmp_index"
     * 
     * @param   int right_id
     * @return  de.schlund.db.methadon.Plane_right_matrix[] entity objects
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByRight_id($right_id) {
      $r= self::getPeer()->doSelect(new Criteria(array('right_id', $right_id, EQUAL)));
      foreach ($r as $e) $e->_isLoaded= true;
      return $r;
    }

    /**
     * Gets an instance of this object by index "PCM_HH_ID"
     * 
     * @param   int heredity_history_id
     * @return  de.schlund.db.methadon.Plane_right_matrix[] entity objects
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByHeredity_history_id($heredity_history_id) {
      $r= self::getPeer()->doSelect(new Criteria(array('heredity_history_id', $heredity_history_id, EQUAL)));
      foreach ($r as $e) $e->_isLoaded= true;
      return $r;
    }

    /**
     * Gets an instance of this object by index "PK_plane_right_matrix"
     * 
     * @param   int heredity_history_id
     * @param   int right_id
     * @param   int person_id
     * @return  de.schlund.db.methadon.Plane_right_matrix entitiy object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByHeredity_history_idRight_idPerson_id($heredity_history_id, $right_id, $person_id) {
      return new self(array(
        'heredity_history_id'  => $heredity_history_id,
        'right_id'  => $right_id,
        'person_id'  => $person_id,
        '_loadCrit' => new Criteria(
          array('heredity_history_id', $heredity_history_id, EQUAL),
          array('right_id', $right_id, EQUAL),
          array('person_id', $person_id, EQUAL)
        )
      ));
    }

    /**
     * Gets an instance of this object by index "plan_right_I1"
     * 
     * @param   int right_type_id
     * @return  de.schlund.db.methadon.Plane_right_matrix[] entity objects
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByRight_type_id($right_type_id) {
      $r= self::getPeer()->doSelect(new Criteria(array('right_type_id', $right_type_id, EQUAL)));
      foreach ($r as $e) $e->_isLoaded= true;
      return $r;
    }

    /**
     * Gets an instance of this object by index "plan_right_I2"
     * 
     * @param   int right_level_id
     * @return  de.schlund.db.methadon.Plane_right_matrix[] entity objects
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByRight_level_id($right_level_id) {
      $r= self::getPeer()->doSelect(new Criteria(array('right_level_id', $right_level_id, EQUAL)));
      foreach ($r as $e) $e->_isLoaded= true;
      return $r;
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
     * Retrieves right_id
     *
     * @return  int
     */
    public function getRight_id() {
      return $this->right_id;
    }
      
    /**
     * Sets right_id
     *
     * @param   int right_id
     * @return  int the previous value
     */
    public function setRight_id($right_id) {
      return $this->_change('right_id', $right_id);
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
     * Retrieves right_type_id
     *
     * @return  int
     */
    public function getRight_type_id() {
      return $this->right_type_id;
    }
      
    /**
     * Sets right_type_id
     *
     * @param   int right_type_id
     * @return  int the previous value
     */
    public function setRight_type_id($right_type_id) {
      return $this->_change('right_type_id', $right_type_id);
    }

    /**
     * Retrieves right_level_id
     *
     * @return  int
     */
    public function getRight_level_id() {
      return $this->right_level_id;
    }
      
    /**
     * Sets right_level_id
     *
     * @param   int right_level_id
     * @return  int the previous value
     */
    public function setRight_level_id($right_level_id) {
      return $this->_change('right_level_id', $right_level_id);
    }

    /**
     * Retrieves the Right entity
     * referenced by right_id=>right_id
     *
     * @return  de.schlund.db.methadon.Right entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getRight() {
      $r= XPClass::forName('de.schlund.db.methadon.Right')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('right_id', $this->getRight_id(), EQUAL)
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
     * Retrieves the Heredity_history entity
     * referenced by heredity_history_id=>heredity_history_id
     *
     * @return  de.schlund.db.methadon.Heredity_history entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getHeredity_history() {
      $r= XPClass::forName('de.schlund.db.methadon.Heredity_history')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('heredity_history_id', $this->getHeredity_history_id(), EQUAL)
      ));
      return $r ? $r[0] : NULL;
    }
  }
?>