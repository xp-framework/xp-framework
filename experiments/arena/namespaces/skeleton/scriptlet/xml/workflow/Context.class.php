<?php
/* This class is part of the XP framework
 *
 * $Id: Context.class.php 10594 2007-06-11 10:04:54Z friebe $
 */

  namespace scriptlet::xml::workflow;

  /**
   * Context
   *
   * @purpose  Context
   */
  class Context extends lang::Object {
    public
      $_changed  = FALSE;

    /**
     * Set changed flag
     *
     * @param   bool changed default TRUE
     */
    public function setChanged($changed= ) {
      $this->_changed= $changed;
    }

    /**
     * Get changed flag
     *
     * @return  bool
     */
    public function getChanged() {
      return $this->_changed;
    }
    
    /**
     * Setup context. 
     *
     * Does nothing in this default implementation.
     *
     * @param   scriptlet.HttpScriptletRequest request
     * @throws  lang.IllegalStateException to indicate an error
     * @throws  lang.IllegalAccessException to indicate an error
     */
    public function setup($request) { }

    /**
     * Process the context.
     *
     * Does nothing in this default implementation.
     *
     * @param   scriptlet.HttpScriptletRequest request
     * @throws  lang.IllegalStateException to indicate an error
     * @throws  lang.IllegalAccessException to indicate an error
     */
    public function process($request) { }
    
    /**
     * Sleep function. Returns an array of the names of those member 
     * variables that should be serialized to the session. In this
     * implementation, it returns all public members (members whose names
     * do not begin with an underscore).
     *
     * @return  string[]
     */
    public function __sleep() {
      return array_filter(
        array_keys(get_class_vars(get_class($this))), 
        create_function('$k', 'return "_" != $k{0};')
      );
    }
    
    /**
     * Insert formresult nodes.
     *
     * @param   scriptlet.xml.XMLScriptletResponse response
     */
    public function insertStatus($response) { }
  }
?>
