<?php
/* This class is part of the XP framework
 *
 * $Id: BugsActivity.class.php 8971 2006-12-27 15:27:10Z friebe $
 */

  namespace org::bugzilla::db;
 
  ::uses('rdbms.DataSet');
 
  /**
   * Class wrapper for table bugs_activity, database bugs
   * (Auto-generated on Wed,  8 Dec 2004 16:48:17 +0100 by clang)
   *
   * @purpose  Datasource accessor
   */
  class BugsActivity extends rdbms::DataSet {
    public
      $bug_id             = 0,
      $who                = 0,
      $bug_when           = NULL,
      $fieldid            = 0,
      $added              = NULL,
      $removed            = NULL,
      $attach_id          = NULL;

    /**
     * Static initializer
     *
     */
    public static function __static() { 
      with ($peer= ::getPeer()); {
        $peer->setTable('bugs_activity');
        $peer->setConnection('bugzilla');
        $peer->setPrimary(array(''));
        $peer->setTypes(array(
          'bug_id'              => '%d',
          'who'                 => '%d',
          'bug_when'            => '%s',
          'fieldid'             => '%d',
          'added'               => '%s',
          'removed'             => '%s',
          'attach_id'           => '%d'
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
     * Gets an instance of this object by index "bug_id"
     *
     * @param   int bug_id
     * @return  &BugsActivity[] object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getByBug_id($bug_id) {
      $peer= ::getPeer();
      return $peer->doSelect(new rdbms::Criteria(array('bug_id', $bug_id, EQUAL)));
    }

    /**
     * Gets an instance of this object by index "bug_when"
     *
     * @param   util.Date bug_when
     * @return  &BugsActivity[] object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getByBug_when($bug_when) {
      $peer= ::getPeer();
      return $peer->doSelect(new rdbms::Criteria(array('bug_when', $bug_when, EQUAL)));
    }

    /**
     * Gets an instance of this object by index "fieldid"
     *
     * @param   int fieldid
     * @return  &BugsActivity[] object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getByFieldid($fieldid) {
      $peer= ::getPeer();
      return $peer->doSelect(new rdbms::Criteria(array('fieldid', $fieldid, EQUAL)));
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
     * Retrieves who
     *
     * @return  int
     */
    public function getWho() {
      return $this->who;
    }
      
    /**
     * Sets who
     *
     * @param   int who
     * @return  int the previous value
     */
    public function setWho($who) {
      return $this->_change('who', $who);
    }

    /**
     * Retrieves bug_when
     *
     * @return  util.Date
     */
    public function getBug_when() {
      return $this->bug_when;
    }
      
    /**
     * Sets bug_when
     *
     * @param   util.Date bug_when
     * @return  util.Date the previous value
     */
    public function setBug_when($bug_when) {
      return $this->_change('bug_when', $bug_when);
    }

    /**
     * Retrieves fieldid
     *
     * @return  int
     */
    public function getFieldid() {
      return $this->fieldid;
    }
      
    /**
     * Sets fieldid
     *
     * @param   int fieldid
     * @return  int the previous value
     */
    public function setFieldid($fieldid) {
      return $this->_change('fieldid', $fieldid);
    }

    /**
     * Retrieves added
     *
     * @return  string
     */
    public function getAdded() {
      return $this->added;
    }
      
    /**
     * Sets added
     *
     * @param   string added
     * @return  string the previous value
     */
    public function setAdded($added) {
      return $this->_change('added', $added);
    }

    /**
     * Retrieves removed
     *
     * @return  string
     */
    public function getRemoved() {
      return $this->removed;
    }
      
    /**
     * Sets removed
     *
     * @param   string removed
     * @return  string the previous value
     */
    public function setRemoved($removed) {
      return $this->_change('removed', $removed);
    }

    /**
     * Retrieves attach_id
     *
     * @return  int
     */
    public function getAttach_id() {
      return $this->attach_id;
    }
      
    /**
     * Sets attach_id
     *
     * @param   int attach_id
     * @return  int the previous value
     */
    public function setAttach_id($attach_id) {
      return $this->_change('attach_id', $attach_id);
    }
  }
?>
