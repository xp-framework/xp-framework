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
     * @access  magic
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
