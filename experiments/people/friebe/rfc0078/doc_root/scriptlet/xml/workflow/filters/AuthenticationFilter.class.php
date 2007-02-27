<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('scriptlet.xml.workflow.filters.XMLScriptletFilter');

  /**
   * (Insert class' description here)
   *
   * @purpose  Filter implementation
   */
  class AuthenticationFilter extends Object implements XMLScriptletFilter {

    /**
     * Filters request and/or response
     *
     * @param   scriptlet.xml.XMLScriptletRequest request 
     * @param   scriptlet.xml.XMLScriptletResponse response 
     */
    public function filter($request, $response) {
      if (!$request->hasSession()) return FALSE;
      if (!($user= $request->session->getValue('user'))) return FALSE;
      return TRUE;
    }
  }
?>
