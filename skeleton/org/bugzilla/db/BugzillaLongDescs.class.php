<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses('rdbms.DataSet');
 
  /**
   * Class wrapper for table longdescs, database bugs
   * (Auto-generated on Mon, 18 Oct 2004 18:12:16 +0200 by clang)
   *
   * @purpose  Datasource accessor
   */
  class BugzillaLongDescs extends DataSet {
    var
      $bug_id             = 0,
      $who                = 0,
      $bug_when           = NULL,
      $thetext            = NULL;

    /**
     * Static initializer
     *
     * @model   static
     * @access  public
     */
    function __static() { 
      with ($peer= &BugzillaLongDescs::getPeer()); {
        $peer->setTable('longdescs');
        $peer->setConnection('bugzilla');
        $peer->setPrimary(array(''));
        $peer->setTypes(array(
          'bug_id'              => '%d',
          'who'                 => '%d',
          'bug_when'            => '%s',
          'thetext'             => '%s'
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
     * @return  &BugzillaLongDescs[] object
     * @throws  rdbms.SQLException in case an error occurs
     */
    function &getByBug_id($bug_id) {
      $peer= &BugzillaLongDescs::getPeer();
      return $peer->doSelect(new Criteria(array('bug_id', $bug_id, EQUAL)));
    }

    /**
     * Gets an instance of this object by index "who"
     *
     * @access  static
     * @param   int who
     * @return  &BugzillaLongDescs[] object
     * @throws  rdbms.SQLException in case an error occurs
     */
    function &getByWho($who) {
      $peer= &BugzillaLongDescs::getPeer();
      return $peer->doSelect(new Criteria(array('who', $who, EQUAL)));
    }

    /**
     * Gets an instance of this object by index "bug_when"
     *
     * @access  static
     * @param   util.Date bug_when
     * @return  &BugzillaLongDescs[] object
     * @throws  rdbms.SQLException in case an error occurs
     */
    function &getByBug_when($bug_when) {
      $peer= &BugzillaLongDescs::getPeer();
      return $peer->doSelect(new Criteria(array('bug_when', $bug_when, EQUAL)));
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
     * Retrieves thetext
     *
     * @access  public
     * @return  string
     */
    function getThetext() {
      return $this->thetext;
    }
      
    /**
     * Sets thetext
     *
     * @access  public
     * @param   string thetext
     * @return  string the previous value
     */
    function setThetext($thetext) {
      return $this->_change('thetext', $thetext);
    }
  }
?>
