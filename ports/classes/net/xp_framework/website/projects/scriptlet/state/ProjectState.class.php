<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('scriptlet.xml.workflow.AbstractState');

  /**
   * Handles /xml/project
   *
   * @purpose  State
   */
  class ProjectState extends AbstractState {

    /**
     * Process this state.
     *
     * @param   scriptlet.xml.workflow.WorkflowScriptletRequest request
     * @param   scriptlet.xml.XMLScriptletResponse response
     */
    public function process($request, $response) {
      sscanf($request->getQueryString(), '%[a-zA-Z_]/%[a-zA-Z_]', $category, $project);

      // TBI
    }
  }
?>
