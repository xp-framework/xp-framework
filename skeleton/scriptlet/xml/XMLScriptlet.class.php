<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses(
    'xml.DomXSLProcessor',
    'scriptlet.HttpScriptlet',
    'scriptlet.xml.XMLScriptletURL',
    'scriptlet.xml.XMLScriptletResponse',
    'scriptlet.xml.XMLScriptletRequest'
  );
  
  /**
   * XML scriptlets are the more advanced version of HttpScriptlets.
   * XML scriptlets do not implement a direct output to the client. 
   * Rather, the response object consists of a so-called "OutputDocument"
   * (resembling an XML DOM-Tree) and an XSL stylesheet.
   *
   * The three main nodes, formresult, formvalues and formerrors are 
   * represented in the OutputDocument class by corresponding
   * member variables. For ease of their manipulation, there are three
   * method in XMLSriptletResponse to add nodes to them. The
   * XSL stylesheet is applied against this XML.
   *
   * All request parameters are imported into the formvalues node to give
   * you access to the request parameters withing your XSL stylesheet (e.g.,
   * via /formresult/formvalues/param[@name= 'query']). You might
   * want to define an xsl:variable containing the formvalues for easier
   * access.
   *
   * Farthermore, the following attributes are passed as external parameters:
   * <pre>
   *   Name        Meaning
   *   ----------- -------------------------------------------------
   *   __state     the current state
   *   __lang      the language in which this page is displayed
   *   __product   the product (think of it as "theme")
   *   __sess      the session's id
   *   __query     the query string
   * </pre>
   * 
   * @see      xp://scriptlet.xml.XMLScriptletRequest
   * @see      xp://scriptlet.xml.XMLScriptletResponse
   * @see      xp://scriptlet.xml.XMLScriptletResponse#addFormValue
   * @see      xp://scriptlet.xml.XMLScriptletResponse#addFormError
   * @see      xp://scriptlet.xml.XMLScriptletResponse#addFormResult
   * @test     xp://net.xp_framework.unittest.scriptlet.XmlScriptletTest
   * @purpose  Base class for websites using XML/XSL to render their output
   */
  class XMLScriptlet extends HttpScriptlet {
    public 
      $processor = NULL;
      
    /**
     * Constructor
     *
     * @param   string base default ''
     */
    public function __construct($base= '') {
      $this->processor= $this->_processor();
      $this->processor->setBase($base);
    }
    
    /**
     * Set our own processor object
     *
     * @return  xml.IXSLProcessor
     */
    protected function _processor() {
      return new DomXSLProcessor();
    }
    
    /**
     * Set our own response object
     *
     * @return  scriptlet.xml.XMLScriptletResponse
     * @see     xp://scriptlet.HttpScriptlet#_response
     */
    protected function _response() {
      $response= new XMLScriptletResponse();
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
      return new XMLScriptletRequest();
    }
    
    /**
     * Returns an URL object for the given URL
     *
     * @param string url The current requested URL
     * @return scriptlet.XMLScriptletURL
     */
    protected function _url($url) {
      return new XMLScriptletURL($url);
    }
    
    /**
     * Handle method. Calls doCreate if necessary (the environment variable
     * "PRODUCT" is not set - which it will be if the RewriteRule has
     * taken control).
     *
     * @param   scriptlet.xml.XMLScriptletRequest request
     * @return  string class method (one of doGet, doPost, doHead)
     * @see     xp://scriptlet.xml.XMLScriptlet#_handleMethod
     */
    public function handleMethod($request) {
      // XXX TDB
      // if (!$request->getEnvValue('PRODUCT')) {
      //   return 'doCreate';
      // }

      return parent::handleMethod($request);
    }
    
    /**
     * Helper method for doCreate() and doCreateSession()
     *
     * @param   scriptlet.xml.XMLScriptletRequest request
     * @param   scriptlet.xml.XMLScriptletResponse response
     * @param   string sessionId default NULL
     * @return  bool
     */
    public function doRedirect($request, $response, $sessionId= NULL) {
      with ($redirect= $request->getURL()); {
    
        // Include session id in URL if available
        if ($sessionId !== NULL) $redirect->setSessionId($sessionId);

        $response->sendRedirect($redirect->getURL());
      }
      
      return FALSE; // Indicate no further processing is to be done
    }
    
    /**
     * Create - redirects to /xml/$pr:$ll_LL/static if 
     * necessary, regarding the environment variables DEF_PROD and 
     * DEF_LANG as values for $pr and $ll_LL. If these aren't set, "site" and
     * "en_US" are assumed as default values.
     *
     * @param   scriptlet.xml.XMLScriptletRequest request
     * @param   scriptlet.xml.XMLScriptletResponse response
     * @return  bool
     */
    public function doCreate($request, $response) {
      return $this->doRedirect($request, $response);
    }

    /**
     * Creates a session. 
     *
     * @return  bool processed
     * @param   scriptlet.HttpScriptletRequest request 
     * @param   scriptlet.HttpScriptletResponse response 
     */
    public function doCreateSession($request, $response) {
      return $this->doRedirect($request, $response, $request->session->getId());
    }

    /**
     * Sets the responses XSL stylesheet
     *
     * @param   scriptlet.scriptlet.XMLScriptletRequest request
     * @param   scriptlet.scriptlet.XMLScriptletResponse response
     */
    protected function _setStylesheet($request, $response) {
      $response->setStylesheet(sprintf(
        '%2$s%1$s%3$s%1$s%4$s.xsl',
        DIRECTORY_SEPARATOR,
        $request->getProduct(),
        $request->getLanguage(),
        $request->getStateName()
      ));
    }

    /**
     * Process request
     *
     * @param   scriptlet.xml.XMLScriptletRequest request 
     * @param   scriptlet.xml.XMLScriptletResponse response 
     */
    public function processRequest($request, $response) {

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
      return $this->processRequest($request, $response);
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
      return $this->processRequest($request, $response);
    }
  }
?>
