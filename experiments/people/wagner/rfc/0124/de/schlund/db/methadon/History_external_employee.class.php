<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet');

  /**
   * Class wrapper for table history_external_employee, database METHADON
   * (Auto-generated on Wed, 04 Apr 2007 10:45:27 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class History_external_employee extends DataSet {

    protected
      $_isLoaded= false,
      $_loadCrit= NULL;

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('METHADON..history_external_employee');
        $peer->setConnection('sybintern');
        $peer->setIdentity('history_external_employee_id');
        $peer->setPrimary(array(''));
        $peer->setTypes(array(
          'company'             => array('%s', FieldType::VARCHAR, TRUE),
          'location'            => array('%s', FieldType::VARCHAR, TRUE),
          'comment'             => array('%s', FieldType::VARCHAR, TRUE),
          'changedby'           => array('%s', FieldType::VARCHAR, FALSE),
          'history_external_employee_id' => array('%d', FieldType::NUMERIC, FALSE),
          'external_employee_id' => array('%d', FieldType::NUMERIC, FALSE),
          'officer_id'          => array('%d', FieldType::NUMERIC, FALSE),
          'bz_id'               => array('%d', FieldType::NUMERIC, FALSE),
          'lastchange'          => array('%s', FieldType::DATETIME, FALSE),
          'department_id'       => array('%d', FieldType::NUMERICN, TRUE)
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
     * Retrieves company
     *
     * @return  string
     */
    public function getCompany() {
      return $this->company;
    }
      
    /**
     * Sets company
     *
     * @param   string company
     * @return  string the previous value
     */
    public function setCompany($company) {
      return $this->_change('company', $company);
    }

    /**
     * Retrieves location
     *
     * @return  string
     */
    public function getLocation() {
      return $this->location;
    }
      
    /**
     * Sets location
     *
     * @param   string location
     * @return  string the previous value
     */
    public function setLocation($location) {
      return $this->_change('location', $location);
    }

    /**
     * Retrieves comment
     *
     * @return  string
     */
    public function getComment() {
      return $this->comment;
    }
      
    /**
     * Sets comment
     *
     * @param   string comment
     * @return  string the previous value
     */
    public function setComment($comment) {
      return $this->_change('comment', $comment);
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
     * Retrieves history_external_employee_id
     *
     * @return  int
     */
    public function getHistory_external_employee_id() {
      return $this->history_external_employee_id;
    }
      
    /**
     * Sets history_external_employee_id
     *
     * @param   int history_external_employee_id
     * @return  int the previous value
     */
    public function setHistory_external_employee_id($history_external_employee_id) {
      return $this->_change('history_external_employee_id', $history_external_employee_id);
    }

    /**
     * Retrieves external_employee_id
     *
     * @return  int
     */
    public function getExternal_employee_id() {
      return $this->external_employee_id;
    }
      
    /**
     * Sets external_employee_id
     *
     * @param   int external_employee_id
     * @return  int the previous value
     */
    public function setExternal_employee_id($external_employee_id) {
      return $this->_change('external_employee_id', $external_employee_id);
    }

    /**
     * Retrieves officer_id
     *
     * @return  int
     */
    public function getOfficer_id() {
      return $this->officer_id;
    }
      
    /**
     * Sets officer_id
     *
     * @param   int officer_id
     * @return  int the previous value
     */
    public function setOfficer_id($officer_id) {
      return $this->_change('officer_id', $officer_id);
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
     * Retrieves department_id
     *
     * @return  int
     */
    public function getDepartment_id() {
      return $this->department_id;
    }
      
    /**
     * Sets department_id
     *
     * @param   int department_id
     * @return  int the previous value
     */
    public function setDepartment_id($department_id) {
      return $this->_change('department_id', $department_id);
    }
  }
?>