<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  /**
   * Context
   *
   * @purpose  Context
   */
  class Context extends Object {
    var
      $_changed  = FALSE;

    /**
     * Set changed flag
     *
     * @access  public
     * @param   bool changed default TRUE
     */
    function setChanged($changed= TRUE) {
      $this->_changed= $changed;
    }

    /**
     * Get changed flag
     *
     * @access  public
     * @return  bool
     */
    function getChanged() {
      return $this->_changed;
    }
    
    /**
     * Setup context. 
     *
     * Does nothing in this default implementation.
     *
     * @access  public
     * @param   &scriptlet.HttpScriptletRequest request
     * @throws  lang.IllegalStateException to indicate an error
     * @throws  lang.IllegalAccessException to indicate an error
     */
    function setup(&$request) { }

    /**
     * Process the context.
     *
     * Does nothing in this default implementation.
     *
     * @access  public
     * @param   &scriptlet.HttpScriptletRequest request
     * @throws  lang.IllegalStateException to indicate an error
     * @throws  lang.IllegalAccessException to indicate an error
     */
    function process(&$request) { }
    
    /**
     * Sleep function. Returns an array of the names of those member 
     * variables that should be serialized to the session. In this
     * implementation, it returns all public members (members whose names
     * do not begin with an underscore).
     *
     * @access  protected
     * @return  string[]
     */
    function __sleep() {
      return array_filter(
        array_keys(get_class_vars(get_class($this))), 
        create_function('$k', 'return "_" != $k{0};')
      );
    }
    
    /**
     * Insert formresult nodes.
     *
     * @access  public
     * @param   &scriptlet.xml.XMLScriptletResponse response
     */
    function insertStatus(&$response) { }
  }
?>
