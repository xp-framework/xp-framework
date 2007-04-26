<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet');

  /**
   * Class wrapper for table plane_person_category_matrix, database METHADON
   * (Auto-generated on Wed, 04 Apr 2007 10:45:27 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class Plane_person_category_matrix extends DataSet {

    protected
      $_isLoaded= false,
      $_loadCrit= NULL;

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('METHADON..plane_person_category_matrix');
        $peer->setConnection('sybintern');
        $peer->setIdentity('ppcm_id');
        $peer->setPrimary(array(''));
        $peer->setTypes(array(
          'recursion_depth'     => array('%d', FieldType::INT, FALSE),
          'ppcm_id'             => array('%d', FieldType::NUMERIC, FALSE),
          'category_id'         => array('%d', FieldType::NUMERIC, FALSE),
          'subgroup_id'         => array('%d', FieldType::NUMERIC, FALSE),
          'person_id'           => array('%d', FieldType::NUMERIC, FALSE)
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
     * Retrieves recursion_depth
     *
     * @return  int
     */
    public function getRecursion_depth() {
      return $this->recursion_depth;
    }
      
    /**
     * Sets recursion_depth
     *
     * @param   int recursion_depth
     * @return  int the previous value
     */
    public function setRecursion_depth($recursion_depth) {
      return $this->_change('recursion_depth', $recursion_depth);
    }

    /**
     * Retrieves ppcm_id
     *
     * @return  int
     */
    public function getPpcm_id() {
      return $this->ppcm_id;
    }
      
    /**
     * Sets ppcm_id
     *
     * @param   int ppcm_id
     * @return  int the previous value
     */
    public function setPpcm_id($ppcm_id) {
      return $this->_change('ppcm_id', $ppcm_id);
    }

    /**
     * Retrieves category_id
     *
     * @return  int
     */
    public function getCategory_id() {
      return $this->category_id;
    }
      
    /**
     * Sets category_id
     *
     * @param   int category_id
     * @return  int the previous value
     */
    public function setCategory_id($category_id) {
      return $this->_change('category_id', $category_id);
    }

    /**
     * Retrieves subgroup_id
     *
     * @return  int
     */
    public function getSubgroup_id() {
      return $this->subgroup_id;
    }
      
    /**
     * Sets subgroup_id
     *
     * @param   int subgroup_id
     * @return  int the previous value
     */
    public function setSubgroup_id($subgroup_id) {
      return $this->_change('subgroup_id', $subgroup_id);
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
     * referenced by person_id=>category_id
     *
     * @return  de.schlund.db.methadon.Person entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getCategory() {
      $r= XPClass::forName('de.schlund.db.methadon.Person')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('person_id', $this->getCategory_id(), EQUAL)
      ));
      return $r ? $r[0] : NULL;
    }

    /**
     * Retrieves the Person entity
     * referenced by person_id=>subgroup_id
     *
     * @return  de.schlund.db.methadon.Person entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getSubgroup() {
      $r= XPClass::forName('de.schlund.db.methadon.Person')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('person_id', $this->getSubgroup_id(), EQUAL)
      ));
      return $r ? $r[0] : NULL;
    }
  }
?>