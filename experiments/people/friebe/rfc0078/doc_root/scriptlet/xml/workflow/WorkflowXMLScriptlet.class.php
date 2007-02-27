<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'scriptlet.xml.XMLScriptlet', 
    'scriptlet.xml.workflow.routing.ClassRouter'
  );

  /**
   * Workflow model scriptlet implementation
   *
   * @purpose  Base class
   */
  class WorkflowXMLScriptlet extends XMLScriptlet {
    public
      $package  = NULL;

    /**
     * Constructor
     *
     * @param   string package
     * @param   string base default ''
     */
    function __construct($package, $base= '') {
      parent::__construct($base);
      $this->package= rtrim($package, '.');
    }
    
    /**
     * Create request filter for given request. Returns NULL in this 
     * default implementation.
     *
     * @param   scriptlet.xml.XMLScriptletRequest request
     * @return  scriptlet.xml.workflow.filters.XMLScriptletFilter
     */
    protected function requestFilterFor($request) {
      return NULL;
    }

    /**
     * Create the router object. Returns a ClassRouter in this implementation
     * which resembles the previously hardcoded behaviour.
     *
     * @param   scriptlet.xml.XMLScriptletRequest request
     * @return  scriptlet.xml.workflow.routing.Router
     */
    protected function routerFor($request) {
      return new ClassRouter();
    }
    /**
     * Retrieve context class
     *
     * @param   scriptlet.xml.XMLScriptletRequest request
     * @return  lang.XPClass
     * @throws  lang.ClassNotFoundException
     */
    protected function getContextClass($request) {
      return XPClass::forName($this->package.'.'.(ucfirst($request->getProduct()).'Context'));
    }

    /**
     * Decide whether a session is needed
     *
     * @param   scriptlet.xml.XMLScriptletRequest request
     * @return  bool
     */
    public function needsSession($request) {
      return TRUE;
      ($request->state && (
        $request->state->hasHandlers() || 
        $request->state->requiresAuthentication()
      ));
    }
    
    /**
     * Decide whether a context is needed. Returns FALSE in this default
     * implementation.
     *
     * @param   scriptlet.xml.XMLScriptletRequest request
     * @return  bool
     */
    protected function wantsContext($request) {
      return FALSE;
    }
    
    /**
     * Process workflow. Calls the state's setup() and process() 
     * methods in this order. May NOT be overwritten by subclasses.
     *
     * Return FALSE from this method to indicate no further 
     * processing is to be done
     *
     * @param   scriptlet.xml.XMLScriptletRequest request 
     * @param   scriptlet.xml.XMLScriptletResponse response 
     * @return  bool
     */
    protected function processWorkflow($request, $response) {

      // Request filters (optional)
      if ($filter= $this->requestFilterFor($request)) {
        try {
          $filter->filter($request, $response);
        } catch (IllegalAccessException $e) {
          throw new HttpScriptletException($e->getMessage(), HTTP_FORBIDDEN);
        }
      }

      // Context initialization
      $context= NULL;
      if ($this->wantsContext($request) && $request->hasSession()) {
      
        // Set up context. The context contains - so to say - the "autoglobals",
        // in other words, the omnipresent data such as, for example, the user
        try {
          $class= $this->getContextClass($request);
        } catch (ClassNotFoundException $e) {
          throw new HttpScriptletException($e->getMessage());
        }
      
        // Get context from session. If it is not available there, set up the 
        // context and store it to the session.
        $cidx= $class->getName();
        if (!($context= $request->session->getValue($cidx))) {
          $context= $class->newInstance();

          try {
            $context->setup($request);
          } catch (IllegalStateException $e) {
            throw new HttpScriptletException($e->getMessage(), HTTP_INTERNAL_SERVER_ERROR);
          } catch (IllegalArgumentException $e) {
            throw new HttpScriptletException($e->getMessage(), HTTP_NOT_ACCEPTABLE);
          } catch (IllegalAccessException $e) {
            throw new HttpScriptletException($e->getMessage(), HTTP_FORBIDDEN);
          }
          $request->session->putValue($cidx, $context);
        }

        // Run context's process() method.
        try {
          $context->process($request);
        } catch (IllegalStateException $e) {
          throw new HttpSessionInvalidException($e->getMessage(), HTTP_BAD_REQUEST);
        } catch (IllegalAccessException $e) {
          throw new HttpScriptletException($e->getMessage(), HTTP_FORBIDDEN);
        }

        delete($class);
      }
      
      // Routing
      try {
        $route= $this->routerFor($request)->route($this->package.'.state.', $request, $response, $context);
        $route->dispatch($request, $response, $context);
      } catch (ClassNotFoundException $e) {
        throw new HttpScriptletException($e->getMessage(), HTTP_METHOD_NOT_ALLOWED);
      } catch (IllegalStateException $e) {
        throw new HttpScriptletException($e->getMessage(), HTTP_INTERNAL_SERVER_ERROR);
      } catch (IllegalArgumentException $e) {
        throw new HttpScriptletException($e->getMessage(), HTTP_NOT_ACCEPTABLE);
      } catch (IllegalAccessException $e) {
        throw new HttpScriptletException($e->getMessage(), HTTP_FORBIDDEN);
      }
      
      // If dispatching returns FALSE, the context's insertStatus() method 
      // will  not be called. This, for example, is useful when a state wants 
      // to send a redirect.
      if (FALSE === $r) return FALSE;
      
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
