<?php
/* This class is part of the XP framework
 *
 * $Id: AttendEventState.class.php 4972 2005-04-11 00:08:36Z kiesel $ 
 */

  uses('de.uska.scriptlet.state.UskaState', 'de.uska.scriptlet.handler.AccountEventHandler');

  /**
   * Account points for event
   *
   * @purpose  Accounts points
   */
  class AccountEventState extends UskaState {
    
    /**
     * Indicate this state requires authentication.
     *
     * @access  public
     * @return  bool
     */
    public function requiresAuthentication() { return TRUE; }

    /**
     * Setup the state
     *
     * @access  public
     * @param   &scriptlet.xml.workflow.WorkflowScriptletRequest request 
     * @param   &scriptlet.xml.XMLScriptletResponse response 
     * @param   &scriptlet.xml.Context context
     */
    public function setup(&$request, &$response, &$context) {
      $this->addHandler(new AccountEventHandler());
      parent::setup($request, $response, $context);
    }
  }
?>
