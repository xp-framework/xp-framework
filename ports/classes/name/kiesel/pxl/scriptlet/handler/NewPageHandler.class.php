<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'scriptlet.xml.workflow.Handler',
    'name.kiesel.pxl.scriptlet.wrapper.NewPageWrapper'
  );

  /**
   * Handler. <Add description>
   *
   * @purpose  <Add purpose>
   */
  class NewPageHandler extends Handler {

    /**
     * Constructor
     *
     * @access  public
     */
    function __construct() {
      parent::__construct();
      $this->setWrapper(new NewPageWrapper());
    }
    
    /**
     * Retrieve identifier.
     *
     * @access  public
     * @param   &scriptlet.xml.XMLScriptletRequest request
     * @param   &scriptlet.xml.workflow.Context context
     * @return  string
     */
    function identifierFor(&$request, &$context) {
    
      // TODO: Implement this method, if a somehow unique identifier is required for this
      //       handler. If not, remove the method.
      
      return $this->name;
    }
    
    /**
     * Setup handler.
     *
     * @access  public
     * @param   &scriptlet.xml.XMLScriptletRequest request
     * @param   &scriptlet.xml.workflow.Context context
     * @return  boolean
     */
    function setup(&$request, &$context) {
    
      // TODO: Add code that is required to initially setup the handler
      //       Set values with Handler::setFormValue() to make them accessible in the frontend.
      
      return TRUE;
    }
    
    /**
     * Handle submitted data.
     *
     * @access  public
     * @param   &scriptlet.xml.XMLScriptletRequest request
     * @param   &scriptlet.xml.workflow.Context context
     * @return  boolean
     */
    function handleSubmittedData(&$request, &$context) {
      
      // TODO: Add code that handles the submitted values. The values have already
      //       passed the Wrappers precheck/caster/postcheck routines.
      
      return TRUE;
    }
    
    /**
     * Finalize this handler
     *
     * @access  public
     * @param   &scriptlet.xml.workflow.WorkflowScriptletRequest request 
     * @param   &scriptlet.xml.XMLScriptletResponse response 
     * @param   &scriptlet.xml.Context context
     */
    function finalize(&$request, &$response, &$context) {

      // TODO: Add code that is executed after success and on every reload of the handler.
      //       Many handlers don't need this, so remove the complete function.
    }
  }
?>