<?php
/* This class is part of the XP framework
 *
 * $Id: BugzillaProfile.class.php 8971 2006-12-27 15:27:10Z friebe $
 */

  namespace org::bugzilla::db;
 
  ::uses('rdbms.DataSet');
 
  /**
   * Class wrapper for table profiles, database bugs218
   * (Auto-generated on Tue,  7 Jun 2005 11:47:41 +0200 by clang)
   *
   * @purpose  Datasource accessor
   */
  class BugzillaProfile extends rdbms::DataSet {
    public
      $userid             = 0,
      $login_name         = '',
      $cryptpassword      = NULL,
      $realname           = NULL,
      $disabledtext       = '',
      $mybugslink         = 0,
      $person_id          = NULL,
      $refreshed_when     = NULL,
      $extern_id          = NULL;

    /**
     * Static initializer
     *
     */
    public static function __static() { 
      with ($peer= ::getPeer()); {
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
     * @return  &rdbms.Peer
     */
    public function getPeer() {
      return ::forName(__CLASS__);
    }
  
    /**
     * Gets an instance of this object by index "PRIMARY"
     *
     * @param   int userid
     * @return  &org.bugzilla.db.BugzillaProfile object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getByUserid($userid) {
      $peer= ::getPeer();
      return array_shift($peer->doSelect(new rdbms::Criteria(array('userid', $userid, EQUAL))));
    }

    /**
     * Gets an instance of this object by index "login_name"
     *
     * @param   string login_name
     * @return  &org.bugzilla.db.BugzillaProfile object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getByLogin_name($login_name) {
      $peer= ::getPeer();
      return array_shift($peer->doSelect(new rdbms::Criteria(array('login_name', $login_name, EQUAL))));
    }
    
    /**
     * Gets an instance of this object by person_id
     *
     * @param   int person_id
     * @return  &org.bugzilla.db.BugzillaProfile object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getByPerson_id($person_id) {
      $peer= ::getPeer();
      return array_shift($peer->doSelect(new rdbms::Criteria(array('person_id', $person_id, EQUAL))));
    }

    /**
     * Retrieves userid
     *
     * @return  int
     */
    public function getUserid() {
      return $this->userid;
    }
      
    /**
     * Sets userid
     *
     * @param   int userid
     * @return  int the previous value
     */
    public function setUserid($userid) {
      return $this->_change('userid', $userid);
    }

    /**
     * Retrieves login_name
     *
     * @return  string
     */
    public function getLogin_name() {
      return $this->login_name;
    }
      
    /**
     * Sets login_name
     *
     * @param   string login_name
     * @return  string the previous value
     */
    public function setLogin_name($login_name) {
      return $this->_change('login_name', $login_name);
    }

    /**
     * Retrieves cryptpassword
     *
     * @return  string
     */
    public function getCryptpassword() {
      return $this->cryptpassword;
    }
      
    /**
     * Sets cryptpassword
     *
     * @param   string cryptpassword
     * @return  string the previous value
     */
    public function setCryptpassword($cryptpassword) {
      return $this->_change('cryptpassword', $cryptpassword);
    }

    /**
     * Retrieves realname
     *
     * @return  string
     */
    public function getRealname() {
      return $this->realname;
    }
      
    /**
     * Sets realname
     *
     * @param   string realname
     * @return  string the previous value
     */
    public function setRealname($realname) {
      return $this->_change('realname', $realname);
    }

    /**
     * Retrieves disabledtext
     *
     * @return  string
     */
    public function getDisabledtext() {
      return $this->disabledtext;
    }
      
    /**
     * Sets disabledtext
     *
     * @param   string disabledtext
     * @return  string the previous value
     */
    public function setDisabledtext($disabledtext) {
      return $this->_change('disabledtext', $disabledtext);
    }

    /**
     * Retrieves mybugslink
     *
     * @return  int
     */
    public function getMybugslink() {
      return $this->mybugslink;
    }
      
    /**
     * Sets mybugslink
     *
     * @param   int mybugslink
     * @return  int the previous value
     */
    public function setMybugslink($mybugslink) {
      return $this->_change('mybugslink', $mybugslink);
    }

    /**
     * Retrieves person_id
     *
     * @return  int
     */
    public function getPerson_id() {
      return $this->person_id;
    }
      
    /**
     * Sets person_id
     *
     * @param   int person_id
     * @return  int the previous value
     */
    public function setPerson_id($person_id) {
      return $this->_change('person_id', $person_id);
    }

    /**
     * Retrieves refreshed_when
     *
     * @return  &util.Date
     */
    public function getRefreshed_when() {
      return $this->refreshed_when;
    }
      
    /**
     * Sets refreshed_when
     *
     * @param   &util.Date refreshed_when
     * @return  &util.Date the previous value
     */
    public function setRefreshed_when($refreshed_when) {
      return $this->_change('refreshed_when', $refreshed_when);
    }

   /**
     * Set Extern_id
     *
     * @param   string extern_id
     * @return  string the previous value
     */
    public function setExtern_id($extern_id) {
      return $this->_change('extern_id', $extern_id);
    }

    /**
     * Get Extern_id
     *
     * @return  string
     */
    public function getExtern_id() {
      return $this->extern_id;
    }
  }
?>
