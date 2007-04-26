<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet');

  /**
   * Class wrapper for table person_todo, database METHADON
   * (Auto-generated on Wed, 04 Apr 2007 10:45:27 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class Person_todo extends DataSet {

    protected
      $_isLoaded= false,
      $_loadCrit= NULL;

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('METHADON..person_todo');
        $peer->setConnection('sybintern');
        $peer->setIdentity('person_todo_id');
        $peer->setPrimary(array('person_todo_id'));
        $peer->setTypes(array(
          'changedby'           => array('%s', FieldType::VARCHAR, FALSE),
          'person_todo_id'      => array('%d', FieldType::NUMERIC, FALSE),
          'history_id'          => array('%d', FieldType::NUMERIC, FALSE),
          'person_todo_type_id' => array('%d', FieldType::NUMERIC, FALSE),
          'tool_id'             => array('%d', FieldType::NUMERIC, FALSE),
          'person_id'           => array('%d', FieldType::NUMERIC, FALSE),
          'lastchange'          => array('%s', FieldType::DATETIME, FALSE)
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
     * Gets an instance of this object by index "PK_PERSON_TODO"
     * 
     * @param   int person_todo_id
     * @return  de.schlund.db.methadon.Person_todo entitiy object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByPerson_todo_id($person_todo_id) {
      return new self(array(
        'person_todo_id'  => $person_todo_id,
        '_loadCrit' => new Criteria(array('person_todo_id', $person_todo_id, EQUAL))
      ));
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
     * Retrieves person_todo_id
     *
     * @return  int
     */
    public function getPerson_todo_id() {
      return $this->person_todo_id;
    }
      
    /**
     * Sets person_todo_id
     *
     * @param   int person_todo_id
     * @return  int the previous value
     */
    public function setPerson_todo_id($person_todo_id) {
      return $this->_change('person_todo_id', $person_todo_id);
    }

    /**
     * Retrieves history_id
     *
     * @return  int
     */
    public function getHistory_id() {
      return $this->history_id;
    }
      
    /**
     * Sets history_id
     *
     * @param   int history_id
     * @return  int the previous value
     */
    public function setHistory_id($history_id) {
      return $this->_change('history_id', $history_id);
    }

    /**
     * Retrieves person_todo_type_id
     *
     * @return  int
     */
    public function getPerson_todo_type_id() {
      return $this->person_todo_type_id;
    }
      
    /**
     * Sets person_todo_type_id
     *
     * @param   int person_todo_type_id
     * @return  int the previous value
     */
    public function setPerson_todo_type_id($person_todo_type_id) {
      return $this->_change('person_todo_type_id', $person_todo_type_id);
    }

    /**
     * Retrieves tool_id
     *
     * @return  int
     */
    public function getTool_id() {
      return $this->tool_id;
    }
      
    /**
     * Sets tool_id
     *
     * @param   int tool_id
     * @return  int the previous value
     */
    public function setTool_id($tool_id) {
      return $this->_change('tool_id', $tool_id);
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
     * Retrieves the Person entity
     * referenced by person_id=>tool_id
     *
     * @return  de.schlund.db.methadon.Person entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getTool() {
      $r= XPClass::forName('de.schlund.db.methadon.Person')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('person_id', $this->getTool_id(), EQUAL)
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
     * Retrieves the Person_todo_type entity
     * referenced by person_todo_type_id=>person_todo_type_id
     *
     * @return  de.schlund.db.methadon.Person_todo_type entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getPerson_todo_type() {
      $r= XPClass::forName('de.schlund.db.methadon.Person_todo_type')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('person_todo_type_id', $this->getPerson_todo_type_id(), EQUAL)
      ));
      return $r ? $r[0] : NULL;
    }
  }
?>