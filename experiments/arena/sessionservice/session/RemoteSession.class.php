<?php
/* This class is part of the XP framework
 *
 * $Id: SPSession.class.php 51769 2006-12-28 13:33:19Z kiesel $
 */
 
  uses(
    'scriptlet.HttpSession', 
    'session.SessionConnection', 
    'session.RemoteSessionConstants'
  );
  
  /**
   * Remotesession
   *
   * @see   xp://scriptlet.HttpSession
   */
  class RemoteSession extends HttpSession {
    public
      $conn   = NULL,
      $isNew  = FALSE,
      $timeout= 0;
  
    /**
     * Constructor
     *
     * @param   string server
     * @param   int port
     * @param   int timeout time in seconds until session is auto-destroyed
     */
    public function __construct($server= '172.17.29.15', $port= 2001, $timeout= 86400) {
      $this->conn= new SessionConnection();
      $this->remote= $server;
      $this->port= $port;
      $this->timeout= $timeout;
      parent::__construct();
    }

    /**
     * Initializes the session
     *
     * @param   string id session id
     * @return  bool
     */
    public function initialize($id) {
      if (NULL === $id) {
        $this->conn->connectTo($this->remote, $this->port);
        $this->id= $this->conn->command(RemoteSessionConstants::CREATE, array($this->timeout));
        $this->isNew= TRUE;
      } else {
        $this->conn->connectTo(implode('.', sscanf($id, '%2x%2x%2x%2x')), $this->port);
        $this->conn->command(RemoteSessionConstants::INIT, array($id));
        $this->id= $id;
        $this->isNew= FALSE;
      }
    }
    
    /**
     * Returns if this session is valid
     *
     * @return  bool valid
     */
    public function isValid() {
      return $this->conn->command(RemoteSessionConstants::VALID, array($this->id));
    }
    
    /**
     * Returns whether the current session has been created in
     * this instance.
     *
     * @return  bool
     */
    public function isNew() {
      return $this->isNew;
    }
    
    /**
     * Retreives the time when this session was created, as Unix-
     * timestamp
     *
     * @return  int Unix-timestamp
     * @throws  lang.IllegalStateException when session is invalid
     */
    public function getCreationTime() {
      return -1;
    }
    
    /**
     * Resets the session and deletes all variables 
     *
     * @throws  lang.IllegalStateException when session is invalid
     */
    public function reset() {
      return $this->conn->command(RemoteSessionConstants::RESET, array($this->id));
    }

    /**
     * Registers a variable. If another variable is already registered
     * under the specified name, it is replaced
     *
     * @param   string name
     * @param   mixed& value Any data type
     * @param   string stor default HANNAH_TMP
     * @throws  lang.IllegalStateException when session is invalid
     */
    public function putValue($name, $value, $stor= HANNAH_TMP) {
      $this->conn->command(
        RemoteSessionConstants::WRITE, 
        array($this->id, $name), 
        new ByteCountedString(serialize($value))
      );
    }
    
    /**
     * Retreives a value previously registered with the specified name
     * or the default value in case this name does not exist
     *
     * @param   string name
     * @param   mixed default default NULL 
     * @param   string stor default HANNAH_TMP
     * @return  &mixed value
     * @throws  lang.IllegalStateException when session is invalid
     */
    public function getValue($name, $default= NULL, $stor= HANNAH_TMP) {
      return $this->conn->command(RemoteSessionConstants::READ, array($this->id, $name));
    }
    
    /**
     * Checks whether a value by specified name exists
     *
     * @param   string name 
     * @param   string stor default HANNAH_TMP
     * @return  bool TRUE if the value exists, FALSE otherwiese
     */
    public function hasValue($name, $stor= HANNAH_TMP) {
      return $this->conn->command(RemoteSessionConstants::EXISTS, array($this->id, $name));
    }
    
    /**
     * Removes a value from the session. If no value is found for
     * the specified name, nothing happens
     *
     * @param   name The name of the value to delete
     * @throws  lang.IllegalStateException when session is invalid
     */
    public function removeValue($name, $stor= HANNAH_TMP) {
      return $this->conn->command(RemoteSessionConstants::DELETE, array($this->id, $name));
    }
    
    /**
     * Return an array of all names registered in this session
     *
     * @return  &string[] names
     * @throws  lang.IllegalStateException when session is invalid
     */
    public function getValueNames($stor= HANNAH_TMP) {
      return $this->conn->command(RemoteSessionConstants::KEYS, array($this->id));
    }
    
    /**
     * Invalidates a session and deletes all values
     *
     * @throws  lang.IllegalStateException when session is invalid
     */
    public function invalidate() {
      return $this->conn->command(RemoteSessionConstants::KILL, array($this->id));
    }
    
    /**
     * Tries to lock a session. A lock is an exclusive read
     * and write lock, and can only be aquired when no other process
     * has currently attached this session.
     *
     * @return  bool TRUE if session could be locked
     * @throws  lang.IllegalStateException when session is invalid
     */
    public function lock() {
      return $this->conn->lock($this->id);
    }
    
    /**
     * Unlocks a session.
     *
     * @return  bool TRUE if session could be unlocked.
     */
    public function unlock() {
      return $this->conn->unlock($this->id);
    }
  }
?>
