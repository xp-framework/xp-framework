<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  define('TYPE_ASCII',  'A');
  define('TYPE_BINARY', 'I');

  /**
   * Holds information about an FTP session
   *
   * @see      xp://peer.ftp.server.FtpConnectionListener
   * @purpose  Session information
   */
  class FtpSession extends Object {
    var
      $username       = '',
      $authenticated  = FALSE,
      $type           = TYPE_ASCII,
      $tempVar        = array();

    /**
     * Returns type name depending on the type
     *
     * The following codes are assigned:
     * <pre>
     * A = ASCII (text files)
     * N = Non-print (files that have no vertical format controls such 
     *     as carriage returns and line feeds)
     * T = Telnet format effectors (files that have ASCII or EBCDIC 
     *     vertical format controls)
     * E = EBCDIC (files being transferred between systems that use 
     *     EBCDIC for internal character representation)
     * C = Carriage Control (ASA) (files that contain ASA [FORTRAN] 
     *     vertical format controls)
     * I = Image (binary files)
     * L = Local byte size (files that need to be transferred using 
     *     specific non-standard size bytes)
     * </pre>
     *
     * The default representation type is ASCII Non-print. This 
     * implementation supports ASCII (A) and BINARY (I)
     *
     * @access  protected
     * @return  string
     */
    function typeName() {
      static $names= array(
        TYPE_ASCII  => 'ASCII',
        TYPE_BINARY => 'BINARY'
      );
      return $names[$this->type];
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
     * Set Authenticated
     *
     * @access  public
     * @param   bool authenticated
     */
    function setAuthenticated($authenticated) {
      $this->authenticated= $authenticated;
    }

    /**
     * Retrieve whether this session is authenticated
     *
     * @access  public
     * @return  bool
     */
    function isAuthenticated() {
      return $this->authenticated;
    }

    /**
     * Set Type
     *
     * @access  public
     * @param   int type
     */
    function setType($type) {
      $this->type= $type;
    }

    /**
     * Set temporary variable
     *
     * @access  public
     * @param   string name
     * @param   mixed value
     */
    function setTempVar($name, $value) {
      $this->tempVar[$name]= $value;
    }

    /**
     * Get value of a temporary variable
     *
     * @access  public
     * @param   string name
     * @return  mixed value
     */    
    function getTempVar($name) {
      return $this->tempVar[$name];
    }
    
    /**
     * Remove a temporary variable
     *
     * @access  public
     * @param   string name
     */    
    function removeTempVar($name) {
      unset($this->tempVar[$name]);
    }

    /**
     * Get Type
     *
     * @access  public
     * @return  int
     */
    function getType() {
      return $this->type;
    }
  }
?>
