<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses('rdbms.DataSet');
 
  /**
   * Class wrapper for table profiles, database bugs
   * (Auto-generated on Mon, 18 Oct 2004 10:23:06 +0200 by thekid)
   *
   * @purpose  Datasource accessor
   */
  class BugzillaProfile extends DataSet {
    var
      $userid             = 0,
      $login_name         = '',
      $cryptpassword      = NULL,
      $realname           = NULL,
      $groupset           = 0,
      $disabledtext       = '',
      $mybugslink         = 0,
      $blessgroupset      = 0,
      $emailflags         = NULL,
      $person_id          = NULL;

    /**
     * Static initializer
     *
     * @model   static
     * @access  public
     */
    function __static() { 
      with ($peer= &BugzillaProfile::getPeer()); {
        $peer->setTable('bugs.profiles');
        $peer->setConnection('bugzilla');
        $peer->setIdentity('userid');
        $peer->setPrimary(array('userid', 'login_name'));
        $peer->setTypes(array(
          'userid'              => '%d',
          'login_name'          => '%s',
          'cryptpassword'       => '%s',
          'realname'            => '%s',
          'groupset'            => '%d',
          'disabledtext'        => '%s',
          'mybugslink'          => '%d',
          'blessgroupset'       => '%d',
          'emailflags'          => '%s',
          'person_id'           => '%d'
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
     * Retrieves groupset
     *
     * @access  public
     * @return  int
     */
    function getGroupset() {
      return $this->groupset;
    }
      
    /**
     * Sets groupset
     *
     * @access  public
     * @param   int groupset
     * @return  int the previous value
     */
    function setGroupset($groupset) {
      return $this->_change('groupset', $groupset);
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
     * Retrieves blessgroupset
     *
     * @access  public
     * @return  int
     */
    function getBlessgroupset() {
      return $this->blessgroupset;
    }
      
    /**
     * Sets blessgroupset
     *
     * @access  public
     * @param   int blessgroupset
     * @return  int the previous value
     */
    function setBlessgroupset($blessgroupset) {
      return $this->_change('blessgroupset', $blessgroupset);
    }

    /**
     * Retrieves emailflags
     *
     * @access  public
     * @return  string
     */
    function getEmailflags() {
      return $this->emailflags;
    }
      
    /**
     * Sets emailflags
     *
     * @access  public
     * @param   string emailflags
     * @return  string the previous value
     */
    function setEmailflags($emailflags) {
      return $this->_change('emailflags', $emailflags);
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
  }
?>
