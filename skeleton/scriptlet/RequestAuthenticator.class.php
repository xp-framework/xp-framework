<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * This interface describes objects that take care of request
   * authentication.
   *
   * @see      xp://scriptlet.HttpScriptlet#getAuthenticator
   * @purpose  Authentication for scriptlets
   */
  interface RequestAuthenticator {
  
    /**
     * Authenticate a request
     *
     * @param   scriptlet.HttpScriptletRequest request
     * @param   scriptlet.HttpScriptletResponse response
     * @param   scriptlet.xml.workflow.Context context
     * @return  bool
     */
    public function authenticate($request, $response, $context);
  }
?>
