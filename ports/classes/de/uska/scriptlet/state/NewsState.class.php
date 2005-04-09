<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('de.uska.scriptlet.state.UskaState');

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class NewsState extends UskaState {
  
    /**
     * Process this state.
     *
     * @access  public
     * @param   &scriptlet.xml.workflow.WorkflowScriptletRequest request 
     * @param   &scriptlet.xml.XMLScriptletResponse response 
     * @param   &scriptlet.xml.Context context
     */
    function process(&$request, &$response, &$context) {
      parent::process($request, $response, $context);
      $this->insertTeams($request, $response);
    }
  }
?>
