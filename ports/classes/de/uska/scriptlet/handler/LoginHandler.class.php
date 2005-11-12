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
   * Handler for login
   *
   * @purpose  Login
   */
  class LoginHandler extends Handler {
    var
      $cat=     NULL;

    /**
     * Constructor.
     *
     * @access  public
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
            plain_right_matrix as prm,
            permission as p
          where p.permission_id= prm.permission_id
            and prm.player_id= %d',
          $player->getPlayer_id()
        );
      } if (catch('SQLException', $e)) {
        return throw($e);
      }
      
      $cperms= array();
      foreach ($perms as $p) { $cperms[$p['name']]= TRUE; }
      $context->setPermissions($cperms);
      
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

      // Set a cookie
      $response->setCookie(new Cookie('uska-user', $context->user->getUsername()));

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
