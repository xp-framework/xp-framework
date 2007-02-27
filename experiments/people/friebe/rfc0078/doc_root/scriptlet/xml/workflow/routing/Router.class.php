<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('scriptlet.xml.workflow.routing.Route');

  /**
   * A router is a mechanism to route requests to states.
   *
   * @see      xp://scriptlet.xml.workflow.WorkflowXMLScriptlet#routerFor
   * @purpose  Interface
   */
  interface Router {
  
    /**
     * Route a request
     *
     * @param   string package
     * @param   scriptlet.xml.XMLScriptletRequest
     * @param   scriptlet.xml.XMLScriptletResponse
     * @param   scriptlet.xml.workflow.Context
     * @return  scriptlet.xml.workflow.routing.Route
     */
    public function route($package, $request, $response, $context);
  }
?>
