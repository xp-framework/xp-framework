<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet');

  /**
   * Class wrapper for table pim_todo, database METHADON
   * (Auto-generated on Wed, 04 Apr 2007 10:45:27 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class Pim_todo extends DataSet {

    protected
      $_isLoaded= false,
      $_loadCrit= NULL;

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('METHADON..pim_todo');
        $peer->setConnection('sybintern');
        $peer->setIdentity('todo_id');
        $peer->setPrimary(array('todo_id'));
        $peer->setTypes(array(
          'description'         => array('%s', FieldType::VARCHAR, FALSE),
          'todo_id'             => array('%d', FieldType::NUMERIC, FALSE),
          'person_id'           => array('%d', FieldType::NUMERIC, FALSE),
          'priority_id'         => array('%d', FieldType::NUMERIC, FALSE),
          'type_id'             => array('%d', FieldType::NUMERIC, FALSE),
          'done'                => array('%d', FieldType::INTN, TRUE),
          'deadline'            => array('%s', FieldType::DATETIMN, TRUE),
          'depends_on'          => array('%d', FieldType::NUMERICN, TRUE)
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
     * Gets an instance of this object by index "PK_TODO"
     * 
     * @param   int todo_id
     * @return  de.schlund.db.methadon.Pim_todo entitiy object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByTodo_id($todo_id) {
      return new self(array(
        'todo_id'  => $todo_id,
        '_loadCrit' => new Criteria(array('todo_id', $todo_id, EQUAL))
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
     * Retrieves todo_id
     *
     * @return  int
     */
    public function getTodo_id() {
      return $this->todo_id;
    }
      
    /**
     * Sets todo_id
     *
     * @param   int todo_id
     * @return  int the previous value
     */
    public function setTodo_id($todo_id) {
      return $this->_change('todo_id', $todo_id);
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
     * Retrieves priority_id
     *
     * @return  int
     */
    public function getPriority_id() {
      return $this->priority_id;
    }
      
    /**
     * Sets priority_id
     *
     * @param   int priority_id
     * @return  int the previous value
     */
    public function setPriority_id($priority_id) {
      return $this->_change('priority_id', $priority_id);
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
     * Retrieves done
     *
     * @return  int
     */
    public function getDone() {
      return $this->done;
    }
      
    /**
     * Sets done
     *
     * @param   int done
     * @return  int the previous value
     */
    public function setDone($done) {
      return $this->_change('done', $done);
    }

    /**
     * Retrieves deadline
     *
     * @return  util.Date
     */
    public function getDeadline() {
      return $this->deadline;
    }
      
    /**
     * Sets deadline
     *
     * @param   util.Date deadline
     * @return  util.Date the previous value
     */
    public function setDeadline($deadline) {
      return $this->_change('deadline', $deadline);
    }

    /**
     * Retrieves depends_on
     *
     * @return  int
     */
    public function getDepends_on() {
      return $this->depends_on;
    }
      
    /**
     * Sets depends_on
     *
     * @param   int depends_on
     * @return  int the previous value
     */
    public function setDepends_on($depends_on) {
      return $this->_change('depends_on', $depends_on);
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
     * Retrieves the Priority entity
     * referenced by priority_id=>priority_id
     *
     * @return  de.schlund.db.methadon.Priority entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getPriority() {
      $r= XPClass::forName('de.schlund.db.methadon.Priority')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('priority_id', $this->getPriority_id(), EQUAL)
      ));
      return $r ? $r[0] : NULL;
    }
  }
?>