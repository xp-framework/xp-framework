<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet');

  /**
   * Class wrapper for table toilette, database Ruben_Test_PS
   * (Auto-generated on Wed, 04 Apr 2007 10:45:30 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class RubentestToilette extends DataSet {

    protected
      $_isLoaded= false,
      $_loadCrit= NULL;

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('Ruben_Test_PS.toilette');
        $peer->setConnection('localhost');
        $peer->setIdentity('toilette_id');
        $peer->setPrimary(array('toilette_id'));
        $peer->setTypes(array(
          'toilette_id'         => array('%d', FieldType::INT, FALSE),
          'person_id'           => array('%d', FieldType::INT, TRUE)
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
     * Gets an instance of this object by index "PRIMARY"
     * 
     * @param   int toilette_id
     * @return  de.schlund.db.rubentest.RubentestToilette entitiy object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByToilette_id($toilette_id) {
      return new self(array(
        'toilette_id'  => $toilette_id,
        '_loadCrit' => new Criteria(array('toilette_id', $toilette_id, EQUAL))
      ));
    }

    /**
     * Gets an instance of this object by index "person_id"
     * 
     * @param   int person_id
     * @return  de.schlund.db.rubentest.RubentestToilette entitiy object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByPerson_id($person_id) {
      return new self(array(
        'person_id'  => $person_id,
        '_loadCrit' => new Criteria(array('person_id', $person_id, EQUAL))
      ));
    }

    /**
     * Retrieves toilette_id
     *
     * @return  int
     */
    public function getToilette_id() {
      return $this->toilette_id;
    }
      
    /**
     * Sets toilette_id
     *
     * @param   int toilette_id
     * @return  int the previous value
     */
    public function setToilette_id($toilette_id) {
      return $this->_change('toilette_id', $toilette_id);
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
     * @return  de.schlund.db.rubentest.RubentestPerson entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getPerson() {
      $r= XPClass::forName('de.schlund.db.rubentest.RubentestPerson')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('person_id', $this->getPerson_id(), EQUAL)
      ));
      return $r ? $r[0] : NULL;
    }
  }
?>