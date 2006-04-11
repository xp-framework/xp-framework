<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses('rdbms.DataSet');
 
  /**
   * Class wrapper for table profiles, database bugs218
   * (Auto-generated on Tue,  7 Jun 2005 11:47:41 +0200 by clang)
   *
   * @purpose  Datasource accessor
   */
  class BugzillaProfile extends DataSet {
    var
      $userid             = 0,
      $login_name         = '',
      $cryptpassword      = NULL,
      $realname           = NULL,
      $disabledtext       = '',
      $mybugslink         = 0,
      $person_id          = NULL,
      $refreshed_when     = NULL;

    /**
     * Static initializer
     *
     * @model   static
     * @access  public
     */
    function __static() { 
      with ($peer= &BugzillaProfile::getPeer()); {
        $peer->setTable('profiles');
        $peer->setConnection('bugzilla');
        $peer->setIdentity('userid');
        $peer->setPrimary(array('userid'));
        $peer->setTypes(array(
          'userid'              => '%d',
          'login_name'          => '%s',
          'cryptpassword'       => '%s',
          'realname'            => '%s',
          'disabledtext'        => '%s',
          'mybugslink'          => '%d',
          'person_id'           => '%d',
          'refreshed_when'      => '%s'
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
     * @param   int userid
     * @return  &org.bugzilla.db.BugzillaProfile object
     * @throws  rdbms.SQLException in case an error occurs
     */
    function &getByUserid($userid) {
      $peer= &BugzillaProfile::getPeer();
      return array_shift($peer->doSelect(new Criteria(array('userid', $userid, EQUAL))));
    }

    /**
     * Gets an instance of this object by index "login_name"
     *
     * @access  static
     * @param   string login_name
     * @return  &org.bugzilla.db.BugzillaProfile object
     * @throws  rdbms.SQLException in case an error occurs
     */
    function &getByLogin_name($login_name) {
      $peer= &BugzillaProfile::getPeer();
      return array_shift($peer->doSelect(new Criteria(array('login_name', $login_name, EQUAL))));
    }
    
    /**
     * Gets an instance of this object by person_id
     *
     * @access  static
     * @param   int person_id
     * @return  &org.bugzilla.db.BugzillaProfile object
     * @throws  rdbms.SQLException in case an error occurs
     */
    function &getByPerson_id($person_id) {
      $peer= &BugzillaProfile::getPeer();
      return array_shift($peer->doSelect(new Criteria(array('person_id', $person_id, EQUAL))));
    }

    /**
     * Retrieves userid
     *
     * @access  public
     * @return  int
     */
    function getUserid() {
      return $this->userid;
    }
      
    /**
     * Sets userid
     *
     * @access  public
     * @param   int userid
     * @return  int the previous value
     */
    function setUserid($userid) {
      return $this->_change('userid', $userid);
    }

    /**
     * Retrieves login_name
     *
     * @access  public
     * @return  string
     */
    function getLogin_name() {
      return $this->login_name;
    }
      
    /**
     * Sets login_name
     *
     * @access  public
     * @param   string login_name
     * @return  string the previous value
     */
    function setLogin_name($login_name) {
      return $this->_change('login_name', $login_name);
    }

    /**
     * Retrieves cryptpassword
     *
     * @access  public
     * @return  string
     */
    function getCryptpassword() {
      return $this->cryptpassword;
    }
      
    /**
     * Sets cryptpassword
     *
     * @access  public
     * @param   string cryptpassword
     * @return  string the previous value
     */
    function setCryptpassword($cryptpassword) {
      return $this->_change('cryptpassword', $cryptpassword);
    }

    /**
     * Retrieves realname
     *
     * @access  public
     * @return  string
     */
    function getRealname() {
      return $this->realname;
    }
      
    /**
     * Sets realname
     *
     * @access  public
     * @param   string realname
     * @return  string the previous value
     */
    function setRealname($realname) {
      return $this->_change('realname', $realname);
    }

    /**
     * Retrieves disabledtext
     *
     * @access  public
     * @return  string
     */
    function getDisabledtext() {
      return $this->disabledtext;
    }
      
    /**
     * Sets disabledtext
     *
     * @access  public
     * @param   string disabledtext
     * @return  string the previous value
     */
    function setDisabledtext($disabledtext) {
      return $this->_change('disabledtext', $disabledtext);
    }

    /**
     * Retrieves mybugslink
     *
     * @access  public
     * @return  int
     */
    function getMybugslink() {
      return $this->mybugslink;
    }
      
    /**
     * Sets mybugslink
     *
     * @access  public
     * @param   int mybugslink
     * @return  int the previous value
     */
    function setMybugslink($mybugslink) {
      return $this->_change('mybugslink', $mybugslink);
    }

    /**
     * Retrieves person_id
     *
     * @access  public
     * @return  int
     */
    function getPerson_id() {
      return $this->person_id;
    }
      
    /**
     * Sets person_id
     *
     * @access  public
     * @param   int person_id
     * @return  int the previous value
     */
    function setPerson_id($person_id) {
      return $this->_change('person_id', $person_id);
    }

    /**
     * Retrieves refreshed_when
     *
     * @access  public
     * @return  &util.Date
     */
    function &getRefreshed_when() {
      return $this->refreshed_when;
    }
      
    /**
     * Sets refreshed_when
     *
     * @access  public
     * @param   &util.Date refreshed_when
     * @return  &util.Date the previous value
     */
    function &setRefreshed_when(&$refreshed_when) {
      return $this->_change('refreshed_when', $refreshed_when);
    }
  }
?>
