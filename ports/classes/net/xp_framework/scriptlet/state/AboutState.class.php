<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'scriptlet.xml.workflow.AbstractState'
  );

  /**
   * Handles /xml/about
   *
   * @purpose  State
   */
  class AboutState extends AbstractState {

    /**
     * Process this state.
     *
     * @access  public
     * @param   &scriptlet.xml.workflow.WorkflowScriptletRequest request
     * @param   &scriptlet.xml.XMLScriptletResponse response
     */
    public function process(&$request, &$response) {
    }
  }
?>
