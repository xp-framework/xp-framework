<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('util.Date');

  /**
   * Represent an FTP entry
   *
   * @see      xp://peer.ftp.FtpDir#getEntry
   * @purpose  Base class
   */
  class FtpEntry extends Object {
    var
      $name         = '',
      $permissions  = 0,
      $numlinks     = 0,
      $user         = '',
      $group        = '',
      $size         = 0,
      $date         = NULL;

    var
      $_hdl     = NULL;
      
    /**
     * Constructor
     *
     * @access  public
     * @param   string name
     * @param   resource hdl default NULL
     */
    function __construct($name, $hdl= NULL) {
      $this->name= $name;
      $this->_hdl= $hdl;
    }

    /**
     * Set Permissions
     *
     * @access  public
     * @param   int permissions
     */
    function setPermissions($permissions) {
      $this->permissions= $permissions;
    }

    /**
     * Get Permissions
     *
     * @access  public
     * @return  int
     */
    function getPermissions() {
      return $this->permissions;
    }

    /**
     * Set Numlinks
     *
     * @access  public
     * @param   int numlinks
     */
    function setNumlinks($numlinks) {
      $this->numlinks= $numlinks;
    }

    /**
     * Get Numlinks
     *
     * @access  public
     * @return  int
     */
    function getNumlinks() {
      return $this->numlinks;
    }

    /**
     * Set User
     *
     * @access  public
     * @param   string user
     */
    function setUser($user) {
      $this->user= $user;
    }

    /**
     * Get User
     *
     * @access  public
     * @return  string
     */
    function getUser() {
      return $this->user;
    }

    /**
     * Set Group
     *
     * @access  public
     * @param   string group
     */
    function setGroup($group) {
      $this->group= $group;
    }

    /**
     * Get Group
     *
     * @access  public
     * @return  string
     */
    function getGroup() {
      return $this->group;
    }

    /**
     * Set Size
     *
     * @access  public
     * @param   int size
     */
    function setSize($size) {
      $this->size= $size;
    }

    /**
     * Get Size
     *
     * @access  public
     * @return  int
     */
    function getSize() {
      return $this->size;
    }

    /**
     * Set Date
     *
     * @access  public
     * @param   &util.Date date
     */
    function setDate(&$date) {
      $this->date= &$date;
    }

    /**
     * Get Date
     *
     * @access  public
     * @return  &util.Date
     */
    function &getDate() {
      return $this->date;
    }

    /**
     * Set Name
     *
     * @access  public
     * @param   mixed name
     */
    function setName($name) {
      $this->name= $name;
    }

    /**
     * Get Name
     *
     * @access  public
     * @return  string
     */
    function getName() {
      return $this->name;
    }
  }
?>
