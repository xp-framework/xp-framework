<?php
/* This class is part of the XP framework
 *
 * $Id: EditPlayerState.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace de::uska::scriptlet::state;

  ::uses(
    'de.uska.scriptlet.state.UskaState',
    'de.uska.scriptlet.handler.EditPlayerHandler'
  );

  /**
   * Edit or create players.
   *
   * @purpose  Edit players
   */
  class EditPlayerState extends UskaState {

    /**
     * Retrieve whether authentication is needed.
     *
     * @return  bool
     */
    public function requiresAuthentication() {
      return TRUE;
    }
    
    /**
     * Setup the state
     *
     * @param   &scriptlet.xml.workflow.WorkflowScriptletRequest request 
     * @param   &scriptlet.xml.XMLScriptletResponse response 
     * @param   &scriptlet.xml.Context context
     */
    public function setup($request, $response, $context) {
      $this->addHandler(new de::uska::scriptlet::handler::EditPlayerHandler());
      parent::setup($request, $response, $context);
    }
  }
?>
