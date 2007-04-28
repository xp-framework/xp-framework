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
     */
    public function __construct() {
      parent::__construct();
      $this->setWrapper(new LoginWrapper());
    }
    
    /**
     * Handle submitted data.
     *
     * @param   &scriptlet.xml.XMLScriptletRequest request
     * @param   &scriptlet.xml.workflow.Context context
     * @return  boolean
     */
    public function handleSubmittedData(&$request, &$context) {
      $prop= PropertyManager::getInstance()->getProperties('site');
      $db= ConnectionManager::getInstance()->getByHost('pxl', 0);

      $user= $db->select('
          author_id,
          username,
          realname,
          email,
          password
        from
          author
        where username= %s
        ',
        $this->wrapper->getUsername()
      );
      
      if (!sizeof($user)) {
        $this->addError('unknown', 'username');
        $this->addError('unknown', 'password');
        return FALSE;
      }
      
      $user= array_shift($user);
      if (md5($this->wrapper->getPassword()) != $user['password']) {
        $this->addError('unknown', 'username');
        $this->addError('unknown', 'password');
        return FALSE;
      }

      $context->setUser($user);
      return TRUE;
    }
    
    /**
     * Finalize this handler
     *
     * @param   &scriptlet.xml.workflow.WorkflowScriptletRequest request 
     * @param   &scriptlet.xml.XMLScriptletResponse response 
     * @param   &scriptlet.xml.Context context
     */
    public function finalize($request, $response, $context) {
      $return= $request->session->getValue('authreturn');

      if ($return) {
        $request->session->removeValue('authreturn');
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
