<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'scriptlet.xml.workflow.Handler',
    'name.kiesel.pxl.scriptlet.wrapper.LoginWrapper'
  );

  /**
   * Login handler
   *
   * @purpose  Provide login
   */
  class LoginHandler extends Handler {

    /**
     * Constructor
     *
     * @access  public
     */
    function __construct() {
      parent::__construct();
      $this->setWrapper(new LoginWrapper());
    }
    
    /**
     * Handle submitted data.
     *
     * @access  public
     * @param   &scriptlet.xml.XMLScriptletRequest request
     * @param   &scriptlet.xml.workflow.Context context
     * @return  boolean
     */
    function handleSubmittedData(&$request, &$context) {
      $pm= &PropertyManager::getInstance();
      $prop= &$pm->getProperties('site');
      
      $user= $prop->readSection('user::'.$this->wrapper->getUsername());
      if (md5($this->wrapper->getPassword()) != $user['password'])
        return FALSE;
      
      $context->setUser($user);
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
