<?php
/* This class is part of the XP framework
 *
 * $Id: Bug.class.php 8971 2006-12-27 15:27:10Z friebe $
 */

  namespace org::bugzilla::db;
 
  ::uses('rdbms.DataSet');
 
  /**
   * Class wrapper for table bugs, database bugs
   * (Auto-generated on Tue,  7 Jun 2005 11:58:02 +0200 by clang)
   *
   * @purpose  Datasource accessor
   */
  class Bug extends rdbms::DataSet {
    public
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
     */
    public static function __static() { 
      with ($peer= ::getPeer()); {
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
     * @return  &rdbms.Peer
     */
    public function getPeer() {
      return ::forName(__CLASS__);
    }
  
    /**
     * Gets an instance of this object by index "PRIMARY"
     *
     * @param   int bug_id
     * @return  &org.bugzilla.db.Bug object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getByBug_id($bug_id) {
      $peer= ::getPeer();
      return array_shift($peer->doSelect(new rdbms::Criteria(array('bug_id', $bug_id, EQUAL))));
    }

    /**
     * Gets an instance of this object by index "alias"
     *
     * @param   string alias
     * @return  &org.bugzilla.db.Bug object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getByAlias($alias) {
      $peer= ::getPeer();
      return array_shift($peer->doSelect(new rdbms::Criteria(array('alias', $alias, EQUAL))));
    }

    /**
     * Gets an instance of this object by index "assigned_to"
     *
     * @param   int assigned_to
     * @return  &org.bugzilla.db.Bug[] object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getByAssigned_to($assigned_to) {
      $peer= ::getPeer();
      return $peer->doSelect(new rdbms::Criteria(array('assigned_to', $assigned_to, EQUAL)));
    }

    /**
     * Gets an instance of this object by index "creation_ts"
     *
     * @param   util.Date creation_ts
     * @return  &org.bugzilla.db.Bug[] object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getByCreation_ts($creation_ts) {
      $peer= ::getPeer();
      return $peer->doSelect(new rdbms::Criteria(array('creation_ts', $creation_ts, EQUAL)));
    }

    /**
     * Gets an instance of this object by index "delta_ts"
     *
     * @param   util.Date delta_ts
     * @return  &org.bugzilla.db.Bug[] object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getByDelta_ts($delta_ts) {
      $peer= ::getPeer();
      return $peer->doSelect(new rdbms::Criteria(array('delta_ts', $delta_ts, EQUAL)));
    }

    /**
     * Gets an instance of this object by index "bug_severity"
     *
     * @param   string bug_severity
     * @return  &org.bugzilla.db.Bug[] object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getByBug_severity($bug_severity) {
      $peer= ::getPeer();
      return $peer->doSelect(new rdbms::Criteria(array('bug_severity', $bug_severity, EQUAL)));
    }

    /**
     * Gets an instance of this object by index "bug_status"
     *
     * @param   string bug_status
     * @return  &org.bugzilla.db.Bug[] object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getByBug_status($bug_status) {
      $peer= ::getPeer();
      return $peer->doSelect(new rdbms::Criteria(array('bug_status', $bug_status, EQUAL)));
    }

    /**
     * Gets an instance of this object by index "op_sys"
     *
     * @param   string op_sys
     * @return  &org.bugzilla.db.Bug[] object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getByOp_sys($op_sys) {
      $peer= ::getPeer();
      return $peer->doSelect(new rdbms::Criteria(array('op_sys', $op_sys, EQUAL)));
    }

    /**
     * Gets an instance of this object by index "priority"
     *
     * @param   string priority
     * @return  &org.bugzilla.db.Bug[] object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getByPriority($priority) {
      $peer= ::getPeer();
      return $peer->doSelect(new rdbms::Criteria(array('priority', $priority, EQUAL)));
    }

    /**
     * Gets an instance of this object by index "reporter"
     *
     * @param   int reporter
     * @return  &org.bugzilla.db.Bug[] object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getByReporter($reporter) {
      $peer= ::getPeer();
      return $peer->doSelect(new rdbms::Criteria(array('reporter', $reporter, EQUAL)));
    }

    /**
     * Gets an instance of this object by index "version"
     *
     * @param   string version
     * @return  &org.bugzilla.db.Bug[] object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getByVersion($version) {
      $peer= ::getPeer();
      return $peer->doSelect(new rdbms::Criteria(array('version', $version, EQUAL)));
    }

    /**
     * Gets an instance of this object by index "resolution"
     *
     * @param   string resolution
     * @return  &org.bugzilla.db.Bug[] object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getByResolution($resolution) {
      $peer= ::getPeer();
      return $peer->doSelect(new rdbms::Criteria(array('resolution', $resolution, EQUAL)));
    }

    /**
     * Gets an instance of this object by index "votes"
     *
     * @param   int votes
     * @return  &org.bugzilla.db.Bug[] object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getByVotes($votes) {
      $peer= ::getPeer();
      return $peer->doSelect(new rdbms::Criteria(array('votes', $votes, EQUAL)));
    }

    /**
     * Gets an instance of this object by index "product_id"
     *
     * @param   string product_id
     * @return  &org.bugzilla.db.Bug[] object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getByProduct_id($product_id) {
      $peer= ::getPeer();
      return $peer->doSelect(new rdbms::Criteria(array('product_id', $product_id, EQUAL)));
    }

    /**
     * Gets an instance of this object by index "component_id"
     *
     * @param   string component_id
     * @return  &org.bugzilla.db.Bug[] object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getByComponent_id($component_id) {
      $peer= ::getPeer();
      return $peer->doSelect(new rdbms::Criteria(array('component_id', $component_id, EQUAL)));
    }

    /**
     * Gets an instance of this object by index "short_desc"
     *
     * @param   string short_desc
     * @return  &org.bugzilla.db.Bug[] object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getByShort_desc($short_desc) {
      $peer= ::getPeer();
      return $peer->doSelect(new rdbms::Criteria(array('short_desc', $short_desc, EQUAL)));
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
     * Retrieves assigned_to
     *
     * @return  int
     */
    public function getAssigned_to() {
      return $this->assigned_to;
    }
      
    /**
     * Sets assigned_to
     *
     * @param   int assigned_to
     * @return  int the previous value
     */
    public function setAssigned_to($assigned_to) {
      return $this->_change('assigned_to', $assigned_to);
    }

    /**
     * Retrieves bug_file_loc
     *
     * @return  string
     */
    public function getBug_file_loc() {
      return $this->bug_file_loc;
    }
      
    /**
     * Sets bug_file_loc
     *
     * @param   string bug_file_loc
     * @return  string the previous value
     */
    public function setBug_file_loc($bug_file_loc) {
      return $this->_change('bug_file_loc', $bug_file_loc);
    }

    /**
     * Retrieves bug_severity
     *
     * @return  string
     */
    public function getBug_severity() {
      return $this->bug_severity;
    }
      
    /**
     * Sets bug_severity
     *
     * @param   string bug_severity
     * @return  string the previous value
     */
    public function setBug_severity($bug_severity) {
      return $this->_change('bug_severity', $bug_severity);
    }

    /**
     * Retrieves bug_status
     *
     * @return  string
     */
    public function getBug_status() {
      return $this->bug_status;
    }
      
    /**
     * Sets bug_status
     *
     * @param   string bug_status
     * @return  string the previous value
     */
    public function setBug_status($bug_status) {
      return $this->_change('bug_status', $bug_status);
    }

    /**
     * Retrieves creation_ts
     *
     * @return  &util.Date
     */
    public function getCreation_ts() {
      return $this->creation_ts;
    }
      
    /**
     * Sets creation_ts
     *
     * @param   &util.Date creation_ts
     * @return  &util.Date the previous value
     */
    public function setCreation_ts($creation_ts) {
      return $this->_change('creation_ts', $creation_ts);
    }

    /**
     * Retrieves delta_ts
     *
     * @return  &util.Date
     */
    public function getDelta_ts() {
      return $this->delta_ts;
    }
      
    /**
     * Sets delta_ts
     *
     * @param   &util.Date delta_ts
     * @return  &util.Date the previous value
     */
    public function setDelta_ts($delta_ts) {
      return $this->_change('delta_ts', $delta_ts);
    }

    /**
     * Retrieves short_desc
     *
     * @return  string
     */
    public function getShort_desc() {
      return $this->short_desc;
    }
      
    /**
     * Sets short_desc
     *
     * @param   string short_desc
     * @return  string the previous value
     */
    public function setShort_desc($short_desc) {
      return $this->_change('short_desc', $short_desc);
    }

    /**
     * Retrieves op_sys
     *
     * @return  string
     */
    public function getOp_sys() {
      return $this->op_sys;
    }
      
    /**
     * Sets op_sys
     *
     * @param   string op_sys
     * @return  string the previous value
     */
    public function setOp_sys($op_sys) {
      return $this->_change('op_sys', $op_sys);
    }

    /**
     * Retrieves priority
     *
     * @return  string
     */
    public function getPriority() {
      return $this->priority;
    }
      
    /**
     * Sets priority
     *
     * @param   string priority
     * @return  string the previous value
     */
    public function setPriority($priority) {
      return $this->_change('priority', $priority);
    }

    /**
     * Retrieves rep_platform
     *
     * @return  string
     */
    public function getRep_platform() {
      return $this->rep_platform;
    }
      
    /**
     * Sets rep_platform
     *
     * @param   string rep_platform
     * @return  string the previous value
     */
    public function setRep_platform($rep_platform) {
      return $this->_change('rep_platform', $rep_platform);
    }

    /**
     * Retrieves reporter
     *
     * @return  int
     */
    public function getReporter() {
      return $this->reporter;
    }
      
    /**
     * Sets reporter
     *
     * @param   int reporter
     * @return  int the previous value
     */
    public function setReporter($reporter) {
      return $this->_change('reporter', $reporter);
    }

    /**
     * Retrieves version
     *
     * @return  string
     */
    public function getVersion() {
      return $this->version;
    }
      
    /**
     * Sets version
     *
     * @param   string version
     * @return  string the previous value
     */
    public function setVersion($version) {
      return $this->_change('version', $version);
    }

    /**
     * Retrieves resolution
     *
     * @return  string
     */
    public function getResolution() {
      return $this->resolution;
    }
      
    /**
     * Sets resolution
     *
     * @param   string resolution
     * @return  string the previous value
     */
    public function setResolution($resolution) {
      return $this->_change('resolution', $resolution);
    }

    /**
     * Retrieves target_milestone
     *
     * @return  string
     */
    public function getTarget_milestone() {
      return $this->target_milestone;
    }
      
    /**
     * Sets target_milestone
     *
     * @param   string target_milestone
     * @return  string the previous value
     */
    public function setTarget_milestone($target_milestone) {
      return $this->_change('target_milestone', $target_milestone);
    }

    /**
     * Retrieves qa_contact
     *
     * @return  int
     */
    public function getQa_contact() {
      return $this->qa_contact;
    }
      
    /**
     * Sets qa_contact
     *
     * @param   int qa_contact
     * @return  int the previous value
     */
    public function setQa_contact($qa_contact) {
      return $this->_change('qa_contact', $qa_contact);
    }

    /**
     * Retrieves status_whiteboard
     *
     * @return  string
     */
    public function getStatus_whiteboard() {
      return $this->status_whiteboard;
    }
      
    /**
     * Sets status_whiteboard
     *
     * @param   string status_whiteboard
     * @return  string the previous value
     */
    public function setStatus_whiteboard($status_whiteboard) {
      return $this->_change('status_whiteboard', $status_whiteboard);
    }

    /**
     * Retrieves votes
     *
     * @return  int
     */
    public function getVotes() {
      return $this->votes;
    }
      
    /**
     * Sets votes
     *
     * @param   int votes
     * @return  int the previous value
     */
    public function setVotes($votes) {
      return $this->_change('votes', $votes);
    }

    /**
     * Retrieves keywords
     *
     * @return  string
     */
    public function getKeywords() {
      return $this->keywords;
    }
      
    /**
     * Sets keywords
     *
     * @param   string keywords
     * @return  string the previous value
     */
    public function setKeywords($keywords) {
      return $this->_change('keywords', $keywords);
    }

    /**
     * Retrieves lastdiffed
     *
     * @return  &util.Date
     */
    public function getLastdiffed() {
      return $this->lastdiffed;
    }
      
    /**
     * Sets lastdiffed
     *
     * @param   &util.Date lastdiffed
     * @return  &util.Date the previous value
     */
    public function setLastdiffed($lastdiffed) {
      return $this->_change('lastdiffed', $lastdiffed);
    }

    /**
     * Retrieves everconfirmed
     *
     * @return  int
     */
    public function getEverconfirmed() {
      return $this->everconfirmed;
    }
      
    /**
     * Sets everconfirmed
     *
     * @param   int everconfirmed
     * @return  int the previous value
     */
    public function setEverconfirmed($everconfirmed) {
      return $this->_change('everconfirmed', $everconfirmed);
    }

    /**
     * Retrieves reporter_accessible
     *
     * @return  int
     */
    public function getReporter_accessible() {
      return $this->reporter_accessible;
    }
      
    /**
     * Sets reporter_accessible
     *
     * @param   int reporter_accessible
     * @return  int the previous value
     */
    public function setReporter_accessible($reporter_accessible) {
      return $this->_change('reporter_accessible', $reporter_accessible);
    }

    /**
     * Retrieves cclist_accessible
     *
     * @return  int
     */
    public function getCclist_accessible() {
      return $this->cclist_accessible;
    }
      
    /**
     * Sets cclist_accessible
     *
     * @param   int cclist_accessible
     * @return  int the previous value
     */
    public function setCclist_accessible($cclist_accessible) {
      return $this->_change('cclist_accessible', $cclist_accessible);
    }

    /**
     * Retrieves estimated_time
     *
     * @return  string
     */
    public function getEstimated_time() {
      return $this->estimated_time;
    }
      
    /**
     * Sets estimated_time
     *
     * @param   string estimated_time
     * @return  string the previous value
     */
    public function setEstimated_time($estimated_time) {
      return $this->_change('estimated_time', $estimated_time);
    }

    /**
     * Retrieves remaining_time
     *
     * @return  string
     */
    public function getRemaining_time() {
      return $this->remaining_time;
    }
      
    /**
     * Sets remaining_time
     *
     * @param   string remaining_time
     * @return  string the previous value
     */
    public function setRemaining_time($remaining_time) {
      return $this->_change('remaining_time', $remaining_time);
    }

    /**
     * Retrieves alias
     *
     * @return  string
     */
    public function getAlias() {
      return $this->alias;
    }
      
    /**
     * Sets alias
     *
     * @param   string alias
     * @return  string the previous value
     */
    public function setAlias($alias) {
      return $this->_change('alias', $alias);
    }

    /**
     * Retrieves product_id
     *
     * @return  string
     */
    public function getProduct_id() {
      return $this->product_id;
    }
      
    /**
     * Sets product_id
     *
     * @param   string product_id
     * @return  string the previous value
     */
    public function setProduct_id($product_id) {
      return $this->_change('product_id', $product_id);
    }

    /**
     * Retrieves component_id
     *
     * @return  string
     */
    public function getComponent_id() {
      return $this->component_id;
    }
      
    /**
     * Sets component_id
     *
     * @param   string component_id
     * @return  string the previous value
     */
    public function setComponent_id($component_id) {
      return $this->_change('component_id', $component_id);
    }
  }
?>
