<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  define('HANDLER_SETUP',       'setup');
  define('HANDLER_FAILED',      'failed');
  define('HANDLER_INITIALIZED', 'initialized');
  define('HANDLER_ERRORS',      'errors');
  define('HANDLER_SUCCESS',     'success');
  define('HANDLER_RELOADED',    'reloaded');

  /**
   * Represents a single state
   *
   * @see      xp://scriptlet.xml.workflow.AbstractXMLScriptlet
   * @purpose  Base class
   */
  class AbstractState extends Object {
    var
      $handlers= array();
    
    /**
     * Add a handler
     *
     * @access  public
     * @param   &scriptlet.xml.workflow.Handler handler
     * @return  &scriptlet.xml.workflow.Handler the added handler
     */
    function &addHandler(&$handler) {
      $this->handlers[]= &$handler;
      return $handler;
    }
    
    /**
     * Retrieve whether handlers are existant for this state
     *
     * @access  public
     * @return  bool
     */
    function hasHandlers() {
      return !empty($this->handlers);
    }
         
    /**
     * Set up this state
     *
     * @access  public
     * @param   &scriptlet.xml.workflow.WorkflowScriptletRequest request 
     * @param   &scriptlet.xml.XMLScriptletResponse response 
     */
    function setup(&$request, &$response) {
      with ($h= &$response->addFormResult(new Node('handlers'))); {
        for ($i= 0, $s= sizeof($this->handlers); $i < $s; $i++) {
          with ($name= $this->handlers[$i]->getName()); {
            $identifier= 'handler.'.$request->getStateName().'.'.$name;
            $handler= &$h->addChild(new Node('handler', NULL, array('name' => $name)));

            // Set up handler if not in session
            if (!$request->session->hasValue($identifier)) {

              // If the handler is already active, this means the page was reloaded
              if ($this->handlers[$i]->isActive($request)) {
                $handler->setAttribute('status', HANDLER_RELOADED);
                continue;
              }

              // Otherwise, we may set up the handler
              try(); {
                $setup= $this->handlers[$i]->setup($request);
              } if (catch('Exception', $e)) {
                return throw($e);
              }
              
              // In case setup() returns FALSE, it indicates the form can not be 
              // displayed due to a prerequisite problem. For example, an editor
              // handler for an article might want to backcheck the article id
              // it is passed, and fail in case it doesn't exist (the article may
              // have been deleted by the backend or another concurrent request).
              if (!$setup) {
                $handler->setAttribute('status', HANDLER_FAILED);
                continue;
              }

              // Handler was successfully set up, register to session
              $handler->setAttribute('status', HANDLER_SETUP);
              $request->session->putValue($identifier, $this->handlers[$i]->values);
              $handler->addChild(Node::fromArray($this->handlers[$i]->values, 'values'));
              continue;
            }

            // Load handler values from session
            $this->handlers[$i]->values= $request->session->getValue($identifier);
            $handler->setAttribute('status', HANDLER_INITIALIZED);
            $handler->addChild(Node::fromArray($this->handlers[$i]->values, 'values'));

            // If the handler is not active, ask the next handler
            if (!$this->handlers[$i]->isActive($request)) continue;

            // Check if the handler needs data. In case it does, call the
            // handleSubmittedData() method
            if (!$this->handlers[$i]->needsData($request)) continue;

            // Handle the submitted data
            $handled= $this->handlers[$i]->handleSubmittedData($request);

            // Check whether errors occured
            if ($this->handlers[$i]->errorsOccured()) {
              foreach ($this->handlers[$i]->errors as $error) {
                $response->addFormError($name, $error[0], $error[1], $error[2]);
              }
              $handler->setAttribute('status', HANDLER_ERRORS);
              continue;
            }

            // In case handleSubmittedData() returns FALSE (but no errors occured),
            // the handler is simply telling us it's not finalized yet.
            if (!$handled) continue;

            // Submitted data was handled successfully, now remove the handler
            // from the session
            $request->session->removeValue($identifier);

            // Tell the handler to finalize itself. This may include adding a 
            // node to the formresult or sending a redirect to another page
            $this->handlers[$i]->finalize($request, $response);
            $handler->setAttribute('status', HANDLER_SUCCESS);
          }
        }
      }
    }
     
    /**
     * Process this state
     *
     * @access  public
     * @param   &scriptlet.xml.workflow.WorkflowScriptletRequest request 
     * @param   &scriptlet.xml.XMLScriptletResponse response 
     */
    function process(&$request, &$response) {
    }
    
    /**
     * Retrieve whether authentication is needed
     *
     * @access  public
     * @return  bool
     */
    function requiresAuthentication() {
      return FALSE;
    }
  }
?>
