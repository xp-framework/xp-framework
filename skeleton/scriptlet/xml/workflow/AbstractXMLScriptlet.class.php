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
      $request->state->setup($request, $response);
      $request->state->process($request, $response);
      return parent::doGet($request, $response);
    }
  }
?>
