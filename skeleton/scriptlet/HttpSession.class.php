<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses('scriptlet.Session');

  define('SESS_CREATE',    '__PHP_SessionCreatedAt');
  
  /**
   * If you are using this session class from within a HttpScriptlet
   * or a class extended from it, simply set the public variable
   * <pre>needSession</pre> to TRUE. The HttpScriptlet implementation
   * will take care of creating the session and checking its validity
   * for you. As default, the session is prepended to the query string
   * as in <pre>&psessionid=ID</pre>. Of course, you will have to prepend
   * this to all links appearing on your pages. You may use the predefined
   * constant <pre>SID</sid> which contains the value "psessionid=ID" to
   * accomplish this. If you are uncontent whith this, have a look at
   * <pre>sessionURIFormat</pre>. This will allow you to change the way
   * the session's ID is "inserted" into the query string if none exists.
   * Have a look at Apache's RewriteRule for building sexier query strings
   * 
   * As an underlying layer, PHP's builtin session functions are used.
   * Variables are kept in the auto-superglobal <pre>_SESSION</pre> Variable,
   * so a recent version of PHP (>4.2) is required.
   *
   * Please have a look into "Sessions and security" on PHP's session 
   * documentation page.
   *
   * @test    xp://net.xp_framework.unittest.scriptlet.HttpSessionTest
   * @purpose Session implementation                                                    
   */
  class HttpSession extends Object implements Session {
    public 
      $id    = '',
      $isNew = FALSE;
      
    /**
     * Constructor
     *
     */
    public function __construct() {
      ini_set('session.use_cookies', 0);
      session_name('psessionid');
    }
    
    /**
     * Initializes the session
     *
     * @param   string id session id
     * @return  bool
     */
    public function initialize($id) {
      if (!empty($id)) {
        $this->id= $id;
        session_id($this->id);
        @session_start();
        
        if (!isset($_SESSION[SESS_CREATE])) return FALSE;
        
        // OK
        return TRUE;
      }
      
      // New Session
      @session_start(); 
      session_regenerate_id();
      $this->isNew= TRUE;
      $this->id= session_id();
      
      // Remember when we started this session
      $_SESSION[SESS_CREATE]= time();
      return TRUE;
    }
    
    /**
     * Returns if this session is valid
     *
     * @return  bool valid
     */
    public function isValid() {
      return isset($_SESSION[SESS_CREATE]);
    }
    
    /**
     * Returns if this session is a new session
     *
     * @return  bool new
     */
    public function isNew() {
      return $this->isNew;
    }
    
    /**
     * Returns this session's Id
     *
     * @return  string id
     */
    public function getId() {
      return $this->id;
    }
    
    /**
     * Retrieves the time when this session was created, as Unix-
     * timestamp
     *
     * @return  int Unix-timestamp
     * @throws  lang.IllegalStateException when session is invalid
     */
    public function getCreationTime() {
      if (!$this->isValid()) throw new IllegalStateException('Session is invalid');
      return $_SESSION[SESS_CREATE];
    }
    
    /**
     * Resets the session and deletes all variables. The number of deleted
     * session variables is being returned
     *
     * @return  int
     * @throws  lang.IllegalStateException when session is invalid
     */
    public function reset() {
      if (!$this->isValid()) throw new IllegalStateException('Session is invalid');
      $size= sizeof($_SESSION) - 1;
      $_SESSION= array(SESS_CREATE => $_SESSION[SESS_CREATE]);
      return $size;
    }
    

    /**
     * Registers a variable. If another variable is already registered
     * under the specified name, it is replaced
     *
     * @param   string name
     * @param   mixed value Any data type
     * @throws  lang.IllegalStateException when session is invalid
     */
    public function putValue($name, $value) {
      if (!$this->isValid()) throw new IllegalStateException('Session is invalid');
      $_SESSION[$name]= serialize($value);
    }
    
    /**
     * Retrieves a value previously registered with the specified name
     * or the default value in case this name does not exist
     *
     * @param   string name
     * @param   mixed default default NULL 
     * @return  mixed value
     * @throws  lang.IllegalStateException when session is invalid
     */
    public function getValue($name, $default= NULL) {
      if (!$this->isValid()) throw new IllegalStateException('Session is invalid');
      if (isset($_SESSION[$name])) return unserialize($_SESSION[$name]); else return $default;
    }
    
    /**
     * Checks whether a value by specified name exists
     *
     * @param   string name 
     * @return  bool TRUE if the value exists, FALSE otherwiese
     */
    public function hasValue($name) {
      return isset($_SESSION[$name]);
    }
    
    /**
     * Removes a value from the session. If no value is found for
     * the specified name, nothing happens
     *
     * @param   string name The name of the value to delete
     */
    public function removeValue($name) {
      if (!$this->isValid()) throw new IllegalStateException('Session is invalid');
      if (isset($_SESSION[$name])) {
        $_SESSION[$name]= NULL;
      }
    }
    
    /**
     * Return an array of all names registered in this session
     *
     * @return  string[] names
     * @throws  lang.IllegalStateException when session is invalid
     */
    public function getValueNames() {
      if (!$this->isValid()) throw new IllegalStateException('Session is invalid');
      $names= array_keys($_SESSION);
      unset($names[array_search(SESS_CREATE, $names)]);
      return array_values($names);
    }
    
    /**
     * Invalidates a session and deletes all values
     *
     * @throws  lang.IllegalStateException when session is invalid
     */
    public function invalidate() {
      if (!$this->isValid()) throw new IllegalStateException('Session is invalid');
      @session_destroy();
      $_SESSION= array();
    }

    /**
     * Destructor. Calls session_write_close 
     *
     * @see     php://session_write_close
     */
    public function __destruct() {
      session_write_close();
    }
  }
?>
