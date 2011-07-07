<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('scriptlet.Session');

  /**
   * @purpose  Helper class for test cases.
   */
  class DummySession extends Object implements Session {
    protected $data;

    /**
     * Initializes the session
     *
     * @param   string id session id
     * @return  bool
     */
    public function initialize($id) { }

    /**
     * Returns if this session is valid
     *
     * @return  bool valid
     */
    public function isValid() { }

    /**
     * Returns if this session is a new session
     *
     * @return  bool new
     */
    public function isNew() { }

    /**
     * Returns this session's Id
     *
     * @return  string id
     */
    public function getId() { }

    /**
     * Retrieves the time when this session was created, as Unix-
     * timestamp
     *
     * @return  int Unix-timestamp
     * @throws  lang.IllegalStateException when session is invalid
     */
    public function getCreationTime() { }

    /**
     * Resets the session and deletes all variables. The number of deleted
     * session variables is being returned
     *
     * @return  int
     * @throws  lang.IllegalStateException when session is invalid
     */
    public function reset() { }

    /**
     * Registers a variable. If another variable is already registered
     * under the specified name, it is replaced
     *
     * @param   string name
     * @param   var value
     * @throws  lang.IllegalStateException when session is invalid
     */
    public function putValue($name, $value) {
      $this->data[$name] = $value;
    }

    /**
     * Retrieves a value previously registered with the specified name
     * or the default value in case this name does not exist
     *
     * @param   string name
     * @param   var default default NULL
     * @return  var value
     * @throws  lang.IllegalStateException when session is invalid
     */
    public function getValue($name, $default= NULL) {
      if (isset($this->data[$name])) {
        return $this->data[$name];
      }

      return $default;
    }

    /**
     * Checks whether a value by specified name exists
     *
     * @param   string name
     * @return  bool TRUE if the value exists, FALSE otherwiese
     */
    public function hasValue($name) {
      return isset($this->data[$name]);
    }

    /**
     * Removes a value from the session. If no value is found for
     * the specified name, nothing happens
     *
     * @param   string name The name of the value to delete
     */
    public function removeValue($name) {
      unset($this->data[$name]);
    }

    /**
     * Return an array of all names registered in this session
     *
     * @return  string[] names
     * @throws  lang.IllegalStateException when session is invalid
     */
    public function getValueNames() { }

    /**
     * Invalidates a session and deletes all values
     *
     * @throws  lang.IllegalStateException when session is invalid
     */
    public function invalidate() { }
  }
?>
