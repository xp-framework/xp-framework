<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 52481 2007-01-16 11:26:17Z rdoebele $
 */
 
  uses('rdbms.DataSet');

  /**
   * Class wrapper for table bug_history, database METHADON
   * (Auto-generated on Wed, 04 Apr 2007 10:45:27 +0200 by ruben)
   *
   * @purpose  Datasource accessor
   */
  class Bug_history extends DataSet {

    protected
      $_isLoaded= false,
      $_loadCrit= NULL;

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('METHADON..bug_history');
        $peer->setConnection('sybintern');
        $peer->setIdentity('bughistory_id');
        $peer->setPrimary(array('bughistory_id'));
        $peer->setTypes(array(
          'subject'             => array('%s', FieldType::VARCHAR, FALSE),
          'url'                 => array('%s', FieldType::VARCHAR, TRUE),
          'changedby'           => array('%s', FieldType::VARCHAR, FALSE),
          'bughistory_id'       => array('%d', FieldType::NUMERIC, FALSE),
          'bug_id'              => array('%d', FieldType::NUMERIC, FALSE),
          'bugchannel_id'       => array('%d', FieldType::NUMERIC, FALSE),
          'reporter_id'         => array('%d', FieldType::NUMERIC, FALSE),
          'owner_id'            => array('%d', FieldType::NUMERIC, FALSE),
          'status_id'           => array('%d', FieldType::NUMERIC, FALSE),
          'severity_id'         => array('%d', FieldType::NUMERIC, FALSE),
          'priority'            => array('%d', FieldType::NUMERIC, FALSE),
          'bz_id'               => array('%d', FieldType::NUMERIC, FALSE),
          'comment'             => array('%s', FieldType::TEXT, TRUE),
          'param1'              => array('%s', FieldType::TEXT, TRUE),
          'param2'              => array('%s', FieldType::TEXT, TRUE),
          'param3'              => array('%s', FieldType::TEXT, TRUE),
          'lastchange'          => array('%s', FieldType::SMALLDATETIME, FALSE),
          'depend_id'           => array('%d', FieldType::NUMERICN, TRUE),
          'duplicate_id'        => array('%d', FieldType::NUMERICN, TRUE)
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
     * Gets an instance of this object by index "PK_bughistory"
     * 
     * @param   int bughistory_id
     * @return  de.schlund.db.methadon.Bug_history entitiy object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByBughistory_id($bughistory_id) {
      return new self(array(
        'bughistory_id'  => $bughistory_id,
        '_loadCrit' => new Criteria(array('bughistory_id', $bughistory_id, EQUAL))
      ));
    }

    /**
     * Retrieves subject
     *
     * @return  string
     */
    public function getSubject() {
      return $this->subject;
    }
      
    /**
     * Sets subject
     *
     * @param   string subject
     * @return  string the previous value
     */
    public function setSubject($subject) {
      return $this->_change('subject', $subject);
    }

    /**
     * Retrieves url
     *
     * @return  string
     */
    public function getUrl() {
      return $this->url;
    }
      
    /**
     * Sets url
     *
     * @param   string url
     * @return  string the previous value
     */
    public function setUrl($url) {
      return $this->_change('url', $url);
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
     * Retrieves bughistory_id
     *
     * @return  int
     */
    public function getBughistory_id() {
      return $this->bughistory_id;
    }
      
    /**
     * Sets bughistory_id
     *
     * @param   int bughistory_id
     * @return  int the previous value
     */
    public function setBughistory_id($bughistory_id) {
      return $this->_change('bughistory_id', $bughistory_id);
    }

    /**
     * Retrieves bug_id
     *
     * @return  int
     */
    public function getBug_id() {
      return $this->bug_id;
    }
      
    /**
     * Sets bug_id
     *
     * @param   int bug_id
     * @return  int the previous value
     */
    public function setBug_id($bug_id) {
      return $this->_change('bug_id', $bug_id);
    }

    /**
     * Retrieves bugchannel_id
     *
     * @return  int
     */
    public function getBugchannel_id() {
      return $this->bugchannel_id;
    }
      
    /**
     * Sets bugchannel_id
     *
     * @param   int bugchannel_id
     * @return  int the previous value
     */
    public function setBugchannel_id($bugchannel_id) {
      return $this->_change('bugchannel_id', $bugchannel_id);
    }

    /**
     * Retrieves reporter_id
     *
     * @return  int
     */
    public function getReporter_id() {
      return $this->reporter_id;
    }
      
    /**
     * Sets reporter_id
     *
     * @param   int reporter_id
     * @return  int the previous value
     */
    public function setReporter_id($reporter_id) {
      return $this->_change('reporter_id', $reporter_id);
    }

    /**
     * Retrieves owner_id
     *
     * @return  int
     */
    public function getOwner_id() {
      return $this->owner_id;
    }
      
    /**
     * Sets owner_id
     *
     * @param   int owner_id
     * @return  int the previous value
     */
    public function setOwner_id($owner_id) {
      return $this->_change('owner_id', $owner_id);
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
     * Retrieves priority
     *
     * @return  int
     */
    public function getPriority() {
      return $this->priority;
    }
      
    /**
     * Sets priority
     *
     * @param   int priority
     * @return  int the previous value
     */
    public function setPriority($priority) {
      return $this->_change('priority', $priority);
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
     * Retrieves param1
     *
     * @return  string
     */
    public function getParam1() {
      return $this->param1;
    }
      
    /**
     * Sets param1
     *
     * @param   string param1
     * @return  string the previous value
     */
    public function setParam1($param1) {
      return $this->_change('param1', $param1);
    }

    /**
     * Retrieves param2
     *
     * @return  string
     */
    public function getParam2() {
      return $this->param2;
    }
      
    /**
     * Sets param2
     *
     * @param   string param2
     * @return  string the previous value
     */
    public function setParam2($param2) {
      return $this->_change('param2', $param2);
    }

    /**
     * Retrieves param3
     *
     * @return  string
     */
    public function getParam3() {
      return $this->param3;
    }
      
    /**
     * Sets param3
     *
     * @param   string param3
     * @return  string the previous value
     */
    public function setParam3($param3) {
      return $this->_change('param3', $param3);
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
     * Retrieves depend_id
     *
     * @return  int
     */
    public function getDepend_id() {
      return $this->depend_id;
    }
      
    /**
     * Sets depend_id
     *
     * @param   int depend_id
     * @return  int the previous value
     */
    public function setDepend_id($depend_id) {
      return $this->_change('depend_id', $depend_id);
    }

    /**
     * Retrieves duplicate_id
     *
     * @return  int
     */
    public function getDuplicate_id() {
      return $this->duplicate_id;
    }
      
    /**
     * Sets duplicate_id
     *
     * @param   int duplicate_id
     * @return  int the previous value
     */
    public function setDuplicate_id($duplicate_id) {
      return $this->_change('duplicate_id', $duplicate_id);
    }

    /**
     * Retrieves the Bug entity
     * referenced by bug_id=>bug_id
     *
     * @return  de.schlund.db.methadon.Bug entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getBug() {
      $r= XPClass::forName('de.schlund.db.methadon.Bug')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('bug_id', $this->getBug_id(), EQUAL)
      ));
      return $r ? $r[0] : NULL;
    }

    /**
     * Retrieves the Person entity
     * referenced by person_id=>owner_id
     *
     * @return  de.schlund.db.methadon.Person entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getOwner() {
      $r= XPClass::forName('de.schlund.db.methadon.Person')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('person_id', $this->getOwner_id(), EQUAL)
      ));
      return $r ? $r[0] : NULL;
    }

    /**
     * Retrieves the Person entity
     * referenced by person_id=>owner_id
     *
     * @return  de.schlund.db.methadon.Person entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getOwner() {
      $r= XPClass::forName('de.schlund.db.methadon.Person')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('person_id', $this->getOwner_id(), EQUAL)
      ));
      return $r ? $r[0] : NULL;
    }

    /**
     * Retrieves the Person entity
     * referenced by person_id=>reporter_id
     *
     * @return  de.schlund.db.methadon.Person entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getReporter() {
      $r= XPClass::forName('de.schlund.db.methadon.Person')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('person_id', $this->getReporter_id(), EQUAL)
      ));
      return $r ? $r[0] : NULL;
    }

    /**
     * Retrieves the Bug_status entity
     * referenced by status_id=>status_id
     *
     * @return  de.schlund.db.methadon.Bug_status entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getStatus() {
      $r= XPClass::forName('de.schlund.db.methadon.Bug_status')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('status_id', $this->getStatus_id(), EQUAL)
      ));
      return $r ? $r[0] : NULL;
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
     * Retrieves the Bug_severity entity
     * referenced by severity_id=>severity_id
     *
     * @return  de.schlund.db.methadon.Bug_severity entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getSeverity() {
      $r= XPClass::forName('de.schlund.db.methadon.Bug_severity')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('severity_id', $this->getSeverity_id(), EQUAL)
      ));
      return $r ? $r[0] : NULL;
    }

    /**
     * Retrieves the Bug_channel entity
     * referenced by bugchannel_id=>bugchannel_id
     *
     * @return  de.schlund.db.methadon.Bug_channel entity
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getBugchannel() {
      $r= XPClass::forName('de.schlund.db.methadon.Bug_channel')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('bugchannel_id', $this->getBugchannel_id(), EQUAL)
      ));
      return $r ? $r[0] : NULL;
    }

    /**
     * Retrieves an array of all Bug_binaryhistory_matrix entities referencing
     * this entity by bughistory_id=>bughistory_id
     *
     * @return  de.schlund.db.methadon.Bug_binaryhistory_matrix[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getBug_binaryhistory_matrixBughistoryList() {
      return XPClass::forName('de.schlund.db.methadon.Bug_binaryhistory_matrix')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new Criteria(
          array('bughistory_id', $this->getBughistory_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Bug_binaryhistory_matrix entities referencing
     * this entity by bughistory_id=>bughistory_id
     *
     * @return  rdbms.ResultIterator<de.schlund.db.methadon.Bug_binaryhistory_matrix>
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getBug_binaryhistory_matrixBughistoryIterator() {
      return XPClass::forName('de.schlund.db.methadon.Bug_binaryhistory_matrix')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new Criteria(
          array('bughistory_id', $this->getBughistory_id(), EQUAL)
      ));
    }
  }
?>