<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'org.apache.xml.workflow.ContextResourceManager',
    'org.apache.xml.workflow.StateFlowManager',
    'org.apache.xml.workflow.ContextFailedException',
    'org.apache.xml.workflow.State',
    'org.apache.xml.workflow.User',
    'lang.ElementNotFoundException',
    'util.log.Logger',
    'util.log.FileAppender'
  );

  /**
   * Context
   *
   * @see      xp://org.apache.xml.workflow.AbstractXMLScriptlet
   * @purpose  Part of the workflow model
   */
  class Context extends Object {
    public
      $crm          = NULL,
      $sfm          = NULL,
      $cat          = NULL,
      $user         = NULL,
      $classloader  = NULL;
    
    /**
     * Called to initialize this application context
     *
     * @access  public
     * @param   &lang.ClassLoader classloader
     * @param   &org.apache.HttpScriptletRequest request 
     */
    public function initialize(&$classloader, &$request) {
      $this->crm= new ContextResourceManager();
      $this->crm->initialize($classloader);
      $this->sfm= new StateFlowManager();
      $this->sfm->initialize();
      $this->classloader= $classloader;
      
      self::setUser(new User(
        $request->getEnvValue('REMOTE_ADDR'),
        $request->getEnvValue('HTTP_USER_AGENT'),
        $request->getLanguage()
      ));
    }

    /**
     * Set User
     *
     * @access  public
     * @param   &org.apache.xml.workflow.User user
     */
    public function setUser(&$user) {
      $this->user= $user;
    }

    /**
     * Get User
     *
     * @access  public
     * @return  &org.apache.xml.workflow.User
     */
    public function getUser() {
      return $this->user;
    }
    
    /**
     * Get ContextResourceManager
     *
     * @access  public
     * @return  &org.apache.xml.workflow.ContextResourceManager
     */
    public function getContextResourceManager() {
      return $this->crm;
    }

    /**
     * Convenience function to get ContextResource
     *
     * @access  public
     * @param   string name
     * @return  &org.apache.xml.workflow.ContextResource
     * @see     xp://org.apache.xml.workflow.ContextResourceManager#getContextResource
     */
    public function getContextResource($name, $class= 'ContextResource') {
      $cr= $this->crm->getContextResource($name, $class);

      $l= Logger::getInstance();
      $cat= $l->getCategory(self::getClassName());
      $cat->debug('getContextResource', $name, $class, '>>>', $cr->getClassName());

      return $cr;
    }
    
    /**
     * Returns corresponding state. Loads the state class if necessary
     *
     * @access  private
     * @param   string name
     * @return  &org.apache.xml.workflow.State
     */
    private function getStateByName($name) {
      $l= Logger::getInstance();
      $cat= $l->getCategory(self::getClassName());
      $cat->debug('getStateByName', $name);
      
      // Check to see if we know this state
      $class= ucfirst($name).'State';
      if (!class_exists($class)) {
        $cat->debug($class, 'not existant, loading');

        try {
          $class= $this->classloader->loadClass($class);
        } catch (ClassNotFoundException $e) {
          throw (new HttpScriptletException($e->message));
        }
      }
      
      // Initialize the state
      $state= call_user_func(array($class, 'getInstance'), $name);
      $state->initialize($this);
      
      return $state;
    }
    
    /**
     * Handle a single request
     *
     * @access  public
     * @param   &org.apache.xml.XMLScriptletRequest request
     * @param   &org.apache.xml.XMLScriptletResponse response
     * @return  bool
     */
    public function handleRequest(&$request, &$response) {
      $this->crm->setStorage($request->getSession());
      
      $l= Logger::getInstance();
      $cat= $l->getCategory(self::getClassName());
      
      $stateName= $request->getState();
      $cat->debug('In '.self::getClassName().'.handleRequest(request, response) for', $request->uri, 'state', $stateName);
      do {
      
        if (!($state= self::getStateByName($stateName))) {
          $cat->error(
            'State', $stateName, 'not found',
            '[headers', $request->headers, ']'
          );
          
          // The state could not be found. Essentially, this is a 404
          throw (new ContextFailedException(
            'State/workflow error', 
            new ElementNotFoundException($request->getState()))
          );
        }
        
        if (!$state->isAccessible($this, $request)) {
          $cat->debug($state->getName(), 'is not accessible, getting next...');
          
          // If a state is not accessible, we will check to see if
          // there is a worfklow. If there is such a workflow, then
          // we will try to get it's next state.
          if (!($flow= $this->sfm->getCurrentFlow())) {
            $cat->error(
              'State', $stateName, 'not accessible',
              '[headers', $request->headers, ']'
            );
            throw (new ContextFailedException(
              'No workflow and state not accessible', 
              new IllegalAccessException($request->getState()))
            );
          }
          $cat->debug('Have flow', $flow);
          
          // If we are at the end of the workflow and this state is not 
          // accessible, jump to the first state in this workflow. If the
          // first state in this workflow is the same as the current one,
          // we have a serious problem and must break out of this loop,
          // thus preventing recursive calls.
          if ($stateName == ($next= $flow->getNextState())) {
            $cat->warn('Final page of workflow not accessible, jumping to first');
            if ($stateName == ($first= $flow->getFirstState())) {
              $cat->error(
                $stateName, 'is only state in flow, stopping to prevent endless loop', 
                '[headers', $request->headers, ']'
              );
              throw (new ContextFailedException(
                'Workflow contains only one element', 
                new IllegalAccessException($request->getState()))
              );
            }
            
            // Ask first state
            $cat->debug($stateName, 'Asking first state in workflow', $first);
            $stateName= $first;
            continue;
          }
          
          // Ask next state.
          $cat->debug($stateName, 'Asking next state in workflow', $next);
          $stateName= $next;
          continue;
        }

        // Initialize state and set is as current state
        $request->setState($state->getName());
        $cat->info('Current state is', $state);

        // Call getDocument() method
        $cat->info('Calling getDocument()', $state->getClassName());
        $state->getDocument($this, $request, $response);
        
        break;
      } while(1);
        
      // Finally, we're done
      $cat->debug('Stateflow:', $this->sfm->flows);
      $cat->mark();
      return TRUE;
    }
    
  }
?>
