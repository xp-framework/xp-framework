<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet');

  /**
   * Class wrapper for table right_todo, database METHADON
   * (Auto-generated on Wed, 04 Apr 2007 10:45:27 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class Right_todo extends DataSet {

    protected
      $_isLoaded= false,
      $_loadCrit= NULL;

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('METHADON..right_todo');
        $peer->setConnection('sybintern');
        $peer->setIdentity('right_todo_id');
        $peer->setPrimary(array('right_todo_id'));
        $peer->setTypes(array(
          'status'              => array('%d', FieldType::INT, FALSE),
          'right_todo_id'       => array('%d', FieldType::NUMERIC, FALSE),
          'person_id'           => array('%d', FieldType::NUMERIC, FALSE),
          'right_id'            => array('%d', FieldType::NUMERIC, FALSE),
          'right_type_id'       => array('%d', FieldType::NUMERIC, FALSE),
          'right_level_id'      => array('%d', FieldType::NUMERIC, FALSE),
          'creation_date'       => array('%s', FieldType::DATETIME, FALSE)
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
     * Gets an instance of this object by index "PK_right_todo"
     * 
     * @param   int right_todo_id
     * @return  de.schlund.db.methadon.Right_todo entitiy object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByRight_todo_id($right_todo_id) {
      return new self(array(
        'right_todo_id'  => $right_todo_id,
        '_loadCrit' => new Criteria(array('right_todo_id', $right_todo_id, EQUAL))
      ));
    }

    /**
     * Retrieves status
     *
     * @return  int
     */
    public function getStatus() {
      return $this->status;
    }
      
    /**
     * Sets status
     *
     * @param   int status
     * @return  int the previous value
     */
    public function setStatus($status) {
      return $this->_change('status', $status);
    }

    /**
     * Retrieves right_todo_id
     *
     * @return  int
     */
    public function getRight_todo_id() {
      return $this->right_todo_id;
    }
      
    /**
     * Sets right_todo_id
     *
     * @param   int right_todo_id
     * @return  int the previous value
     */
    public function setRight_todo_id($right_todo_id) {
      return $this->_change('right_todo_id', $right_todo_id);
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
  }
?>