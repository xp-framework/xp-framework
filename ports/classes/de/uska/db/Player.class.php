<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses('rdbms.DataSet');
 
  /**
   * Class wrapper for table player, database uska
   * (Auto-generated on Sat, 02 Jul 2005 18:44:51 +0200 by alex)
   *
   * @purpose  Datasource accessor
   */
  class Player extends DataSet {
    var
      $player_id          = 0,
      $player_type_id     = 0,
      $firstname          = '',
      $lastname           = '',
      $username           = NULL,
      $password           = NULL,
      $email              = NULL,
      $position           = NULL,
      $created_by         = NULL,
      $lastchange         = NULL,
      $changedby          = '',
      $team_id            = 0;

    /**
     * Static initializer
     *
     * @model   static
     * @access  public
     */
    function __static() { 
      with ($peer= &Player::getPeer()); {
        $peer->setTable('uska.player');
        $peer->setConnection('uska');
        $peer->setIdentity('player_id');
        $peer->setPrimary(array('player_id'));
        $peer->setTypes(array(
          'player_id'           => '%d',
          'player_type_id'      => '%d',
          'firstname'           => '%s',
          'lastname'            => '%s',
          'username'            => '%s',
          'password'            => '%s',
          'email'               => '%s',
          'position'            => '%d',
          'created_by'          => '%d',
          'lastchange'          => '%s',
          'changedby'           => '%s',
          'team_id'             => '%d'
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
     * @param   int player_id
     * @return  &de.uska.db.Player object
     * @throws  rdbms.SQLException in case an error occurs
     */
    function &getByPlayer_id($player_id) {
      $peer= &Player::getPeer();
      return array_shift($peer->doSelect(new Criteria(array('player_id', $player_id, EQUAL))));
    }

    /**
     * Gets an instance of this object by index "username"
     *
     * @access  static
     * @param   string username
     * @return  &de.uska.db.Player object
     * @throws  rdbms.SQLException in case an error occurs
     */
    function &getByUsername($username) {
      $peer= &Player::getPeer();
      return array_shift($peer->doSelect(new Criteria(array('username', $username, EQUAL))));
    }

    /**
     * Gets an instance of this object by index "created_by"
     *
     * @access  static
     * @param   int created_by
     * @return  &de.uska.db.Player[] object
     * @throws  rdbms.SQLException in case an error occurs
     */
    function &getByCreated_by($created_by) {
      $peer= &Player::getPeer();
      return $peer->doSelect(new Criteria(array('created_by', $created_by, EQUAL)));
    }

    /**
     * Gets an instance of this object by index "team_id"
     *
     * @access  static
     * @param   int team_id
     * @return  &de.uska.db.Player[] object
     * @throws  rdbms.SQLException in case an error occurs
     */
    function &getByTeam_id($team_id) {
      $peer= &Player::getPeer();
      return $peer->doSelect(new Criteria(array('team_id', $team_id, EQUAL)));
    }

    /**
     * Retrieves player_id
     *
     * @access  public
     * @return  int
     */
    function getPlayer_id() {
      return $this->player_id;
    }
      
    /**
     * Sets player_id
     *
     * @access  public
     * @param   int player_id
     * @return  int the previous value
     */
    function setPlayer_id($player_id) {
      return $this->_change('player_id', $player_id);
    }

    /**
     * Retrieves player_type_id
     *
     * @access  public
     * @return  int
     */
    function getPlayer_type_id() {
      return $this->player_type_id;
    }
      
    /**
     * Sets player_type_id
     *
     * @access  public
     * @param   int player_type_id
     * @return  int the previous value
     */
    function setPlayer_type_id($player_type_id) {
      return $this->_change('player_type_id', $player_type_id);
    }

    /**
     * Retrieves firstname
     *
     * @access  public
     * @return  string
     */
    function getFirstname() {
      return $this->firstname;
    }
      
    /**
     * Sets firstname
     *
     * @access  public
     * @param   string firstname
     * @return  string the previous value
     */
    function setFirstname($firstname) {
      return $this->_change('firstname', $firstname);
    }

    /**
     * Retrieves lastname
     *
     * @access  public
     * @return  string
     */
    function getLastname() {
      return $this->lastname;
    }
      
    /**
     * Sets lastname
     *
     * @access  public
     * @param   string lastname
     * @return  string the previous value
     */
    function setLastname($lastname) {
      return $this->_change('lastname', $lastname);
    }

    /**
     * Retrieves username
     *
     * @access  public
     * @return  string
     */
    function getUsername() {
      return $this->username;
    }
      
    /**
     * Sets username
     *
     * @access  public
     * @param   string username
     * @return  string the previous value
     */
    function setUsername($username) {
      return $this->_change('username', $username);
    }

    /**
     * Retrieves password
     *
     * @access  public
     * @return  string
     */
    function getPassword() {
      return $this->password;
    }
      
    /**
     * Sets password
     *
     * @access  public
     * @param   string password
     * @return  string the previous value
     */
    function setPassword($password) {
      return $this->_change('password', $password);
    }

    /**
     * Retrieves email
     *
     * @access  public
     * @return  string
     */
    function getEmail() {
      return $this->email;
    }
      
    /**
     * Sets email
     *
     * @access  public
     * @param   string email
     * @return  string the previous value
     */
    function setEmail($email) {
      return $this->_change('email', $email);
    }

    /**
     * Retrieves position
     *
     * @access  public
     * @return  int
     */
    function getPosition() {
      return $this->position;
    }
      
    /**
     * Sets position
     *
     * @access  public
     * @param   int position
     * @return  int the previous value
     */
    function setPosition($position) {
      return $this->_change('position', $position);
    }

    /**
     * Retrieves sex
     *
     * @access  public
     * @return  int
     */
    function getSex() {
      return $this->sex;
    }
      
    /**
     * Sets sex
     *
     * @access  public
     * @param   int sex
     * @return  int the previous value
     */
    function setSex($sex) {
      return $this->_change('sex', $sex);
    }

    /**
     * Retrieves created_by
     *
     * @access  public
     * @return  int
     */
    function getCreated_by() {
      return $this->created_by;
    }
      
    /**
     * Sets created_by
     *
     * @access  public
     * @param   int created_by
     * @return  int the previous value
     */
    function setCreated_by($created_by) {
      return $this->_change('created_by', $created_by);
    }

    /**
     * Retrieves lastchange
     *
     * @access  public
     * @return  &util.Date
     */
    function &getLastchange() {
      return $this->lastchange;
    }
      
    /**
     * Sets lastchange
     *
     * @access  public
     * @param   &util.Date lastchange
     * @return  &util.Date the previous value
     */
    function &setLastchange(&$lastchange) {
      return $this->_change('lastchange', $lastchange);
    }

    /**
     * Retrieves changedby
     *
     * @access  public
     * @return  string
     */
    function getChangedby() {
      return $this->changedby;
    }
      
    /**
     * Sets changedby
     *
     * @access  public
     * @param   string changedby
     * @return  string the previous value
     */
    function setChangedby($changedby) {
      return $this->_change('changedby', $changedby);
    }

    /**
     * Retrieves team_id
     *
     * @access  public
     * @return  int
     */
    function getTeam_id() {
      return $this->team_id;
    }
      
    /**
     * Sets team_id
     *
     * @access  public
     * @param   int team_id
     * @return  int the previous value
     */
    function setTeam_id($team_id) {
      return $this->_change('team_id', $team_id);
    }
  }
?>
