<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'de.uska.scriptlet.state.UskaState',
    'de.uska.scriptlet.handler.EditEventHandler'
  );

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class EditEventState extends UskaState {

    /**
     * Retrieve whether authentication is needed.
     *
     * @access  public
     * @return  bool
     */
    public function requiresAuthentication() {
      return TRUE;
    }
    
    /**
     * Setup the state
     *
     * @access  public
     * @param   &scriptlet.xml.workflow.WorkflowScriptletRequest request 
     * @param   &scriptlet.xml.XMLScriptletResponse response 
     * @param   &scriptlet.xml.Context context
     */
    public function setup(&$request, &$response, &$context) {
      $this->addHandler(new EditEventHandler());
      parent::setup($request, $response, $context);
    }
  }
?>
