<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Route
   *
   * @see      xp://scriptlet.xml.workflow.routing.Router
   * @purpose  Interface
   */
  interface Route {
    
    /**
     * Dispatch this route
     *
     * @param   scriptlet.xml.XMLScriptletRequest
     * @param   scriptlet.xml.XMLScriptletResponse
     * @param   scriptlet.xml.workflow.Context
     * @return  mixed
     */
    public function dispatch($request, $response, $context);
  }
?>
