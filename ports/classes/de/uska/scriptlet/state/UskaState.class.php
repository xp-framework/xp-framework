<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'scriptlet.xml.workflow.AbstractState',
    'de.uska.db.Player'
  );

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class UskaState extends AbstractState {
    var
      $cat=     NULL,
      $db=      NULL;
      
    /**
     * Constructor.
     *
     * @access  public
     */
    function __construct() {
      $log= &Logger::getInstance();
      $this->cat= &$log->getCategory();
    }
    
    /**
     * Process this state. Just sets up database connection
     *
     * @access  public
     * @param   &scriptlet.xml.workflow.WorkflowScriptletRequest request 
     * @param   &scriptlet.xml.XMLScriptletResponse response 
     * @param   &scriptlet.xml.Context context
     * @return  boolean
     */
    function process(&$request, &$response, &$context) {
    
      // Automatically handle authentication if state indicates so
      if ($this->requiresAuthentication()) {
        $this->cat->debug('requireauth?', $context);
        if (!is('de.uska.db.Player', $context->user)) {

          // Store return point in session
          $uri= &$request->getURI();
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
    
      $cm= &ConnectionManager::getInstance();
      $this->db= &$cm->getByHost($request->getProduct(), 0);
    }
  
    /**
     * Insert all teams into the result tree.
     *
     * @access  public
     * @param   &scriptlet.xml.workflow.WorkflowScriptletRequest request 
     * @param   &scriptlet.xml.XMLScriptletResponse response 
     */
    function insertTeams(&$request, &$response) {
      $pm= &PropertyManager::getInstance();
      $prop= &$pm->getProperties('product');
      
      try(); {
        $teams= $this->db->select('
            team_id,
            name
          from
            team
          where team_id in (%d)',
          $prop->readArray($request->getProduct(), 'teams')
        );
      } if (catch('SQLException', $e)) {
        return throw($e);
      }
      
      $response->addFormResult(Node::fromArray($teams, 'teams'));
    }
  }
?>
