<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  define('HANDLER_SETUP',       'setup');
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
     * @param   
     * @return  
     */
    function addHandler(&$handler) {
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
      $status= array();
      for ($i= 0, $s= sizeof($this->handlers); $i < $s; $i++) {
        with ($name= $this->handlers[$i]->getName()); {
          $identifier= 'handler.'.$request->getStateName().'.'.$name;
          
          // Set up handler if not in session
          if (!$request->session->hasValue($identifier)) {

            // If the handler is already active, this means the page was reloaded
            if ($this->handlers[$i]->isActive($request)) {
              $status[$name]= HANDLER_RELOADED;
              continue;
            }

            // Otherwise, we may set up the handler
            $status[$name]= HANDLER_SETUP;
            $this->handlers[$i]->setup($request);
            $request->session->putValue($identifier, $this->handlers[$i]);
            continue;
          }

          $status[$name]= HANDLER_INITIALIZED;
          
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
            $status[$name]= HANDLER_ERRORS;
            continue;
          }
          
          if (!$handled) continue;
          
          // Submitted data was handled successfully, now remove the handler
          // from the session
          $request->session->removeValue($identifier);
          
          // Tell the handler to finalize itself. This may include adding a 
          // node to the formresult or sending a redirect to another page
          $this->handlers[$i]->finalize($request, $response);
          $status[$name]= HANDLER_SUCCESS;
        }
      }
      
      // Reflect handler stati into formresult
      with ($n= &$response->addFormResult(new Node('handlers'))); {
        foreach ($status as $name => $value) {
          $n->addChild(new Node('handler', $value, array('name' => $name)));
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
