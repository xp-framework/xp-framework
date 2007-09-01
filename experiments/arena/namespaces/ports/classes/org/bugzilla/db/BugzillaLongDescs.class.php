<?php
/* This class is part of the XP framework
 *
 * $Id: BugzillaLongDescs.class.php 8971 2006-12-27 15:27:10Z friebe $
 */

  namespace org::bugzilla::db;
 
  ::uses('rdbms.DataSet');
 
  /**
   * Class wrapper for table longdescs, database bugs
   * (Auto-generated on Mon, 18 Oct 2004 18:12:16 +0200 by clang)
   *
   * @purpose  Datasource accessor
   */
  class BugzillaLongDescs extends rdbms::DataSet {
    public
      $bug_id             = 0,
      $who                = 0,
      $bug_when           = NULL,
      $thetext            = NULL;

    /**
     * Static initializer
     *
     */
    public static function __static() { 
      with ($peer= BugzillaLongDescs::getPeer()); {
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
     * @return  &rdbms.Peer
     */
    public function getPeer() {
      return ::forName(__CLASS__);
    }
  
    /**
     * Gets an instance of this object by index "bug_id"
     *
     * @param   int bug_id
     * @return  &BugzillaLongDescs[] object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getByBug_id($bug_id) {
      $peer= BugzillaLongDescs::getPeer();
      return $peer->doSelect(new rdbms::Criteria(array('bug_id', $bug_id, EQUAL)));
    }

    /**
     * Gets an instance of this object by index "who"
     *
     * @param   int who
     * @return  &BugzillaLongDescs[] object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getByWho($who) {
      $peer= BugzillaLongDescs::getPeer();
      return $peer->doSelect(new rdbms::Criteria(array('who', $who, EQUAL)));
    }

    /**
     * Gets an instance of this object by index "bug_when"
     *
     * @param   util.Date bug_when
     * @return  &BugzillaLongDescs[] object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getByBug_when($bug_when) {
      $peer= BugzillaLongDescs::getPeer();
      return $peer->doSelect(new rdbms::Criteria(array('bug_when', $bug_when, EQUAL)));
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
     * Retrieves thetext
     *
     * @return  string
     */
    public function getThetext() {
      return $this->thetext;
    }
      
    /**
     * Sets thetext
     *
     * @param   string thetext
     * @return  string the previous value
     */
    public function setThetext($thetext) {
      return $this->_change('thetext', $thetext);
    }
  }
?>
