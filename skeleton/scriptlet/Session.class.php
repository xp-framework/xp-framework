<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  /**
   * A session provides a way of identifying users across a website
   * and having data associated to this person available on every page.
   * Since HTTP is a stateless protocol, sessions where implemented
   * to persist across more than one connection and request. A session
   * usually corresponds to exactly one user, "identifying" him/her
   * to the web pages displayed.
   *
   * Sessions may also be used to "cache" data from an RDBMS underlying
   * the web site. E.g., a user logs on to his service provider's 
   * administration site where he/she is allowed to configure a number
   * of - say - server settings. In this case, in the login site, we
   * read all of his/her contracts from the database and store these
   * objects to the session. On preceding pages displayed, these objects
   * are simply read from the session, saving time and database load.
   * Of course, in any case the data may have changed, we must unset
   * it from the session and reread it from the database.
   *
   * @see     php://session                                                 
   * @see     xp://scriptlet.HttpScriptlet                                  
   * @see     http://httpd.apache.org/docs/mod/mod_rewrite.html#RewriteRule 
   * @see     http://www.engelschall.com/pw/apache/rewriteguide/            
   */
  interface Session {

    /**
     * Initializes the session
     *
     * @param   string id session id
     * @return  bool
     */
    public function initialize($id);
    
    /**
     * Returns if this session is valid
     *
     * @return  bool valid
     */
    public function isValid();
    
    /**
     * Returns if this session is a new session
     *
     * @return  bool new
     */
    public function isNew();
    
    /**
     * Returns this session's Id
     *
     * @return  string id
     */
    public function getId();
    
    /**
     * Retrieves the time when this session was created, as Unix-
     * timestamp
     *
     * @return  int Unix-timestamp
     * @throws  lang.IllegalStateException when session is invalid
     */
    public function getCreationTime();
    
    /**
     * Resets the session and deletes all variables. The number of deleted
     * session variables is being returned
     *
     * @return  int
     * @throws  lang.IllegalStateException when session is invalid
     */
    public function reset();

    /**
     * Registers a variable. If another variable is already registered
     * under the specified name, it is replaced
     *
     * @param   string name
     * @param   var value
     * @throws  lang.IllegalStateException when session is invalid
     */
    public function putValue($name, $value);
    
    /**
     * Retrieves a value previously registered with the specified name
     * or the default value in case this name does not exist
     *
     * @param   string name
     * @param   var default default NULL 
     * @return  var value
     * @throws  lang.IllegalStateException when session is invalid
     */
    public function getValue($name, $default= NULL);
    
    /**
     * Checks whether a value by specified name exists
     *
     * @param   string name 
     * @return  bool TRUE if the value exists, FALSE otherwiese
     */
    public function hasValue($name);
    
    /**
     * Removes a value from the session. If no value is found for
     * the specified name, nothing happens
     *
     * @param   string name The name of the value to delete
     */
    public function removeValue($name);
    
    /**
     * Return an array of all names registered in this session
     *
     * @return  string[] names
     * @throws  lang.IllegalStateException when session is invalid
     */
    public function getValueNames();
    
    /**
     * Invalidates a session and deletes all values
     *
     * @throws  lang.IllegalStateException when session is invalid
     */
    public function invalidate();
  }
?>
