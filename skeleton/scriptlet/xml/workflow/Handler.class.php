<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  // Handler stati
  define('HANDLER_SETUP',       'setup');
  define('HANDLER_FAILED',      'failed');
  define('HANDLER_INITIALIZED', 'initialized');
  define('HANDLER_ERRORS',      'errors');
  define('HANDLER_SUCCESS',     'success');
  define('HANDLER_RELOADED',    'reloaded');

  // Value storages
  define('HVAL_PERSISTENT',  0x0000);
  define('HVAL_FORMPARAM',   0x0001);

  /**
   * Handler
   *
   * @see      xp://scriptlet.xml.workflow.AbstractState#addHandler
   * @purpose  Base class
   */
  class Handler extends Object {
    var
      $wrapper  = NULL,
      $values   = array(HVAL_PERSISTENT => array(), HVAL_FORMPARAM => array()),
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
     * Creates a string representation of this handler
     *
     * @access  public
     * @return  string
     */
    function toString() {
      $s= sprintf(
        "%s(%s,wrapper=%s)@{\n",
        $this->getClassName(),
        $this->name,
        $this->wrapper ? $this->wrapper->getClassName() : '(null)'
      );
      foreach (array_keys($this->values[HVAL_PERSISTENT]) as $key) {
        $s.= sprintf("[%-20s] %s\n", $key, xp::typeOf($this->values[$key]));
      }
      return $s.'}';
    }

    /**
     * Set Wrapper
     *
     * @access  public
     * @param   &scriptlet.xml.workflow.Wrapper wrapper
     */
    function setWrapper(&$wrapper) {
      $this->wrapper= &$wrapper;
    }

    /**
     * Get Wrapper
     *
     * @access  public
     * @return  &scriptlet.xml.workflow.Wrapper
     */
    function &getWrapper() {
      return $this->wrapper;
    }

    /**
     * Check whether a wrapper is present
     *
     * @access  public
     * @return  bool
     */
    function hasWrapper() {
      return NULL != $this->wrapper;
    }
    
    /**
     * Set a value by a specified name
     *
     * @access  public
     * @param   string name
     * @param   mixed value
     */
    function setValue($name, $value) {
      $this->values[HVAL_PERSISTENT][$name]= $value;
    }

    /**
     * Set a form value by a specified name
     *
     * @access  public
     * @param   string name
     * @param   mixed value
     */
    function setFormValue($name, $value) {
      $this->values[HVAL_FORMPARAM][$name]= $value;
    }
    
    /**
     * Return all values
     *
     * @access  public
     * @return  array
     */
    function getValues() {
      return $this->values[HVAL_PERSISTENT];
    }

    /**
     * Return all form values
     *
     * @access  public
     * @return  array
     */
    function getFormValues() {
      return $this->values[HVAL_FORMPARAM];
    }
    
    /**
     * Retrieve a value by its name
     *
     * @access  public
     * @param   string name
     * @param   mixed default default NULL
     * @return  mixed value
     */
    function getValue($name, $default= NULL) {
      return (isset($this->values[HVAL_PERSISTENT][$name]) 
        ? $this->values[HVAL_PERSISTENT][$name] 
        : $default
      );
    }
    
    /**
     * Retrieve a form value by its name
     *
     * @access  public
     * @param   string name
     * @param   mixed default default NULL
     * @return  mixed value
     */
    function getFormValue($name, $default= NULL) {
      return (isset($this->values[HVAL_FORMPARAM][$name]) 
        ? $this->values[HVAL_FORMPARAM][$name] 
        : $default
      );
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
     * Handle error condition
     *
     * @access  public
     * @param   &scriptlet.xml.workflow.WorkflowScriptletRequest request 
     */
    function handleErrorCondition(&$request) {
      return FALSE;
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
    function finalize(&$request, &$response) { }
  }
?>
