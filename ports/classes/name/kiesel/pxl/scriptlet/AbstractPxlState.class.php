<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'scriptlet.xml.workflow.AbstractState',
    'util.PropertyManager',
    'name.kiesel.pxl.Catalogue',
    'name.kiesel.pxl.Page',
    'name.kiesel.pxl.Picture',
    'name.kiesel.pxl.storage.FilesystemContainer'
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
    protected function _getDataPath($path) {
      $path= $_SERVER['DOCUMENT_ROOT'].'/pages/'.$path;
      return realpath($path);
    }    
  
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    protected function _getCatalogue() {
      if (NULL === $this->catalogue) {
        $this->catalogue= Catalogue::create(new FilesystemContainer($this->_getDataPath()));
      }
      
      return $this->catalogue;
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    protected function _getPage($path) {
      $page= Page::create(new FilesystemContainer($this->_getDataPath($path)));
      return $page;
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
    
  }
?>
