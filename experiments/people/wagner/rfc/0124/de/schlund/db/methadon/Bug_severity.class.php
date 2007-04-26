<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet');

  /**
   * Class wrapper for table bug_severity, database METHADON
   * (Auto-generated on Wed, 04 Apr 2007 10:45:27 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class Bug_severity extends DataSet {

    protected
      $_isLoaded= false,
      $_loadCrit= NULL;

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('METHADON..bug_severity');
        $peer->setConnection('sybintern');
        $peer->setPrimary(array('severity_id'));
        $peer->setTypes(array(
          'name'                => array('%s', FieldType::VARCHAR, FALSE),
          'description'         => array('%s', FieldType::VARCHAR, TRUE),
          'rank'                => array('%d', FieldType::INT, FALSE),
          'severity_id'         => array('%d', FieldType::NUMERIC, FALSE)
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
     * Gets an instance of this object by index "PK_bug_severity"
     * 
     * @param   int severity_id
     * @return  de.schlund.db.methadon.Bug_severity entitiy object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getBySeverity_id($severity_id) {
      return new self(array(
        'severity_id'  => $severity_id,
        '_loadCrit' => new Criteria(array('severity_id', $severity_id, EQUAL))
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
     * Retrieves severity_id
     *
     * @return  int
     */
    public function getSeverity_id() {
      return $this->severity_id;
    }
      
    /**
     * Sets severity_id
     *
     * @param   int severity_id
     * @return  int the previous value
     */
    public function setSeverity_id($severity_id) {
      return $this->_change('severity_id', $severity_id);
    }

    /**
     * Retrieves an array of all Bug_history entities referencing
     * this entity by severity_id=>severity_id
     *
     * @return  de.schlund.db.methadon.Bug_history[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getBug_historySeverityList() {
      return XPClass::forName('de.schlund.db.methadon.Bug_history')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('severity_id', $this->getSeverity_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Bug_history entities referencing
     * this entity by severity_id=>severity_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Bug_history>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getBug_historySeverityIterator() {
      return XPClass::forName('de.schlund.db.methadon.Bug_history')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('severity_id', $this->getSeverity_id(), EQUAL)
      ));
    }
  }
?>