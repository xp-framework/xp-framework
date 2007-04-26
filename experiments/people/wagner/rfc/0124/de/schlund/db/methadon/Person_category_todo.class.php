<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet');

  /**
   * Class wrapper for table person_category_todo, database METHADON
   * (Auto-generated on Wed, 04 Apr 2007 10:45:27 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class Person_category_todo extends DataSet {

    protected
      $_isLoaded= false,
      $_loadCrit= NULL;

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('METHADON..person_category_todo');
        $peer->setConnection('sybintern');
        $peer->setIdentity('revision');
        $peer->setPrimary(array('revision'));
        $peer->setTypes(array(
          'changedby'           => array('%s', FieldType::VARCHAR, FALSE),
          'action'              => array('%d', FieldType::INT, FALSE),
          'revision'            => array('%d', FieldType::NUMERIC, FALSE),
          'person_id'           => array('%d', FieldType::NUMERIC, FALSE),
          'category_id'         => array('%d', FieldType::NUMERIC, FALSE),
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
     * Gets an instance of this object by index "PK_PERSON_CATEGORY_TODO"
     * 
     * @param   int revision
     * @return  de.schlund.db.methadon.Person_category_todo entitiy object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByRevision($revision) {
      return new self(array(
        'revision'  => $revision,
        '_loadCrit' => new Criteria(array('revision', $revision, EQUAL))
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
     * Retrieves action
     *
     * @return  int
     */
    public function getAction() {
      return $this->action;
    }
      
    /**
     * Sets action
     *
     * @param   int action
     * @return  int the previous value
     */
    public function setAction($action) {
      return $this->_change('action', $action);
    }

    /**
     * Retrieves revision
     *
     * @return  int
     */
    public function getRevision() {
      return $this->revision;
    }
      
    /**
     * Sets revision
     *
     * @param   int revision
     * @return  int the previous value
     */
    public function setRevision($revision) {
      return $this->_change('revision', $revision);
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
     * Retrieves the Category entity
     * referenced by category_id=>category_id
     *
     * @return  de.schlund.db.methadon.Category entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getCategory() {
      $r= XPClass::forName('de.schlund.db.methadon.Category')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('category_id', $this->getCategory_id(), EQUAL)
      ));
      return $r ? $r[0] : NULL;
    }
  }
?>