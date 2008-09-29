<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'scriptlet.xml.workflow.Handler',
    'scriptlet.xml.workflow.IFormResultAggregate',
    'xml.Node',
    'util.log.Traceable'
  );

  /**
   * Represents a single state
   *
   * @see      xp://scriptlet.xml.workflow.AbstractXMLScriptlet
   * @purpose  Base class
   */
  class AbstractState extends Object implements Traceable {
    public
      $cat      = NULL,
      $handlers = array();
    
    /**
     * Add a handler
     *
     * @param   scriptlet.xml.workflow.Handler handler
     * @return  scriptlet.xml.workflow.Handler the added handler
     */
    public function addHandler($handler) {
      $this->handlers[]= $handler;
      return $handler;
    }
    
    /**
     * Retrieve whether handlers are existant for this state
     *
     * @return  bool
     */
    public function hasHandlers() {
      return !empty($this->handlers);
    }
    
    /**
     * Helper method used by setup(). Adds information about the handler (and 
     * about the handler's wrapper, if existant and IFormResultAggregate'd)
     * to the formresult
     *
     * @param   scriptlet.xml.workflow.Handler handler the handler to add
     * @param   xml.Node node the node to add the handler representation to
     * @param   scriptlet.xml.workflow.WorkflowScriptletRequest request 
     */
    protected function addHandlerToFormresult($handler, $node, $request) {
      $node->addChild(Node::fromArray($handler->values[HVAL_PERSISTENT], 'values'));
      foreach (array_keys($handler->values[HVAL_FORMPARAM]) as $key) {

        // Skip parameters which were set via setFormValue() and which were
        // posted via request to avoid duplicate parameters. We do not need
        // to use $response->addFormValue() because this is done in
        // XMLScriptlet::processRequest() called in XMLScriptlet::doGet().
        if (isset($request->params[$key])) continue;
        $request->params[$key]= $handler->values[HVAL_FORMPARAM][$key];
      }
      
      // Add wrapper parameter representation if the handler has a wrapper
      // and this wrapper implements the IFormResultAggregate interface
      if ($handler->hasWrapper() && $handler->wrapper instanceof IFormResultAggregate) {
        $wrapper= $node->addChild(new Node('wrapper'));
        foreach (array_keys($handler->wrapper->paraminfo) as $name) {
          $param= $wrapper->addChild(new Node('param', NULL, array(
            'name'       => $name,
            'type'       => $handler->wrapper->paraminfo[$name]['type'],
            'occurrence' => $handler->wrapper->paraminfo[$name]['occurrence'],
          )));
          if ($handler->wrapper->paraminfo[$name]['values']) {
            foreach ($handler->wrapper->paraminfo[$name]['values'] as $key => $value) {
              $param->addChild(new Node('value', $value, array('name' => $key)));
            }
          }
          if ($handler->wrapper->paraminfo[$name]['default']) {
            $param->addChild(new Node('default', $handler->wrapper->paraminfo[$name]['default']));
          }
        }
      }
    }
    
    /**
     * Set up this state
     *
     * @param   scriptlet.xml.workflow.WorkflowScriptletRequest request 
     * @param   scriptlet.xml.XMLScriptletResponse response 
     * @param   scriptlet.xml.workflow.Context context
     */
    public function setup($request, $response, $context) {
      $this->cat && $this->cat->debug($this->getClassName().'::setup');
      
      with ($h= $response->addFormResult(new Node('handlers'))); {
        for ($i= 0, $s= sizeof($this->handlers); $i < $s; $i++) {
          with ($name= $this->handlers[$i]->getName()); {
            $this->handlers[$i]->identifier= sprintf(
              'handler.%s.%s',
              $request->getStateName(),
              $this->handlers[$i]->identifierFor($request, $context)
            );
            $node= $h->addChild(new Node('handler', NULL, array(
              'id'   => $this->handlers[$i]->identifier,
              'name' => $name
            )));
            $this->cat && $this->cat->debug('Processing handler #', $i, $this->handlers[$i]);
            
            $setup= FALSE;
            if (!$request->session->hasValue($this->handlers[$i]->identifier)) {

              // If the handler is already active, this means the page was reloaded
              if ($this->handlers[$i]->isActive($request, $context)) {
                $this->handlers[$i]->finalize($request, $response, $context);
                $node->setAttribute('status', HANDLER_RELOADED);
                continue;
              }
              
              $setup= TRUE;
            }

            // Set up the handler if necessary
            if ($setup) {
              // In case a wrapper is defined, call its setup() method. This 
              // method is not allowed to fail.
              if ($this->handlers[$i]->hasWrapper()) {
                $this->handlers[$i]->wrapper->setup($request, $this->handlers[$i], $context);
              }

              $result= $this->handlers[$i]->setup($request, $context);

              // In case setup() returns FALSE, it indicates the form can not be 
              // displayed due to a prerequisite problem. For example, an editor
              // handler for an article might want to backcheck the article id
              // it is passed, and fail in case it doesn't exist (the article may
              // have been deleted by the backend or another concurrent request).
              if (!$result) {
                $node->setAttribute('status', HANDLER_FAILED);

                // Add handler errors to formresult.
                foreach ($this->handlers[$i]->errors as $error) {
                  $response->addFormError($name, $error[0], $error[1], $error[2]);
                }
                continue;
              }

              // Handler was successfully set up, register to session
              $request->session->putValue($this->handlers[$i]->identifier, $this->handlers[$i]->values);
              $node->setAttribute('status', HANDLER_SETUP);
              $this->addHandlerToFormresult($this->handlers[$i], $node, $request);
              continue;
            }

            // Load handler values from session
            $this->handlers[$i]->values= $request->session->getValue($this->handlers[$i]->identifier);
            $node->setAttribute('status', HANDLER_INITIALIZED);
            $this->addHandlerToFormresult($this->handlers[$i], $node, $request);

            // If the handler is not active, ask the next handler
            if (!$this->handlers[$i]->isActive($request, $context)) continue;
            
            // If active, ask handler if he wants to be cancelled
            if ($this->handlers[$i]->needsCancel($request, $context)) {
            
              // Perform user-defined cleanup
              $this->handlers[$i]->handleCancellation($request, $context);
              $node->setAttribute('status', HANDLER_CANCELLED);
              
              // Remove handler from session and call handler's finalize() method
              $request->session->removeValue($this->handlers[$i]->identifier);
              $this->handlers[$i]->finalize($request, $response, $context);
              
              continue;
            }

            // Check if the handler needs data. In case it does, call the
            // handleSubmittedData() method
            if (!$this->handlers[$i]->needsData($request, $context)) continue;
            
            // If the handler has a wrapper, tell it to load its values from the
            // request.
            if ($this->handlers[$i]->hasWrapper()) {
              $this->cat && $this->cat->debug($this->handlers[$i]->wrapper->getClassName().'::load');
              $this->handlers[$i]->wrapper->load($request, $this->handlers[$i]);
            }

            // Handle the submitted data. The method errorsOccured() may return
            // true in case a wrapper was set and its load() method produced 
            // errors. In this case, we won't even bother to continue processing
            // the data.
            if (!$this->handlers[$i]->errorsOccured()) {
              $this->cat && $this->cat->debug('Handling submitted data');
              $handled= $this->handlers[$i]->handleSubmittedData($request, $context);
            } else {
              $handled= $this->handlers[$i]->handleErrorCondition($request, $context);
            }

            // Check whether errors occured
            if ($this->handlers[$i]->errorsOccured()) {
              foreach ($this->handlers[$i]->errors as $error) {
                $response->addFormError($name, $error[0], $error[1], $error[2]);
              }
              $node->setAttribute('status', HANDLER_ERRORS);
              continue;
            }

            // In case handleSubmittedData() returns FALSE (but no errors occured),
            // the handler is simply telling us it's not finalized yet.
            if (!$handled) continue;

            // Submitted data was handled successfully, now remove the handler
            // from the session
            $request->session->removeValue($this->handlers[$i]->identifier);

            // Tell the handler to finalize itself. This may include adding a 
            // node to the formresult or sending a redirect to another page
            $this->handlers[$i]->finalize($request, $response, $context);
            $node->setAttribute('status', HANDLER_SUCCESS);
          }
        }
      }
    }
     
    /**
     * Process this state. Does nothing in this default implementation.
     *
     * @param   scriptlet.xml.workflow.WorkflowScriptletRequest request 
     * @param   scriptlet.xml.XMLScriptletResponse response 
     * @param   scriptlet.xml.Context context
     */
    public function process($request, $response, $context) {
    }
    
    /**
     * Retrieve whether authentication is needed. Returns FALSE in this 
     * default implementation.
     *
     * @return  bool
     */
    public function requiresAuthentication() {
      return FALSE;
    }

    /**
     * Set a trace for debugging
     *
     * @param   util.log.LogCategory cat
     * @see     xp://util.log.Traceable
     */
    public function setTrace($cat) { 
      $this->cat= $cat;
    }

  } 
?>
