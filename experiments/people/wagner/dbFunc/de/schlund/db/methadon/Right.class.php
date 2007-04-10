<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet');

  /**
   * Class wrapper for table right, database METHADON
   * (Auto-generated on Wed, 04 Apr 2007 10:45:27 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class Right extends DataSet {

    protected
      $_isLoaded= false,
      $_loadCrit= NULL;

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('METHADON..right');
        $peer->setConnection('sybintern');
        $peer->setIdentity('right_id');
        $peer->setPrimary(array('right_id'));
        $peer->setTypes(array(
          'name'                => array('%s', FieldType::VARCHAR, FALSE),
          'description'         => array('%s', FieldType::VARCHAR, FALSE),
          'changedby'           => array('%s', FieldType::VARCHAR, FALSE),
          'right_id'            => array('%d', FieldType::NUMERIC, FALSE),
          'bz_id'               => array('%d', FieldType::NUMERIC, FALSE),
          'lastchange'          => array('%s', FieldType::DATETIME, FALSE),
          'right_level_id'      => array('%d', FieldType::NUMERICN, TRUE),
          'right_type_id'       => array('%d', FieldType::NUMERICN, TRUE)
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
     * Gets an instance of this object by index "PK_RIGHT"
     * 
     * @param   int right_id
     * @return  de.schlund.db.methadon.Right entitiy object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByRight_id($right_id) {
      return new self(array(
        'right_id'  => $right_id,
        '_loadCrit' => new Criteria(array('right_id', $right_id, EQUAL))
      ));
    }

    /**
     * Gets an instance of this object by index "RIGHT_NAME_I"
     * 
     * @param   string name
     * @return  de.schlund.db.methadon.Right[] entity objects
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByName($name) {
      $r= self::getPeer()->doSelect(new Criteria(array('name', $name, EQUAL)));
      foreach ($r as $e) $e->_isLoaded= true;
      return $r;
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
     * Retrieves the Right_type entity
     * referenced by right_type_id=>right_type_id
     *
     * @return  de.schlund.db.methadon.Right_type entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getRight_type() {
      $r= XPClass::forName('de.schlund.db.methadon.Right_type')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('right_type_id', $this->getRight_type_id(), EQUAL)
      ));
      return $r ? $r[0] : NULL;
    }

    /**
     * Retrieves an array of all Plane_right_matrix entities referencing
     * this entity by right_id=>right_id
     *
     * @return  de.schlund.db.methadon.Plane_right_matrix[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getPlane_right_matrixRightList() {
      return XPClass::forName('de.schlund.db.methadon.Plane_right_matrix')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('right_id', $this->getRight_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Plane_right_matrix entities referencing
     * this entity by right_id=>right_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Plane_right_matrix>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getPlane_right_matrixRightIterator() {
      return XPClass::forName('de.schlund.db.methadon.Plane_right_matrix')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('right_id', $this->getRight_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Abstract_right_matrix entities referencing
     * this entity by right_id=>right_id
     *
     * @return  de.schlund.db.methadon.Abstract_right_matrix[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getAbstract_right_matrixRightList() {
      return XPClass::forName('de.schlund.db.methadon.Abstract_right_matrix')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('right_id', $this->getRight_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Abstract_right_matrix entities referencing
     * this entity by right_id=>right_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Abstract_right_matrix>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getAbstract_right_matrixRightIterator() {
      return XPClass::forName('de.schlund.db.methadon.Abstract_right_matrix')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('right_id', $this->getRight_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Abstract_right_matrix entities referencing
     * this entity by right_id=>right_id
     *
     * @return  de.schlund.db.methadon.Abstract_right_matrix[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getAbstract_right_matrixRightList() {
      return XPClass::forName('de.schlund.db.methadon.Abstract_right_matrix')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('right_id', $this->getRight_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Abstract_right_matrix entities referencing
     * this entity by right_id=>right_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Abstract_right_matrix>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getAbstract_right_matrixRightIterator() {
      return XPClass::forName('de.schlund.db.methadon.Abstract_right_matrix')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('right_id', $this->getRight_id(), EQUAL)
      ));
    }
  }
?>