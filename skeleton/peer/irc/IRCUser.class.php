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
    var
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
    function __construct($nick, $realname= NULL, $username= NULL, $hostname= 'localhost') {
      
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
    function setNick($nick) {
      $this->nick= $nick;
    }

    /**
     * Get Nick
     *
     * @access  public
     * @return  string
     */
    function getNick() {
      return $this->nick;
    }

    /**
     * Set Username
     *
     * @access  public
     * @param   string username
     */
    function setUsername($username) {
      $this->username= $username;
    }

    /**
     * Get Username
     *
     * @access  public
     * @return  string
     */
    function getUsername() {
      return $this->username;
    }

    /**
     * Set Hostname
     *
     * @access  public
     * @param   string hostname
     */
    function setHostname($hostname) {
      $this->hostname= $hostname;
    }

    /**
     * Get Hostname
     *
     * @access  public
     * @return  string
     */
    function getHostname() {
      return $this->hostname;
    }

    /**
     * Set Realname
     *
     * @access  public
     * @param   string realname
     */
    function setRealname($realname) {
      $this->realname= $realname;
    }

    /**
     * Get Realname
     *
     * @access  public
     * @return  string
     */
    function getRealname() {
      return $this->realname;
    }
  }
?>
