<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'org.apache.xml.workflow.ContextResourceManager',
    'org.apache.xml.workflow.StateFlowManager',
    'org.apache.xml.workflow.State'
  );

  /**
   * (Insert class' description here)
   *
   * @ext      extensiom
   * @see      reference
   * @purpose  purpose
   */
  class Context extends Object {
    var
      $crm          = NULL,
      $sfm          = NULL;
      
    /**
     * Called to initialize this application context
     *
     * @access  public
     * @param   &lang.ClassLoader classloader
     */
    function initialize(&$classloader) {
      $this->crm= &new ContextResourceManager();
      $this->crm->initialize();
      $this->sfm= &new StateFlowManager();
      $this->sfm->initialize($classloader);
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function &getContextResourceManager() {
      return $this->crm;
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
    
      // FIXME for debugging only - remove me! (and all calls to $cat->)
      $l= &Logger::getInstance();
      $cat= &$l->getCategory($this->getClassName());
      
      // Load corresponding state
      $state= &$this->sfm->getStateByName($request->getState());
      while ($state && !$state->isAccessible($this)) {
        $state= &$this->sfm->getNextState();
      }
      
      // No state found
      if (!$state) {
        return throw(new HttpScriptletException('No state found'));
      }
      
      $this->sfm->setCurrentState($state);
      $cat->info($this->sfm, $state);
      
      // Now that we have the correct state:
      // - call all of its handlers and if this succeeds,
      // - call its getDocument method
      try(); {
        $has_error= FALSE;
        if ($state->isSubmitTrigger($request)) {
          for ($i= 0, $s= sizeof($state->handlers); $i < $s; $i++) {
            $cat->info('Calling handler #'.$i, $state->handlers[$i]);
            
            // If this handler is not active, ask the next
            if (!$state->handlers[$i]->isActive($this)) {
              $cat->warn('Handler is not active, proceeding...');
              continue;
            }

            // If this handler is satisfied, ask the next
            if (!$state->handlers[$i]->needsData($this)) {
              $cat->warn('Handler does not need data, proceeding...');
              continue;
            }  

            // Handle submitted data
            if ($state->handlers[$i]->handleSubmittedData($this, $request)) {
              $cat->info('handleSubmittedData returns TRUE, proceeding...');
              continue;
            }

            $cat->error('Errors occured', $state->handlers[$i]->errors);
            // In case of an error, add all errors to formerrors ...
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
        }
        
        if (!$has_error) {
          $cat->info('Calling getDocument()');
          $state->getDocument($this, $request, $response);
        }
      } if (catch('Exception', $e)) {
        return throw(new HttpScriptletException($e->message));
      }
      
      return TRUE;
    }
    
  }
?>
