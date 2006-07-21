<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Represents an IRC User
   *
   * @see      xp://peer.irc.IRCConnection
   * @purpose  User
   */
  class IRCUser extends Object {
    public
      $nick     = '',
      $username = '',
      $hostname = '',
      $realname = '';
      
    /**
     * Constructor
     *
     * @access  public
     * @param   string nick
     * @param   string realname default NULL (defaults to nickname)
     * @param   string username default NULL (defaults to current username)
     * @param   string hostname default 'localhost'
     */
    public function __construct($nick, $realname= NULL, $username= NULL, $hostname= 'localhost') {
      
      $this->nick= $nick;
      $this->realname= $realname ? $realname : $nick;
      $this->username= $username ? $username : get_current_user();
      $this->hostname= $hostname;
    }

    /**
     * Set Nick
     *
     * @access  public
     * @param   string nick
     */
    public function setNick($nick) {
      $this->nick= $nick;
    }

    /**
     * Get Nick
     *
     * @access  public
     * @return  string
     */
    public function getNick() {
      return $this->nick;
    }

    /**
     * Set Username
     *
     * @access  public
     * @param   string username
     */
    public function setUsername($username) {
      $this->username= $username;
    }

    /**
     * Get Username
     *
     * @access  public
     * @return  string
     */
    public function getUsername() {
      return $this->username;
    }

    /**
     * Set Hostname
     *
     * @access  public
     * @param   string hostname
     */
    public function setHostname($hostname) {
      $this->hostname= $hostname;
    }

    /**
     * Get Hostname
     *
     * @access  public
     * @return  string
     */
    public function getHostname() {
      return $this->hostname;
    }

    /**
     * Set Realname
     *
     * @access  public
     * @param   string realname
     */
    public function setRealname($realname) {
      $this->realname= $realname;
    }

    /**
     * Get Realname
     *
     * @access  public
     * @return  string
     */
    public function getRealname() {
      return $this->realname;
    }
  }
?>
