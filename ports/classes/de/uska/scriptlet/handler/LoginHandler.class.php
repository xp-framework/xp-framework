<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'scriptlet.xml.workflow.Handler',
    'de.uska.scriptlet.wrapper.LoginWrapper',
    'de.uska.db.Player'
  );
  
  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class LoginHandler extends Handler {
    var
      $cat=     NULL;

    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function __construct() {
      parent::__construct();
      $this->setWrapper(new LoginWrapper());
      
      $log= &Logger::getInstance();
      $this->cat= &$log->getCategory();
    }
    
    /**
     * Handle submitted data
     *
     * @access  public
     * @param   &scriptlet.xml.workflow.WorkflowScriptletRequest request 
     * @param   &scriptlet.xml.Context context
     */
    function handleSubmittedData(&$request, &$context) {
      $cm= &ConnectionManager::getInstance();
      $wrapper= &$this->getWrapper();
      
      try(); {
        $player= &Player::getByUsername($wrapper->getUsername());
      } if (catch('SQLException', $e)) {
        return throw($e);
      }
      
      if (!is('de.uska.db.Player', $player)) {
        $this->addError('mismatch');
        return FALSE;
      }
      
      if (md5($wrapper->getPassword()) != $player->getPassword()) {
        $this->addError('mismatch');
        return FALSE;
      }
      
      $context->setUser($player);
      
      try(); {
        $db= &$cm->getByHost('uska', 0);
        $perms= $db->select('
            p.name
          from
            plane_right_matrix as prm,
            permission as p
          where p.permission_id= prm.permission_id
            and prm.player_id= %d',
          $player->getPlayer_id()
        );
      } if (catch('SQLException', $e)) {
        return throw($e);
      }
      
      $context->setPermissions($perms);
      
      return TRUE;
    }
    
    /**
     * Finalize this handler
     *
     * @access  public
     * @param   &scriptlet.xml.workflow.WorkflowScriptletRequest request 
     * @param   &scriptlet.xml.XMLScriptletResponse response 
     * @param   &scriptlet.xml.Context context
     */
    function finalize(&$request, &$response, &$context) {
      $return= $request->session->getValue('authreturn');

      if ($return) {
        $this->cat->debug('Redirect to', $return);
        // $request->session->removeValue('authreturn');
        $response->sendRedirect(sprintf('%s://%s%s%s',
          $return['scheme'],
          $return['host'],
          $return['path'],
          $return['query'] ? '?'.$return['query']: ''
        ));
      }
    }
  }
?>
