<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'scriptlet.xml.XMLScriptlet', 
    'scriptlet.xml.workflow.WorkflowScriptletRequest'
  );

  /**
   * Workflow model scriptlet implementation
   *
   * @purpose  Base class
   */
  class AbstractXMLScriptlet extends XMLScriptlet {
    var
      $classloader  = NULL;

    /**
     * Constructor
     *
     * @access  public
     * @param   &lang.ClassLoader classloader
     * @param   string base default ''
     */
    function __construct(&$classloader, $base= '') {
      parent::__construct($base);
      $this->classloader= &$classloader;
    }

    /**
     * Create the request object
     *
     * @access  protected
     * @return  &scriptlet.xml.workflow.WorkflowScriptletRequest
     */
    function &_request() {
      return new WorkflowScriptletRequest($this->classloader);
    }

    /**
     * Decide whether a session is needed
     *
     * @access  protected
     * @param   &scriptlet.xml.workflow.WorkflowScriptletRequest request
     * @return  bool
     */
    function needsSession(&$request) {
      return ($request->state && (
        $request->state->hasHandlers() || 
        $request->state->requiresAuthentication()
      ));
    }
    
    /**
     * Process workflow. Calls the state's setup() and process() 
     * methods in this order. May be overwritten by subclasses.
     *
     * Return FALSE from this method to indicate no further 
     * processing is to be done
     *
     * @access  protected
     * @param   &scriptlet.xml.workflow.WorkflowScriptletRequest request 
     * @param   &scriptlet.xml.XMLScriptletResponse response 
     * @return  bool
     */
    function processWorkflow(&$request, &$response) {
      $request->state->setup($request, $response);
      return $request->state->process($request, $response);
    }

    /**
     * Receives an HTTP GET request from the <pre>process()</pre> method
     * and handles it.
     *
     * @access  protected
     * @return  bool processed
     * @param   &scriptlet.xml.workflow.WorkflowScriptletRequest request 
     * @param   &scriptlet.xml.XMLScriptletResponse response 
     * @throws  lang.Exception to indicate failure
     */
    function doGet(&$request, &$response) {
      if (FALSE === $this->processWorkflow($request, $response)) {
      
        // The processWorkflow() method indicates no further processing
        // is to be done. Pass result "up".
        return FALSE;
      }
      return parent::doGet($request, $response);
    }
  }
?>
