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
  define('HANDLER_CANCELLED',   'cancelled');

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
    public
      $wrapper      = NULL,
      $values       = array(HVAL_PERSISTENT => array(), HVAL_FORMPARAM => array()),
      $errors       = array(),
      $identifier   = '',
      $name         = '';

    /**
     * Constructor
     *
     */
    public function __construct() {
      $this->name= strtolower(get_class($this));
    }
    
    /**
     * Creates a string representation of this handler
     *
     * @return  string
     */
    public function toString() {
      $s= sprintf(
        "%s@{\n".
        "  [name               ] %s\n".
        "  [identifier         ] %s\n".
        "  [wrapper            ] %s\n",
        $this->getClassName(),
        $this->name,
        $this->identifier,
        $this->wrapper ? $this->wrapper->getClassName() : '(null)'
      );
      foreach (array_keys($this->values[HVAL_PERSISTENT]) as $key) {
        $s.= sprintf("  [%-20s] %s\n", $key, xp::typeOf($this->values[$key]));
      }
      return $s.'}';
    }

    /**
     * Set Wrapper
     *
     * @param   scriptlet.xml.workflow.Wrapper wrapper
     */
    public function setWrapper($wrapper) {
      $this->wrapper= $wrapper;
    }

    /**
     * Get Wrapper
     *
     * @return  scriptlet.xml.workflow.Wrapper
     */
    public function getWrapper() {
      return $this->wrapper;
    }

    /**
     * Check whether a wrapper is present
     *
     * @return  bool
     */
    public function hasWrapper() {
      return NULL != $this->wrapper;
    }
    
    /**
     * Set a value by a specified name
     *
     * @param   string name
     * @param   mixed value
     */
    public function setValue($name, $value) {
      $this->values[HVAL_PERSISTENT][$name]= $value;
    }

    /**
     * Set a form value by a specified name
     *
     * @param   string name
     * @param   mixed value
     */
    public function setFormValue($name, $value) {
      $this->values[HVAL_FORMPARAM][$name]= $value;
    }
    
    /**
     * Return all values
     *
     * @return  array
     */
    public function getValues() {
      return $this->values[HVAL_PERSISTENT];
    }

    /**
     * Return all form values
     *
     * @return  array
     */
    public function getFormValues() {
      return $this->values[HVAL_FORMPARAM];
    }
    
    /**
     * Retrieve a value by its name
     *
     * @param   string name
     * @param   mixed default default NULL
     * @return  mixed value
     */
    public function getValue($name, $default= NULL) {
      return (isset($this->values[HVAL_PERSISTENT][$name]) 
        ? $this->values[HVAL_PERSISTENT][$name] 
        : $default
      );
    }
    
    /**
     * Retrieve a form value by its name
     *
     * @param   string name
     * @param   mixed default default NULL
     * @return  mixed value
     */
    public function getFormValue($name, $default= NULL) {
      return (isset($this->values[HVAL_FORMPARAM][$name]) 
        ? $this->values[HVAL_FORMPARAM][$name] 
        : $default
      );
    }

    /**
     * Get name
     *
     * @return  string
     */
    public function getName() {
      return $this->name;
    }

    /**
     * Get identifier. Returns name in this default implementation.
     * Overwrite in subclasses.
     *
     * @param   scriptlet.xml.workflow.WorkflowScriptletRequest request
     * @param   scriptlet.xml.Context context
     * @return  string
     */
    public function identifierFor($request, $context) {
      return $this->name;
    }

    /**
     * Add an error
     *
     * @param   string code
     * @param   string field default '*'
     * @param   mixed info default NULL
     */
    public function addError($code, $field= '*', $info= NULL) {
      $this->errors[]= array($code, $field, $info);
      return FALSE;
    }
    
    /**
     * Check whether errors occured
     *
     * @return  bool
     */
    public function errorsOccured() {
      return !empty($this->errors);
    }
    
    /**
     * Returns whether this handler is active. Returns TRUE in this 
     * default implementation in case the request has a parameter named
     * __handler whose value contains this handler's name.
     *
     * @param   scriptlet.xml.workflow.WorkflowScriptletRequest request
     * @param   scriptlet.xml.Context context
     * @return  bool
     */
    public function isActive($request, $context) {
      return ($request->getParam('__handler') == $this->identifier);
    }
    
    /**
     * Set up this handler. Called when this handler has not yet been
     * registered to the session
     *
     * Return TRUE to indicate success, FALSE to signal failure.
     *
     * @param   scriptlet.xml.workflow.WorkflowScriptletRequest request 
     * @param   scriptlet.xml.Context context
     * @return  bool
     */
    public function setup($request, $context) { 
      return TRUE;
    }

    /**
     * Retrieve whether this handler needs data 
     *
     * @param   scriptlet.xml.workflow.WorkflowScriptletRequest request 
     * @param   scriptlet.xml.Context context
     * @return  bool
     */
    public function needsData($request, $context) {
      return TRUE;
    }  
    
    /**
     * Retrieve whether this handler needs to be cancelled.
     *
     * @param   scriptlet.xml.workflow.WorkflowScriptletRequest request 
     * @param   scriptlet.xml.Context context
     * @return  bool
     */
    public function needsCancel($request, $context) {
      return FALSE;
    }    

    /**
     * Handle error condition
     *
     * @param   scriptlet.xml.workflow.WorkflowScriptletRequest request 
     * @param   scriptlet.xml.Context context
     */
    public function handleErrorCondition($request, $context) {
      return FALSE;
    }
    
    /**
     * Perform cancellation of this handler.
     *
     * @param   scriptlet.xml.workflow.WorkflowScriptletRequest request 
     * @param   scriptlet.xml.Context context
     */
    public function handleCancellation($request, $context) { }

    /**
     * Handle submitted data
     *
     * @param   scriptlet.xml.workflow.WorkflowScriptletRequest request 
     * @param   scriptlet.xml.Context context
     */
    public function handleSubmittedData($request, $context) {
      return FALSE;
    }
    
    /**
     * Finalize this handler
     *
     * @param   scriptlet.xml.workflow.WorkflowScriptletRequest request 
     * @param   scriptlet.xml.XMLScriptletResponse response 
     * @param   scriptlet.xml.Context context
     */
    public function finalize($request, $response, $context) { }
  }
?>
