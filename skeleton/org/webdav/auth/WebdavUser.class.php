<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Represents the Webdav user
   *
   * @purpose  Webdav user
   */
  class WebdavUser extends Object {
    var
      $username = NULL,
      $password = NULL;

    /**
     * Constructor
     *
     * @access  public
     * @param   string username
     * @param   string password
     */
    function __construct($username, $password) {
      $this->username= $username;
      $this->password= $password;
    }
  
    /**
     * Set username
     *
     * @access  public
     * @param   string username The username
     */
    function setUsername($username) {
      $this->username= $username;
    }

    /**
     * Get username
     *
     * @access  public
     * @return  string
     */
    function getUsername() {
      return $this->username;
    }

    /**
     * Set password
     *
     * @access  public
     * @param   string password The password
     */
    function setPassword($password) {
      $this->password= $password;
    }

    /**
     * Get password
     *
     * @access  public
     * @return  string
     */
    function getPassword() {
      return $this->password;
    }
  }
?>
