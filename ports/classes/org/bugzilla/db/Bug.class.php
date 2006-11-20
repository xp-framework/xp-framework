<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses('rdbms.DataSet');
 
  /**
   * Class wrapper for table bugs, database bugs
   * (Auto-generated on Tue,  7 Jun 2005 11:58:02 +0200 by clang)
   *
   * @purpose  Datasource accessor
   */
  class Bug extends DataSet {
    var
      $bug_id             = 0,
      $assigned_to        = 0,
      $bug_file_loc       = NULL,
      $bug_severity       = '',
      $bug_status         = '',
      $creation_ts        = NULL,
      $delta_ts           = NULL,
      $short_desc         = '',
      $op_sys             = '',
      $priority           = '',
      $rep_platform       = NULL,
      $reporter           = 0,
      $version            = '',
      $resolution         = '',
      $target_milestone   = '',
      $qa_contact         = 0,
      $status_whiteboard  = '',
      $votes              = 0,
      $keywords           = '',
      $lastdiffed         = NULL,
      $everconfirmed      = 0,
      $reporter_accessible= 0,
      $cclist_accessible  = 0,
      $estimated_time     = '',
      $remaining_time     = '',
      $alias              = NULL,
      $product_id         = '',
      $component_id       = '';

    /**
     * Static initializer
     *
     * @model   static
     * @access  public
     */
    function __static() { 
      with ($peer= &Bug::getPeer()); {
        $peer->setTable('bugs');
        $peer->setConnection('bugzilla');
        $peer->setIdentity('bug_id');
        $peer->setPrimary(array('bug_id'));
        $peer->setTypes(array(
          'bug_id'              => '%d',
          'assigned_to'         => '%d',
          'bug_file_loc'        => '%s',
          'bug_severity'        => '%s',
          'bug_status'          => '%s',
          'creation_ts'         => '%s',
          'delta_ts'            => '%s',
          'short_desc'          => '%s',
          'op_sys'              => '%s',
          'priority'            => '%s',
          'rep_platform'        => '%s',
          'reporter'            => '%d',
          'version'             => '%s',
          'resolution'          => '%s',
          'target_milestone'    => '%s',
          'qa_contact'          => '%d',
          'status_whiteboard'   => '%s',
          'votes'               => '%d',
          'keywords'            => '%s',
          'lastdiffed'          => '%s',
          'everconfirmed'       => '%d',
          'reporter_accessible' => '%d',
          'cclist_accessible'   => '%d',
          'estimated_time'      => '%s',
          'remaining_time'      => '%s',
          'alias'               => '%s',
          'product_id'          => '%s',
          'component_id'        => '%s'
        ));
      }
    }  
  
    /**
     * Retrieve associated peer
     *
     * @access  public
     * @return  &rdbms.Peer
     */
    function &getPeer() {
      return Peer::forName(__CLASS__);
    }
  
    /**
     * Gets an instance of this object by index "PRIMARY"
     *
     * @access  static
     * @param   int bug_id
     * @return  &org.bugzilla.db.Bug object
     * @throws  rdbms.SQLException in case an error occurs
     */
    function &getByBug_id($bug_id) {
      $peer= &Bug::getPeer();
      return array_shift($peer->doSelect(new Criteria(array('bug_id', $bug_id, EQUAL))));
    }

    /**
     * Gets an instance of this object by index "alias"
     *
     * @access  static
     * @param   string alias
     * @return  &org.bugzilla.db.Bug object
     * @throws  rdbms.SQLException in case an error occurs
     */
    function &getByAlias($alias) {
      $peer= &Bug::getPeer();
      return array_shift($peer->doSelect(new Criteria(array('alias', $alias, EQUAL))));
    }

    /**
     * Gets an instance of this object by index "assigned_to"
     *
     * @access  static
     * @param   int assigned_to
     * @return  &org.bugzilla.db.Bug[] object
     * @throws  rdbms.SQLException in case an error occurs
     */
    function &getByAssigned_to($assigned_to) {
      $peer= &Bug::getPeer();
      return $peer->doSelect(new Criteria(array('assigned_to', $assigned_to, EQUAL)));
    }

    /**
     * Gets an instance of this object by index "creation_ts"
     *
     * @access  static
     * @param   util.Date creation_ts
     * @return  &org.bugzilla.db.Bug[] object
     * @throws  rdbms.SQLException in case an error occurs
     */
    function &getByCreation_ts($creation_ts) {
      $peer= &Bug::getPeer();
      return $peer->doSelect(new Criteria(array('creation_ts', $creation_ts, EQUAL)));
    }

    /**
     * Gets an instance of this object by index "delta_ts"
     *
     * @access  static
     * @param   util.Date delta_ts
     * @return  &org.bugzilla.db.Bug[] object
     * @throws  rdbms.SQLException in case an error occurs
     */
    function &getByDelta_ts($delta_ts) {
      $peer= &Bug::getPeer();
      return $peer->doSelect(new Criteria(array('delta_ts', $delta_ts, EQUAL)));
    }

    /**
     * Gets an instance of this object by index "bug_severity"
     *
     * @access  static
     * @param   string bug_severity
     * @return  &org.bugzilla.db.Bug[] object
     * @throws  rdbms.SQLException in case an error occurs
     */
    function &getByBug_severity($bug_severity) {
      $peer= &Bug::getPeer();
      return $peer->doSelect(new Criteria(array('bug_severity', $bug_severity, EQUAL)));
    }

    /**
     * Gets an instance of this object by index "bug_status"
     *
     * @access  static
     * @param   string bug_status
     * @return  &org.bugzilla.db.Bug[] object
     * @throws  rdbms.SQLException in case an error occurs
     */
    function &getByBug_status($bug_status) {
      $peer= &Bug::getPeer();
      return $peer->doSelect(new Criteria(array('bug_status', $bug_status, EQUAL)));
    }

    /**
     * Gets an instance of this object by index "op_sys"
     *
     * @access  static
     * @param   string op_sys
     * @return  &org.bugzilla.db.Bug[] object
     * @throws  rdbms.SQLException in case an error occurs
     */
    function &getByOp_sys($op_sys) {
      $peer= &Bug::getPeer();
      return $peer->doSelect(new Criteria(array('op_sys', $op_sys, EQUAL)));
    }

    /**
     * Gets an instance of this object by index "priority"
     *
     * @access  static
     * @param   string priority
     * @return  &org.bugzilla.db.Bug[] object
     * @throws  rdbms.SQLException in case an error occurs
     */
    function &getByPriority($priority) {
      $peer= &Bug::getPeer();
      return $peer->doSelect(new Criteria(array('priority', $priority, EQUAL)));
    }

    /**
     * Gets an instance of this object by index "reporter"
     *
     * @access  static
     * @param   int reporter
     * @return  &org.bugzilla.db.Bug[] object
     * @throws  rdbms.SQLException in case an error occurs
     */
    function &getByReporter($reporter) {
      $peer= &Bug::getPeer();
      return $peer->doSelect(new Criteria(array('reporter', $reporter, EQUAL)));
    }

    /**
     * Gets an instance of this object by index "version"
     *
     * @access  static
     * @param   string version
     * @return  &org.bugzilla.db.Bug[] object
     * @throws  rdbms.SQLException in case an error occurs
     */
    function &getByVersion($version) {
      $peer= &Bug::getPeer();
      return $peer->doSelect(new Criteria(array('version', $version, EQUAL)));
    }

    /**
     * Gets an instance of this object by index "resolution"
     *
     * @access  static
     * @param   string resolution
     * @return  &org.bugzilla.db.Bug[] object
     * @throws  rdbms.SQLException in case an error occurs
     */
    function &getByResolution($resolution) {
      $peer= &Bug::getPeer();
      return $peer->doSelect(new Criteria(array('resolution', $resolution, EQUAL)));
    }

    /**
     * Gets an instance of this object by index "votes"
     *
     * @access  static
     * @param   int votes
     * @return  &org.bugzilla.db.Bug[] object
     * @throws  rdbms.SQLException in case an error occurs
     */
    function &getByVotes($votes) {
      $peer= &Bug::getPeer();
      return $peer->doSelect(new Criteria(array('votes', $votes, EQUAL)));
    }

    /**
     * Gets an instance of this object by index "product_id"
     *
     * @access  static
     * @param   string product_id
     * @return  &org.bugzilla.db.Bug[] object
     * @throws  rdbms.SQLException in case an error occurs
     */
    function &getByProduct_id($product_id) {
      $peer= &Bug::getPeer();
      return $peer->doSelect(new Criteria(array('product_id', $product_id, EQUAL)));
    }

    /**
     * Gets an instance of this object by index "component_id"
     *
     * @access  static
     * @param   string component_id
     * @return  &org.bugzilla.db.Bug[] object
     * @throws  rdbms.SQLException in case an error occurs
     */
    function &getByComponent_id($component_id) {
      $peer= &Bug::getPeer();
      return $peer->doSelect(new Criteria(array('component_id', $component_id, EQUAL)));
    }

    /**
     * Gets an instance of this object by index "short_desc"
     *
     * @access  static
     * @param   string short_desc
     * @return  &org.bugzilla.db.Bug[] object
     * @throws  rdbms.SQLException in case an error occurs
     */
    function &getByShort_desc($short_desc) {
      $peer= &Bug::getPeer();
      return $peer->doSelect(new Criteria(array('short_desc', $short_desc, EQUAL)));
    }

    /**
     * Retrieves bug_id
     *
     * @access  public
     * @return  int
     */
    function getBug_id() {
      return $this->bug_id;
    }
      
    /**
     * Sets bug_id
     *
     * @access  public
     * @param   int bug_id
     * @return  int the previous value
     */
    function setBug_id($bug_id) {
      return $this->_change('bug_id', $bug_id);
    }

    /**
     * Retrieves assigned_to
     *
     * @access  public
     * @return  int
     */
    function getAssigned_to() {
      return $this->assigned_to;
    }
      
    /**
     * Sets assigned_to
     *
     * @access  public
     * @param   int assigned_to
     * @return  int the previous value
     */
    function setAssigned_to($assigned_to) {
      return $this->_change('assigned_to', $assigned_to);
    }

    /**
     * Retrieves bug_file_loc
     *
     * @access  public
     * @return  string
     */
    function getBug_file_loc() {
      return $this->bug_file_loc;
    }
      
    /**
     * Sets bug_file_loc
     *
     * @access  public
     * @param   string bug_file_loc
     * @return  string the previous value
     */
    function setBug_file_loc($bug_file_loc) {
      return $this->_change('bug_file_loc', $bug_file_loc);
    }

    /**
     * Retrieves bug_severity
     *
     * @access  public
     * @return  string
     */
    function getBug_severity() {
      return $this->bug_severity;
    }
      
    /**
     * Sets bug_severity
     *
     * @access  public
     * @param   string bug_severity
     * @return  string the previous value
     */
    function setBug_severity($bug_severity) {
      return $this->_change('bug_severity', $bug_severity);
    }

    /**
     * Retrieves bug_status
     *
     * @access  public
     * @return  string
     */
    function getBug_status() {
      return $this->bug_status;
    }
      
    /**
     * Sets bug_status
     *
     * @access  public
     * @param   string bug_status
     * @return  string the previous value
     */
    function setBug_status($bug_status) {
      return $this->_change('bug_status', $bug_status);
    }

    /**
     * Retrieves creation_ts
     *
     * @access  public
     * @return  &util.Date
     */
    function &getCreation_ts() {
      return $this->creation_ts;
    }
      
    /**
     * Sets creation_ts
     *
     * @access  public
     * @param   &util.Date creation_ts
     * @return  &util.Date the previous value
     */
    function &setCreation_ts(&$creation_ts) {
      return $this->_change('creation_ts', $creation_ts);
    }

    /**
     * Retrieves delta_ts
     *
     * @access  public
     * @return  &util.Date
     */
    function &getDelta_ts() {
      return $this->delta_ts;
    }
      
    /**
     * Sets delta_ts
     *
     * @access  public
     * @param   &util.Date delta_ts
     * @return  &util.Date the previous value
     */
    function &setDelta_ts(&$delta_ts) {
      return $this->_change('delta_ts', $delta_ts);
    }

    /**
     * Retrieves short_desc
     *
     * @access  public
     * @return  string
     */
    function getShort_desc() {
      return $this->short_desc;
    }
      
    /**
     * Sets short_desc
     *
     * @access  public
     * @param   string short_desc
     * @return  string the previous value
     */
    function setShort_desc($short_desc) {
      return $this->_change('short_desc', $short_desc);
    }

    /**
     * Retrieves op_sys
     *
     * @access  public
     * @return  string
     */
    function getOp_sys() {
      return $this->op_sys;
    }
      
    /**
     * Sets op_sys
     *
     * @access  public
     * @param   string op_sys
     * @return  string the previous value
     */
    function setOp_sys($op_sys) {
      return $this->_change('op_sys', $op_sys);
    }

    /**
     * Retrieves priority
     *
     * @access  public
     * @return  string
     */
    function getPriority() {
      return $this->priority;
    }
      
    /**
     * Sets priority
     *
     * @access  public
     * @param   string priority
     * @return  string the previous value
     */
    function setPriority($priority) {
      return $this->_change('priority', $priority);
    }

    /**
     * Retrieves rep_platform
     *
     * @access  public
     * @return  string
     */
    function getRep_platform() {
      return $this->rep_platform;
    }
      
    /**
     * Sets rep_platform
     *
     * @access  public
     * @param   string rep_platform
     * @return  string the previous value
     */
    function setRep_platform($rep_platform) {
      return $this->_change('rep_platform', $rep_platform);
    }

    /**
     * Retrieves reporter
     *
     * @access  public
     * @return  int
     */
    function getReporter() {
      return $this->reporter;
    }
      
    /**
     * Sets reporter
     *
     * @access  public
     * @param   int reporter
     * @return  int the previous value
     */
    function setReporter($reporter) {
      return $this->_change('reporter', $reporter);
    }

    /**
     * Retrieves version
     *
     * @access  public
     * @return  string
     */
    function getVersion() {
      return $this->version;
    }
      
    /**
     * Sets version
     *
     * @access  public
     * @param   string version
     * @return  string the previous value
     */
    function setVersion($version) {
      return $this->_change('version', $version);
    }

    /**
     * Retrieves resolution
     *
     * @access  public
     * @return  string
     */
    function getResolution() {
      return $this->resolution;
    }
      
    /**
     * Sets resolution
     *
     * @access  public
     * @param   string resolution
     * @return  string the previous value
     */
    function setResolution($resolution) {
      return $this->_change('resolution', $resolution);
    }

    /**
     * Retrieves target_milestone
     *
     * @access  public
     * @return  string
     */
    function getTarget_milestone() {
      return $this->target_milestone;
    }
      
    /**
     * Sets target_milestone
     *
     * @access  public
     * @param   string target_milestone
     * @return  string the previous value
     */
    function setTarget_milestone($target_milestone) {
      return $this->_change('target_milestone', $target_milestone);
    }

    /**
     * Retrieves qa_contact
     *
     * @access  public
     * @return  int
     */
    function getQa_contact() {
      return $this->qa_contact;
    }
      
    /**
     * Sets qa_contact
     *
     * @access  public
     * @param   int qa_contact
     * @return  int the previous value
     */
    function setQa_contact($qa_contact) {
      return $this->_change('qa_contact', $qa_contact);
    }

    /**
     * Retrieves status_whiteboard
     *
     * @access  public
     * @return  string
     */
    function getStatus_whiteboard() {
      return $this->status_whiteboard;
    }
      
    /**
     * Sets status_whiteboard
     *
     * @access  public
     * @param   string status_whiteboard
     * @return  string the previous value
     */
    function setStatus_whiteboard($status_whiteboard) {
      return $this->_change('status_whiteboard', $status_whiteboard);
    }

    /**
     * Retrieves votes
     *
     * @access  public
     * @return  int
     */
    function getVotes() {
      return $this->votes;
    }
      
    /**
     * Sets votes
     *
     * @access  public
     * @param   int votes
     * @return  int the previous value
     */
    function setVotes($votes) {
      return $this->_change('votes', $votes);
    }

    /**
     * Retrieves keywords
     *
     * @access  public
     * @return  string
     */
    function getKeywords() {
      return $this->keywords;
    }
      
    /**
     * Sets keywords
     *
     * @access  public
     * @param   string keywords
     * @return  string the previous value
     */
    function setKeywords($keywords) {
      return $this->_change('keywords', $keywords);
    }

    /**
     * Retrieves lastdiffed
     *
     * @access  public
     * @return  &util.Date
     */
    function &getLastdiffed() {
      return $this->lastdiffed;
    }
      
    /**
     * Sets lastdiffed
     *
     * @access  public
     * @param   &util.Date lastdiffed
     * @return  &util.Date the previous value
     */
    function &setLastdiffed(&$lastdiffed) {
      return $this->_change('lastdiffed', $lastdiffed);
    }

    /**
     * Retrieves everconfirmed
     *
     * @access  public
     * @return  int
     */
    function getEverconfirmed() {
      return $this->everconfirmed;
    }
      
    /**
     * Sets everconfirmed
     *
     * @access  public
     * @param   int everconfirmed
     * @return  int the previous value
     */
    function setEverconfirmed($everconfirmed) {
      return $this->_change('everconfirmed', $everconfirmed);
    }

    /**
     * Retrieves reporter_accessible
     *
     * @access  public
     * @return  int
     */
    function getReporter_accessible() {
      return $this->reporter_accessible;
    }
      
    /**
     * Sets reporter_accessible
     *
     * @access  public
     * @param   int reporter_accessible
     * @return  int the previous value
     */
    function setReporter_accessible($reporter_accessible) {
      return $this->_change('reporter_accessible', $reporter_accessible);
    }

    /**
     * Retrieves cclist_accessible
     *
     * @access  public
     * @return  int
     */
    function getCclist_accessible() {
      return $this->cclist_accessible;
    }
      
    /**
     * Sets cclist_accessible
     *
     * @access  public
     * @param   int cclist_accessible
     * @return  int the previous value
     */
    function setCclist_accessible($cclist_accessible) {
      return $this->_change('cclist_accessible', $cclist_accessible);
    }

    /**
     * Retrieves estimated_time
     *
     * @access  public
     * @return  string
     */
    function getEstimated_time() {
      return $this->estimated_time;
    }
      
    /**
     * Sets estimated_time
     *
     * @access  public
     * @param   string estimated_time
     * @return  string the previous value
     */
    function setEstimated_time($estimated_time) {
      return $this->_change('estimated_time', $estimated_time);
    }

    /**
     * Retrieves remaining_time
     *
     * @access  public
     * @return  string
     */
    function getRemaining_time() {
      return $this->remaining_time;
    }
      
    /**
     * Sets remaining_time
     *
     * @access  public
     * @param   string remaining_time
     * @return  string the previous value
     */
    function setRemaining_time($remaining_time) {
      return $this->_change('remaining_time', $remaining_time);
    }

    /**
     * Retrieves alias
     *
     * @access  public
     * @return  string
     */
    function getAlias() {
      return $this->alias;
    }
      
    /**
     * Sets alias
     *
     * @access  public
     * @param   string alias
     * @return  string the previous value
     */
    function setAlias($alias) {
      return $this->_change('alias', $alias);
    }

    /**
     * Retrieves product_id
     *
     * @access  public
     * @return  string
     */
    function getProduct_id() {
      return $this->product_id;
    }
      
    /**
     * Sets product_id
     *
     * @access  public
     * @param   string product_id
     * @return  string the previous value
     */
    function setProduct_id($product_id) {
      return $this->_change('product_id', $product_id);
    }

    /**
     * Retrieves component_id
     *
     * @access  public
     * @return  string
     */
    function getComponent_id() {
      return $this->component_id;
    }
      
    /**
     * Sets component_id
     *
     * @access  public
     * @param   string component_id
     * @return  string the previous value
     */
    function setComponent_id($component_id) {
      return $this->_change('component_id', $component_id);
    }
  }
?>
