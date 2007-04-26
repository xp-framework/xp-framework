<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet');

  /**
   * Class wrapper for table bug_status, database METHADON
   * (Auto-generated on Wed, 04 Apr 2007 10:45:27 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class Bug_status extends DataSet {

    protected
      $_isLoaded= false,
      $_loadCrit= NULL;

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('METHADON..bug_status');
        $peer->setConnection('sybintern');
        $peer->setPrimary(array('status_id'));
        $peer->setTypes(array(
          'name'                => array('%s', FieldType::VARCHAR, FALSE),
          'description'         => array('%s', FieldType::VARCHAR, TRUE),
          'rank'                => array('%d', FieldType::INT, FALSE),
          'status_id'           => array('%d', FieldType::NUMERIC, FALSE)
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
     * Gets an instance of this object by index "PK_bug_status"
     * 
     * @param   int status_id
     * @return  de.schlund.db.methadon.Bug_status entitiy object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByStatus_id($status_id) {
      return new self(array(
        'status_id'  => $status_id,
        '_loadCrit' => new Criteria(array('status_id', $status_id, EQUAL))
      ));
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
     * Retrieves rank
     *
     * @return  int
     */
    public function getRank() {
      return $this->rank;
    }
      
    /**
     * Sets rank
     *
     * @param   int rank
     * @return  int the previous value
     */
    public function setRank($rank) {
      return $this->_change('rank', $rank);
    }

    /**
     * Retrieves status_id
     *
     * @return  int
     */
    public function getStatus_id() {
      return $this->status_id;
    }
      
    /**
     * Sets status_id
     *
     * @param   int status_id
     * @return  int the previous value
     */
    public function setStatus_id($status_id) {
      return $this->_change('status_id', $status_id);
    }

    /**
     * Retrieves an array of all Bug_history entities referencing
     * this entity by status_id=>status_id
     *
     * @return  de.schlund.db.methadon.Bug_history[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getBug_historyStatusList() {
      return XPClass::forName('de.schlund.db.methadon.Bug_history')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('status_id', $this->getStatus_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Bug_history entities referencing
     * this entity by status_id=>status_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Bug_history>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getBug_historyStatusIterator() {
      return XPClass::forName('de.schlund.db.methadon.Bug_history')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('status_id', $this->getStatus_id(), EQUAL)
      ));
    }
  }
?>