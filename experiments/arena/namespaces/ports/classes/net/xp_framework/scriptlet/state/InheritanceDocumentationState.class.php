<?php
/* This class is part of the XP framework
 *
 * $Id: DocumentationState.class.php 5901 2005-10-04 20:39:40Z friebe $ 
 */

  namespace net::xp_framework::scriptlet::state;

  ::uses(
    'scriptlet.xml.workflow.AbstractState',
    'net.xp_framework.db.caffeine.XPNews'
  );

  /**
   * Handles /xml/documentation
   *
   * @purpose  State
   */
  class InheritanceDocumentationState extends scriptlet::xml::workflow::AbstractState {

    /**
     * Process this state.
     *
     * @param   &scriptlet.xml.workflow.WorkflowScriptletRequest request
     * @param   &scriptlet.xml.XMLScriptletResponse response
     */
    public function process($request, $response) {
    }
  }
?>
