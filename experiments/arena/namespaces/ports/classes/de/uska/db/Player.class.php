<?php
/* This class is part of the XP framework
 *
 * $Id: Player.class.php 10654 2007-06-23 15:31:58Z kiesel $
 */

  namespace de::uska::db;
 
  ::uses('rdbms.DataSet', 'util.HashmapIterator');

  /**
   * Class wrapper for table player, database uska
   * (Auto-generated on Sat, 23 Jun 2007 16:52:13 +0200 by Alex)
   *
   * @purpose  Datasource accessor
   */
  class Player extends rdbms::DataSet {
    public
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
      $team_id            = 0,
      $bz_id              = 0;
  
    protected
      $cache= array(
        'Created_by' => array(),
        'Team' => array(),
        'Bz' => array(),
      );

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('uska.player');
        $peer->setConnection('uska');
        $peer->setIdentity('player_id');
        $peer->setPrimary(array('player_id'));
        $peer->setTypes(array(
          'player_id'           => array('%d', ::INT, FALSE),
          'player_type_id'      => array('%d', ::INT, FALSE),
          'firstname'           => array('%s', ::VARCHAR, FALSE),
          'lastname'            => array('%s', ::VARCHAR, FALSE),
          'username'            => array('%s', ::VARCHAR, TRUE),
          'password'            => array('%s', ::VARCHAR, TRUE),
          'email'               => array('%s', ::VARCHAR, TRUE),
          'position'            => array('%d', ::INT, TRUE),
          'created_by'          => array('%d', ::INT, TRUE),
          'lastchange'          => array('%s', ::DATETIME, FALSE),
          'changedby'           => array('%s', ::VARCHAR, FALSE),
          'team_id'             => array('%d', ::INT, FALSE),
          'bz_id'               => array('%d', ::INT, FALSE)
        ));
        $peer->setRelations(array(
          'Created_by' => array(
            'classname' => 'de.uska.db.Player',
            'key'       => array(
              'created_by' => 'player_id',
            ),
          ),
          'Team' => array(
            'classname' => 'de.uska.db.Team',
            'key'       => array(
              'team_id' => 'team_id',
            ),
          ),
          'Bz' => array(
            'classname' => 'de.uska.db.Progress',
            'key'       => array(
              'bz_id' => 'bz_id',
            ),
          ),
        ));
      }
    }  

    /**
     * Retrieve associated peer
     *
     * @return  rdbms.Peer
     */
    public static function getPeer() {
      return ::forName(__CLASS__);
    }

    /**
     * column factory
     *
     * @param   string name
     * @return  rdbms.Column
     * @throws  lang.IllegalArumentException
     */
    public static function column($name) {
      return ::forName(__CLASS__)->column($name);
    }
  
    /**
     * Gets an instance of this object by index "PRIMARY"
     * 
     * @param   int player_id
     * @return  de.uska.db.Player entitiy object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByPlayer_id($player_id) {
      $r= self::getPeer()->doSelect(new (array('player_id', $player_id, EQUAL)));
      return $r ? $r[0] : NULL;
    }

    /**
     * Gets an instance of this object by index "username"
     * 
     * @param   string username
     * @return  de.uska.db.Player entitiy object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByUsername($username) {
      $r= self::getPeer()->doSelect(new (array('username', $username, EQUAL)));
      return $r ? $r[0] : NULL;
    }

    /**
     * Gets an instance of this object by index "email"
     * 
     * @param   string email
     * @return  de.uska.db.Player entitiy object
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByEmail($email) {
      $r= self::getPeer()->doSelect(new (array('email', $email, EQUAL)));
      return $r ? $r[0] : NULL;
    }

    /**
     * Gets an instance of this object by index "created_by"
     * 
     * @param   int created_by
     * @return  de.uska.db.Player[] entity objects
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByCreated_by($created_by) {
      return self::getPeer()->doSelect(new (array('created_by', $created_by, EQUAL)));
    }

    /**
     * Gets an instance of this object by index "team_id"
     * 
     * @param   int team_id
     * @return  de.uska.db.Player[] entity objects
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByTeam_id($team_id) {
      return self::getPeer()->doSelect(new (array('team_id', $team_id, EQUAL)));
    }

    /**
     * Gets an instance of this object by index "bz_id"
     * 
     * @param   int bz_id
     * @return  de.uska.db.Player[] entity objects
     * @throws  rdbms.SQLException in case an error occurs
     */
    public static function getByBz_id($bz_id) {
      return self::getPeer()->doSelect(new (array('bz_id', $bz_id, EQUAL)));
    }

    /**
     * Retrieves player_id
     *
     * @return  int
     */
    public function getPlayer_id() {
      return $this->player_id;
    }
      
    /**
     * Sets player_id
     *
     * @param   int player_id
     * @return  int the previous value
     */
    public function setPlayer_id($player_id) {
      return $this->_change('player_id', $player_id);
    }

    /**
     * Retrieves player_type_id
     *
     * @return  int
     */
    public function getPlayer_type_id() {
      return $this->player_type_id;
    }
      
    /**
     * Sets player_type_id
     *
     * @param   int player_type_id
     * @return  int the previous value
     */
    public function setPlayer_type_id($player_type_id) {
      return $this->_change('player_type_id', $player_type_id);
    }

    /**
     * Retrieves firstname
     *
     * @return  string
     */
    public function getFirstname() {
      return $this->firstname;
    }
      
    /**
     * Sets firstname
     *
     * @param   string firstname
     * @return  string the previous value
     */
    public function setFirstname($firstname) {
      return $this->_change('firstname', $firstname);
    }

    /**
     * Retrieves lastname
     *
     * @return  string
     */
    public function getLastname() {
      return $this->lastname;
    }
      
    /**
     * Sets lastname
     *
     * @param   string lastname
     * @return  string the previous value
     */
    public function setLastname($lastname) {
      return $this->_change('lastname', $lastname);
    }

    /**
     * Retrieves username
     *
     * @return  string
     */
    public function getUsername() {
      return $this->username;
    }
      
    /**
     * Sets username
     *
     * @param   string username
     * @return  string the previous value
     */
    public function setUsername($username) {
      return $this->_change('username', $username);
    }

    /**
     * Retrieves password
     *
     * @return  string
     */
    public function getPassword() {
      return $this->password;
    }
      
    /**
     * Sets password
     *
     * @param   string password
     * @return  string the previous value
     */
    public function setPassword($password) {
      return $this->_change('password', $password);
    }

    /**
     * Retrieves email
     *
     * @return  string
     */
    public function getEmail() {
      return $this->email;
    }
      
    /**
     * Sets email
     *
     * @param   string email
     * @return  string the previous value
     */
    public function setEmail($email) {
      return $this->_change('email', $email);
    }

    /**
     * Retrieves position
     *
     * @return  int
     */
    public function getPosition() {
      return $this->position;
    }
      
    /**
     * Sets position
     *
     * @param   int position
     * @return  int the previous value
     */
    public function setPosition($position) {
      return $this->_change('position', $position);
    }

    /**
     * Retrieves created_by
     *
     * @return  int
     */
    public function getCreated_by() {
      return $this->created_by;
    }
      
    /**
     * Sets created_by
     *
     * @param   int created_by
     * @return  int the previous value
     */
    public function setCreated_by($created_by) {
      return $this->_change('created_by', $created_by);
    }

    /**
     * Retrieves lastchange
     *
     * @return  util.Date
     */
    public function getLastchange() {
      return $this->lastchange;
    }
      
    /**
     * Sets lastchange
     *
     * @param   util.Date lastchange
     * @return  util.Date the previous value
     */
    public function setLastchange($lastchange) {
      return $this->_change('lastchange', $lastchange);
    }

    /**
     * Retrieves changedby
     *
     * @return  string
     */
    public function getChangedby() {
      return $this->changedby;
    }
      
    /**
     * Sets changedby
     *
     * @param   string changedby
     * @return  string the previous value
     */
    public function setChangedby($changedby) {
      return $this->_change('changedby', $changedby);
    }

    /**
     * Retrieves team_id
     *
     * @return  int
     */
    public function getTeam_id() {
      return $this->team_id;
    }
      
    /**
     * Sets team_id
     *
     * @param   int team_id
     * @return  int the previous value
     */
    public function setTeam_id($team_id) {
      return $this->_change('team_id', $team_id);
    }

    /**
     * Retrieves bz_id
     *
     * @return  int
     */
    public function getBz_id() {
      return $this->bz_id;
    }
      
    /**
     * Sets bz_id
     *
     * @param   int bz_id
     * @return  int the previous value
     */
    public function setBz_id($bz_id) {
      return $this->_change('bz_id', $bz_id);
    }

    /**
     * Retrieves an array of all Player entities
     * referenced by player_id=>created_by
     *
     * @return  de.uska.db.Player[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getCreated_byList() {
      if ($this->cached['Created_by']) return array_values($this->cache['Created_by']);
      return lang::XPClass::forName('de.uska.db.Player')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new (
        array('player_id', $this->getCreated_by(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Player entities
     * referenced by player_id=>created_by
     *
     * @return  rdbms.ResultIterator<de.uska.db.Player
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getCreated_byIterator() {
      if ($this->cached['Created_by']) return new util::HashmapIterator($this->cache['Created_by']);
      return lang::XPClass::forName('de.uska.db.Player')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new (
        array('player_id', $this->getCreated_by(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Team entities
     * referenced by team_id=>team_id
     *
     * @return  de.uska.db.Team[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getTeamList() {
      if ($this->cached['Team']) return array_values($this->cache['Team']);
      return lang::XPClass::forName('de.uska.db.Team')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new (
        array('team_id', $this->getTeam_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Team entities
     * referenced by team_id=>team_id
     *
     * @return  rdbms.ResultIterator<de.uska.db.Team
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getTeamIterator() {
      if ($this->cached['Team']) return new util::HashmapIterator($this->cache['Team']);
      return lang::XPClass::forName('de.uska.db.Team')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new (
        array('team_id', $this->getTeam_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an array of all Progress entities
     * referenced by bz_id=>bz_id
     *
     * @return  de.uska.db.Progress[] entities
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getBzList() {
      if ($this->cached['Bz']) return array_values($this->cache['Bz']);
      return lang::XPClass::forName('de.uska.db.Progress')
        ->getMethod('getPeer')
        ->invoke()
        ->doSelect(new (
        array('bz_id', $this->getBz_id(), EQUAL)
      ));
    }

    /**
     * Retrieves an iterator for all Progress entities
     * referenced by bz_id=>bz_id
     *
     * @return  rdbms.ResultIterator<de.uska.db.Progress
     * @throws  rdbms.SQLException in case an error occurs
     */
    public function getBzIterator() {
      if ($this->cached['Bz']) return new util::HashmapIterator($this->cache['Bz']);
      return lang::XPClass::forName('de.uska.db.Progress')
        ->getMethod('getPeer')
        ->invoke()
        ->iteratorFor(new (
        array('bz_id', $this->getBz_id(), EQUAL)
      ));
    }
  }
?>