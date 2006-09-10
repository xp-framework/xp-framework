<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'scriptlet.xml.workflow.Handler',
    'name.kiesel.pxl.scriptlet.wrapper.NewPageWrapper',
    'name.kiesel.pxl.util.PageCreator'
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
    
      $s= &new FilesystemContainer($request->getEnvValue('DOCUMENT_ROOT').'/pages');
      $filedata= &$this->wrapper->getFile();
      $file= &$filedata->getFile();
      
      $pc= &new PageCreator(
        $s,
        $this->wrapper->getName(),
        array($file->getURI())
      );
      $pc->setAuthor($context->user['username']);
      $pc->setDescription($this->wrapper->getDescription());
      $pc->addPage();
      
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
