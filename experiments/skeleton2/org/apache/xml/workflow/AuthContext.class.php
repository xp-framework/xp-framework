<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('org.apache.xml.workflow.Context');

  /**
   * AuthContext
   *
   * @see      xp://org.apache.xml.workflow.Context
   * @purpose  Part of the workflow model for authenticated states
   */
  class AuthContext extends Context {
    
    /**
     * Handle a single request
     *
     * @access  public
     * @param   &org.apache.xml.XMLScriptletRequest request
     * @param   &org.apache.xml.XMLScriptletResponse response
     * @return  bool
     */
    public function handleRequest(&$request, &$response) {
      if (!$this->user->isLoggedOn()) {
        $request->setState('login');
      }
      return parent::handleRequest($request, $response);
    }
    
  }
?>
