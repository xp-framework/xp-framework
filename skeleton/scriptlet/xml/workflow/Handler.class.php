<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Handler
   *
   * @see      xp://scriptlet.xml.workflow.AbstractState#addHandler
   * @purpose  Base class
   */
  class Handler extends Object {
    var
      $errors   = array(),
      $name     = '';

    /**
     * Constructor
     *
     * @access  public
     */
    function __construct() {
      $this->name= strtolower(get_class($this));
    }

    /**
     * Get name
     *
     * @access  public
     * @return  string
     */
    function getName() {
      return $this->name;
    }

    /**
     * Add an error
     *
     * @access  public
     * @param   string code
     * @param   string field default '*'
     * @param   mixed info default NULL
     */
    function addError($code, $field= '*', $info= NULL) {
      $this->errors[]= array($code, $field, $info);
      return FALSE;
    }
    
    /**
     * Check whether errors occured
     *
     * @access  public
     * @return  bool
     */
    function errorsOccured() {
      return !empty($this->errors);
    }
    
    /**
     * Returns whether this handler is active. Returns TRUE in this 
     * default implementation in case the request has a parameter named
     * __handler whose value contains this handler's name.
     *
     * @access  protected
     * @param   &scriptlet.xml.workflow.WorkflowScriptletRequest request
     * @return  bool
     */
    function isActive(&$request) {
      return ($request->getParam('__handler') == $this->name);
    }


    /**
     * Set up this handler. Called when this handler has not yet been
     * registered to the session
     *
     * @access  public
     * @param   &scriptlet.xml.workflow.WorkflowScriptletRequest request 
     */
    function setup(&$request) { }

    /**
     * Retrieve whether this handler needs data 
     *
     * @access  public
     * @param   &scriptlet.xml.workflow.WorkflowScriptletRequest request 
     * @return  bool
     */
    function needsData(&$request) {
      return TRUE;
    }  

    /**
     * Handle submitted data
     *
     * @access  public
     * @param   &scriptlet.xml.workflow.WorkflowScriptletRequest request 
     */
    function handleSubmittedData(&$request) {
      return FALSE;
    }
    
    /**
     * Finalize this handler
     *
     * @access  public
     * @param   &scriptlet.xml.workflow.WorkflowScriptletRequest request 
     * @param   &scriptlet.xml.XMLScriptletResponse response 
     */
    function finalize(&$request, &$response) {
    }  

  }
?>
