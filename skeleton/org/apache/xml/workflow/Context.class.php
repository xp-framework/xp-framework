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
    var
      $crm          = NULL,
      $sfm          = NULL,
      $cat          = NULL;
    
    /**
     * Called to initialize this application context
     *
     * @access  public
     * @param   &lang.ClassLoader classloader
     */
    function initialize(&$classloader) {
      $this->crm= &new ContextResourceManager();
      $this->crm->initialize($classloader);
      $this->sfm= &new StateFlowManager();
      $this->sfm->initialize($classloader);
    }
    
    /**
     * Get ContextResourceManager
     *
     * @access  public
     * @return  &org.apache.xml.workflow.ContextResourceManager
     */
    function &getContextResourceManager() {
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
    function &getContextResource($name, $class= 'ContextResource') {
      return $this->crm->getContextResource($name, $class);
    }
    
    /**
     * Handle a single request
     *
     * @access  public
     * @param   &org.apache.xml.XMLScriptletRequest request
     * @param   &org.apache.xml.XMLScriptletResponse response
     * @return  bool
     */
    function handleRequest(&$request, &$response) {
      $this->crm->setStorage($request->getSession());
      
      $l= &Logger::getInstance();
      $cat= &$l->getCategory($this->getClassName());
      
      // Load corresponding state
      $cat->info('Requested state name is', $request->getState(), $request->getPage());
      $state= &$this->sfm->getStateByName($request->getState());
      while ($state && !$state->isAccessible($this)) {
        $cat->debug($state->getName(), 'is not accessible, getting next...');
        $state= &$this->sfm->getNextState();
      }
      
      // No state found
      if (!$state) {
        return throw(new ContextFailedException(
          'State/workflow error', 
          new ElementNotFoundException($request->getState()))
        );
      }
      
      // Initialize state and set is as current state
      $state->initialize($this);
      $this->sfm->setCurrentState($state);
      
      $request->setState($state->getName());
      $cat->info('Current state is', $state->getClassName());

      // Now that we have the correct state:
      // - call all of its handlers and if this succeeds,
      // - call its getDocument method
      $return= TRUE;
      try(); {
        if ($st= $state->isSubmitTrigger($request)) {
          $cat->info('Have submit trigger', $st);
        }
          
        $handled= $has_error= FALSE;
        for ($i= 0, $s= sizeof($state->handlers); $i < $s; $i++) {
          $cat->info('Calling handler #'.$i, $state->handlers[$i]->getClassName());

          // If this handler is satisfied, ask the next handler in the queue
          if (!$state->handlers[$i]->needsData($this)) {
            $cat->warn('Handler does not need data, proceeding...');
            continue;
          }
          
          // No trigger
          if (!$st) continue;

          // If this handler is not active, ask the next handler in the queue
          if (!$state->handlers[$i]->isActive($this, $st)) {
            $cat->warn('Handler is not active for submit trigger', $st, ', proceeding...');
            continue;
          }

          // Handle submitted data
          if ($state->handlers[$i]->handleSubmittedData($this, $request)) {
            $handled= TRUE;
            $cat->info('handleSubmittedData returns TRUE, proceeding...');             
            continue;
          }

          // In case of an error, add all existing statuscodes to the output 
          // document's "formerrors" node. It is the responsibility of the XSL
          // to decide whether it wants to show errors or whether to hide them 
          // (whatever reason that might have)
          $cat->error('Errors occured', $state->handlers[$i]->errors);
          foreach ($state->handlers[$i]->errors as $statuscode) {
            $response->addFormError(
              $state->handlers[$i]->getClassName(),
              $statuscode
            );
          }

          // ...and break out of the loop immediately
          $has_error= TRUE;
          break;
        }
        
        // Go through all existing context resources and call their "insertStatus" method
        foreach (array_keys($this->crm->hash) as $name) {
          if (!isset($this->crm->crs[$this->crm->hash[$name]])) continue;
          
          $crs= &$this->crm->crs[$this->crm->hash[$name]];
          $cat->debug('Calling insertStatus() for', get_class($crs), $name);
          $crs->insertStatus($response->addFormResult(new Node(
            $name, 
            NULL, 
            array('contextresource' => $crs->getClassName())
          )));
        }

        $cat->info('Calling getDocument()');
        $state->getDocument($this, $request, $response);
        
      } if (catch('Exception', $e)) {
        $cat->error('Context::handleRequest', $e->getStackTrace());

        // Catch all and every single exception. This is bad coding but we
        // want to be robust plus this way, we do not use track of the state we were
        // in when the exception was thrown.
        return throw(new ContextFailedException('In state "'.$state->getName().'"', $e));
      }
      
      $cat->debug('Stateflow:', $this->sfm->offset, '@', $this->sfm->flow);
      $cat->mark();

      return $return;
    }
    
  }
?>
