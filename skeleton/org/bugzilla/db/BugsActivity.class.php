<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses('rdbms.DataSet');
 
  /**
   * Class wrapper for table bugs_activity, database bugs
   * (Auto-generated on Wed,  8 Dec 2004 16:48:17 +0100 by clang)
   *
   * @purpose  Datasource accessor
   */
  class BugsActivity extends DataSet {
    var
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
     * @model   static
     * @access  public
     */
    function __static() { 
      with ($peer= &BugsActivity::getPeer()); {
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
     * @access  public
     * @return  &rdbms.Peer
     */
    function &getPeer() {
      return Peer::forName(__CLASS__);
    }
  
    /**
     * Gets an instance of this object by index "bug_id"
     *
     * @access  static
     * @param   int bug_id
     * @return  &BugsActivity[] object
     * @throws  rdbms.SQLException in case an error occurs
     */
    function &getByBug_id($bug_id) {
      $peer= &BugsActivity::getPeer();
      return $peer->doSelect(new Criteria(array('bug_id', $bug_id, EQUAL)));
    }

    /**
     * Gets an instance of this object by index "bug_when"
     *
     * @access  static
     * @param   util.Date bug_when
     * @return  &BugsActivity[] object
     * @throws  rdbms.SQLException in case an error occurs
     */
    function &getByBug_when($bug_when) {
      $peer= &BugsActivity::getPeer();
      return $peer->doSelect(new Criteria(array('bug_when', $bug_when, EQUAL)));
    }

    /**
     * Gets an instance of this object by index "fieldid"
     *
     * @access  static
     * @param   int fieldid
     * @return  &BugsActivity[] object
     * @throws  rdbms.SQLException in case an error occurs
     */
    function &getByFieldid($fieldid) {
      $peer= &BugsActivity::getPeer();
      return $peer->doSelect(new Criteria(array('fieldid', $fieldid, EQUAL)));
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
     * Retrieves who
     *
     * @access  public
     * @return  int
     */
    function getWho() {
      return $this->who;
    }
      
    /**
     * Sets who
     *
     * @access  public
     * @param   int who
     * @return  int the previous value
     */
    function setWho($who) {
      return $this->_change('who', $who);
    }

    /**
     * Retrieves bug_when
     *
     * @access  public
     * @return  util.Date
     */
    function getBug_when() {
      return $this->bug_when;
    }
      
    /**
     * Sets bug_when
     *
     * @access  public
     * @param   util.Date bug_when
     * @return  util.Date the previous value
     */
    function setBug_when($bug_when) {
      return $this->_change('bug_when', $bug_when);
    }

    /**
     * Retrieves fieldid
     *
     * @access  public
     * @return  int
     */
    function getFieldid() {
      return $this->fieldid;
    }
      
    /**
     * Sets fieldid
     *
     * @access  public
     * @param   int fieldid
     * @return  int the previous value
     */
    function setFieldid($fieldid) {
      return $this->_change('fieldid', $fieldid);
    }

    /**
     * Retrieves added
     *
     * @access  public
     * @return  string
     */
    function getAdded() {
      return $this->added;
    }
      
    /**
     * Sets added
     *
     * @access  public
     * @param   string added
     * @return  string the previous value
     */
    function setAdded($added) {
      return $this->_change('added', $added);
    }

    /**
     * Retrieves removed
     *
     * @access  public
     * @return  string
     */
    function getRemoved() {
      return $this->removed;
    }
      
    /**
     * Sets removed
     *
     * @access  public
     * @param   string removed
     * @return  string the previous value
     */
    function setRemoved($removed) {
      return $this->_change('removed', $removed);
    }

    /**
     * Retrieves attach_id
     *
     * @access  public
     * @return  int
     */
    function getAttach_id() {
      return $this->attach_id;
    }
      
    /**
     * Sets attach_id
     *
     * @access  public
     * @param   int attach_id
     * @return  int the previous value
     */
    function setAttach_id($attach_id) {
      return $this->_change('attach_id', $attach_id);
    }
  }
?>
