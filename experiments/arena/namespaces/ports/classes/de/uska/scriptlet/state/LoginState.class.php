<?php
/* This class is part of the XP framework
 *
 * $Id: LoginState.class.php 9677 2007-03-14 23:42:05Z kiesel $ 
 */

  namespace de::uska::scriptlet::state;

  ::uses(
    'de.uska.scriptlet.state.UskaState',
    'de.uska.scriptlet.handler.LoginHandler'
  );
  
  /**
   * Login state.
   *
   * @purpose  Provide login form
   */
  class LoginState extends UskaState {

    /**
     * Constructor.
     *
     */
    public function __construct() {
      $this->addHandler(new de::uska::scriptlet::handler::LoginHandler());
    }
    
    /**
     * Setup the state
     *
     * @param   &scriptlet.xml.workflow.WorkflowScriptletRequest request 
     * @param   &scriptlet.xml.XMLScriptletResponse response 
     * @param   &scriptlet.xml.Context context
     */
    public function setup($request, $response, $context) {
      parent::setup($request, $response, $context);
      
      if ($request->hasParam('logout')) {
        $context->setUser($n= NULL);
        $response->setCookie(new ('uska-user', '', time() - 1000));
        $response->setCookie(new ('uska.loginname', '', time() - 1000));
      
        $uri= $request->getURI();
        $response->sendRedirect(sprintf('%s://%s/xml/uska.de_DE/login',
          $uri['scheme'],
          $uri['host']
        ));
        return FALSE;
      }
    }
  }
?>
