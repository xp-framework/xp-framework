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
    public
      $username = NULL,
      $password = NULL;

    /**
     * Constructor
     *
     * @param   string username
     * @param   string password
     */
    public function __construct($username, $password) {
      $this->username= $username;
      $this->password= $password;
    }
  
    /**
     * Set username
     *
     * @param   string username The username
     */
    public function setUsername($username) {
      $this->username= $username;
    }

    /**
     * Get username
     *
     * @return  string
     */
    public function getUsername() {
      return $this->username;
    }

    /**
     * Set password
     *
     * @param   string password The password
     */
    public function setPassword($password) {
      $this->password= $password;
    }

    /**
     * Get password
     *
     * @return  string
     */
    public function getPassword() {
      return $this->password;
    }
  }
?>
