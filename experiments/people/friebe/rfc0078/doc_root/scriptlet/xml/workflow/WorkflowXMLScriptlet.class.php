<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'scriptlet.HttpScriptlet', 
    'scriptlet.xml.workflow.WorkflowXMLScriptletRequest',
    'scriptlet.xml.workflow.WorkflowXMLScriptletResponse'
  );

  /**
   * Workflow model scriptlet implementation
   *
   * @purpose  Base class
   */
  class WorkflowXMLScriptlet extends HttpScriptlet {
    protected
      $processor  = NULL,
      $package    = NULL;

    /**
     * Constructor
     *
     * @param   string package
     * @param   string base default ''
     */
    function __construct($package, $base= '') {
      $this->package= $package;
      $this->processor= new DomXSLProcessor();
      $this->processor->setBase($base);
    }

    /**
     * Set our own response object
     *
     * @return  scriptlet.xml.XMLScriptletResponse
     * @see     xp://scriptlet.HttpScriptlet#_response
     */
    protected function _response() {
      $response= new WorkflowXMLScriptletResponse();
      $response->setProcessor($this->processor);
      return $response;
    }

    /**
     * Set our own request object
     *
     * @return  scriptlet.xml.XMLScriptletRequest
     * @see     xp://scriptlet.HttpScriptlet#_request
     */
    protected function _request() {
      return new WorkflowXMLScriptletRequest($this->package);
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
    protected final function processWorkflow($request, $response) {

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
      if ($request->hasSession()) {
      
        // Set up context. The context contains - so to say - the "autoglobals",
        // in other words, the omnipresent data such as, for example, the user
        try {
          $class= $request->area->getContextClass($request);
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
        $route= $request->area->getRouter()->route($this->package.'.state.', $request, $response, $context);
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

      // Define special parameters
      $response->setParam('state',   $request->getStateName());
      $response->setParam('page',    $request->getPage());
      $response->setParam('lang',    $request->getLanguage());
      $response->setParam('product', $request->getProduct());
      $response->setParam('sess',    $request->getSessionId());
      $response->setParam('query',   $request->getQueryString());
      
      // Set XSL stylesheet
      $response->hasStylesheet() || $this->_setStylesheet($request, $response);

      // Add all request parameters to the formvalue node
      foreach ($request->params as $key => $value) {
        $response->addFormValue($key, $value);
      }
      
      // If there is no context, we're finished
      if (!$context) return;

      // Tell context to insert form elements. Then store it, if necessary
      $context->insertStatus($response);
      $context->getChanged() && $request->session->putValue($cidx, $context);
    }

    /**
     * Handle all requests. This method is called from <pre>doPost</pre> since
     * it really makes no difference - one can still find out via the 
     * <pre>method</pre> attribute of the request object. 
     *
     * Remember:
     * When overriding this method, please make sure you include all your 
     * sourcecode _before_ you call <pre>parent::doGet()</pre>
     *
     * @return  bool processed
     * @param   scriptlet.xml.XMLScriptletRequest request 
     * @param   scriptlet.xml.XMLScriptletResponse response 
     * @throws  lang.XPException to indicate failure
     * @see     xp://scriptlet.HttpScriptlet#doGet
     */
    public function doGet($request, $response) {
      return $this->processWorkflow($request, $response);
    }
    
    /**
     * Simply call doGet
     *
     * @return  bool processed
     * @param   scriptlet.xml.XMLScriptletRequest request 
     * @param   scriptlet.xml.XMLScriptletResponse response 
     * @throws  lang.XPException to indicate failure
     * @see     xp://scriptlet.HttpScriptlet#doPost
     */
    public function doPost($request, $response) {
      return $this->processWorkflow($request, $response);
    }
  }
?>
