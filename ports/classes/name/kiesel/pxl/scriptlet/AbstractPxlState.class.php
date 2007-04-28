<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'scriptlet.xml.workflow.AbstractState',
    'util.PropertyManager'
  );

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class AbstractPxlState extends AbstractState {
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    protected function webName($string) {
      return preg_replace('/[^a-zA-Z0-9_\-]/', '_', $string);
    }    
    
    /**
     * Setup this state. Redirects to login form in case the state 
     * needs an authenticated user.
     *
     * @param   &scriptlet.xml.workflow.WorkflowScriptletRequest request 
     * @param   &scriptlet.xml.XMLScriptletResponse response 
     * @param   &scriptlet.xml.Context context
     */
    public function setup($request, $response, $context) {
    
      // Automatically handle authentication if state indicates so
      if ($this->requiresAuthentication()) {
        if (!$context->user) {

          // Store return point in session
          $uri= $request->getURI();
          $request->session->putValue('authreturn', $uri);

          // Send redirect
          $response->sendRedirect(sprintf(
            '%s://%s/xml/%s.%s%s/%s%s%s',
            $uri['scheme'],
            $uri['host'],
            $request->getProduct(),
            $request->getLanguage(),
            '.psessionid='.$request->getSessionId(),
            'login',                                            // Authenticate state
            empty($uri['query']) ? '' : '?'.$uri['query'],
            empty($uri['fraction']) ? '' : '#'.$uri['fraction']        
          ));
          
          return FALSE;
        }
      }
      parent::setup($request, $response, $context);
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    function process($request, $response, $context) {
      $prop= PropertyManager::getInstance()->getProperties('site');
      $response->addFormResult(Node::fromArray($prop->readSection('site'), 'config'));
      
      return parent::process($request, $response, $context);    
    }    
    
  }
?>
