<?php
/* This class is part of the XP framework
 *
 * $Id: AbstractXMLScriptlet.class.php 10594 2007-06-11 10:04:54Z friebe $ 
 */

  namespace scriptlet::xml::workflow;

  uses(
    'scriptlet.xml.XMLScriptlet',
    'scriptlet.xml.workflow.WorkflowScriptletRequest'
  );

  /**
   * Workflow model scriptlet implementation
   *
   * @purpose  Base class
   */
  class AbstractXMLScriptlet extends scriptlet::xml::XMLScriptlet {
    public
      $package  = NULL;

    /**
     * Constructor
     *
     * @param   string package
     * @param   string base default ''
     */
    public function __construct($package, $base= '') {
      parent::__construct($base);
      $this->package= $package;
    }

    /**
     * Create the request object
     *
     * @return  scriptlet.xml.workflow.WorkflowScriptletRequest
     */
    protected function _request() {
      return new WorkflowScriptletRequest($this->package);
    }
    
    /**
     * Retrieve context class
     *
     * @param   scriptlet.xml.workflow.WorkflowScriptletRequest request
     * @return  lang.XPClass
     * @throws  lang.ClassNotFoundException
     */
    public function getContextClass($request) {
      return lang::XPClass::forName($this->package.'.'.(ucfirst($request->getProduct()).'Context'));
    }

    /**
     * Decide whether a session is needed
     *
     * @param   scriptlet.xml.workflow.WorkflowScriptletRequest request
     * @return  bool
     */
    public function needsSession($request) {
      return ($request->state && (
        $request->state->hasHandlers() || 
        $request->state->requiresAuthentication()
      ));
    }
    
    /**
     * Decide whether a context is needed. Returns FALSE in this default
     * implementation.
     *
     * @param   scriptlet.xml.workflow.WorkflowScriptletRequest request
     * @return  bool
     */
    public function wantsContext($request) {
      return FALSE;
    }
    
    /**
     * Process workflow. Calls the state's setup() and process() 
     * methods in this order. May be overwritten by subclasses.
     *
     * Return FALSE from this method to indicate no further 
     * processing is to be done
     *
     * @param   scriptlet.xml.workflow.WorkflowScriptletRequest request 
     * @param   scriptlet.xml.XMLScriptletResponse response 
     * @return  bool
     */
    public function processWorkflow($request, $response) {

      // Context initialization
      $context= NULL;
      if ($this->wantsContext($request) && $request->hasSession()) {
      
        // Set up context. The context contains - so to say - the "autoglobals",
        // in other words, the omnipresent data such as the user
        try {
          $class= $this->getContextClass($request);;
        } catch (lang::ClassNotFoundException $e) {
          throw(new scriptlet::HttpScriptletException($e->getMessage()));
          return FALSE;
        }
      
        // Get context from session. If it is not available there, set up the 
        // context and store it to the session.
        $cidx= $class->getName();
        if (!($context= $request->session->getValue($cidx))) {
          $context= $class->newInstance();

          try {
            $context->setup($request);
          } catch (lang::IllegalStateException $e) {
            throw(new scriptlet::HttpScriptletException($e->getMessage(), HTTP_INTERNAL_SERVER_ERROR));
            return FALSE;
          } catch (lang::IllegalArgumentException $e) {
            throw(new scriptlet::HttpScriptletException($e->getMessage(), HTTP_NOT_ACCEPTABLE));
            return FALSE;
          } catch (lang::IllegalAccessException $e) {
            throw(new scriptlet::HttpScriptletException($e->getMessage(), HTTP_FORBIDDEN));
            return FALSE;
          }
          $request->session->putValue($cidx, $context);
        }

        // Run context's process() method.
        try {
          $context->process($request);
        } catch (lang::IllegalStateException $e) {
          throw(new scriptlet::HttpSessionInvalidException($e->getMessage(), HTTP_BAD_REQUEST));
          return FALSE;
        } catch (lang::IllegalAccessException $e) {
          throw(new scriptlet::HttpScriptletException($e->getMessage(), HTTP_FORBIDDEN));
          return FALSE;
        }

        delete($class);
      }
      
      // Call state's setup() method
      try {
        $request->state->setup($request, $response, $context);
      } catch (lang::IllegalStateException $e) {
        throw(new scriptlet::HttpScriptletException($e->getMessage(), HTTP_INTERNAL_SERVER_ERROR));
        return FALSE;
      } catch (lang::IllegalArgumentException $e) {
        throw(new scriptlet::HttpScriptletException($e->getMessage(), HTTP_NOT_ACCEPTABLE));
        return FALSE;
      } catch (lang::IllegalAccessException $e) {
        throw(new scriptlet::HttpScriptletException($e->getMessage(), HTTP_FORBIDDEN));
        return FALSE;
      }
      
      // Call state's process() method. In case it returns FALSE, the
      // context's insertStatus() method will not be called. This, for
      // example, is useful when process() wants to send a redirect.
      if (FALSE === ($r= $request->state->process($request, $response, $context))) {
        return FALSE;
      }
      
      // If there is no context, we're finished
      if (!$context) return;

      // Tell context to insert form elements. Then store it, if necessary
      $context->insertStatus($response);
      $context->getChanged() && $request->session->putValue($cidx, $context);
    }

    /**
     * Process request
     *
     * @param   scriptlet.xml.XMLScriptletRequest request 
     * @param   scriptlet.xml.XMLScriptletResponse response 
     */
    public function processRequest($request, $response) {
      if (FALSE === $this->processWorkflow($request, $response)) {
      
        // The processWorkflow() method indicates no further processing
        // is to be done. Pass result "up".
        return FALSE;
      }

      return parent::processRequest($request, $response);
    }
  }
?>
