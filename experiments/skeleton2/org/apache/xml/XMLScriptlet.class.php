<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'org.apache.HttpScriptlet',
    'org.apache.xml.XMLScriptletResponse',
    'org.apache.xml.XMLScriptletRequest'
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
   *   page      the page displayed
   *   frame     the frame displayed
   *   lang      the language in which this page is displayed
   *   product   the product (think of it as "theme")
   *   sess      the session's id
   * </pre>
   * 
   * @see      xp://org.apache.xml.XMLSriptletRequest
   * @see      xp://org.apache.xml.XMLSriptletResponse
   * @see      xp://org.apache.xml.XMLSriptletResponse#addFormValue
   * @see      xp://org.apache.xml.XMLSriptletResponse#addFormError
   * @see      xp://org.apache.xml.XMLSriptletResponse#addFormResult
   * @purpose  Base class for websites using XML/XSL to render their output
   */
  class XMLScriptlet extends HttpScriptlet {
    public 
      $document         = NULL,
      $stylesheetBase   = '';
      
    /**
     * Constructor
     *
     * @access  public
     * @param   string stylesheetBase
     */
    public function __construct($stylesheetBase) {
      $this->stylesheetBase= $stylesheetBase;
      parent::__construct();
    }
    
    /**
     * Set our own response object
     *
     * @access  protected
     * @see     xp://org.apache.HttpScriptlet#_response
     */
    protected function _response() {
      $this->response= new XMLScriptletResponse();
    }

    /**
     * Set our own request object
     *
     * @access  protected
     * @see     xp://org.apache.HttpScriptlet#_request
     */
    protected function _request() {
      $this->request= new XMLScriptletRequest();
    }
    
    /**
     * Initialize. Calls doCreate if necessary (the environment variable
     * "PRODUCT" is not set - which it will be if the RewriteRule has
     * taken control).
     *
     * @access  public
     */
    public function init() {
      parent::init();
      if (FALSE === getenv('PRODUCT')) $this->request->method= 'CREATE';
      $this->request->state= getenv('STATE');
      $this->request->language= getenv('LANG');
      $this->request->page= $this->request->getParam('__page');
    }
    
    /**
     * Handle method
     *
     * @access  protected
     * @param   string method
     * @return  string method
     * @see     xp://org.apache.xml.XMLScriptlet#_handleMethod
     */
    protected function _handleMethod($method) {
      if ('CREATE' == $method) {
        $this->_method= 'doCreate';
        return $this->_method;
      }
      return parent::_handleMethod($method);
    }
    
    /**
     * Create - redirects to /xml/$pr:$ll_LL/static if 
     * necessary, regarding the environment variables DEF_PROD and 
     * DEF_LANG as values for $pr and $ll_LL. If these aren't set, "site" and
     * "en_US" are assumed as default values.
     *
     * @access  protected
     * @param   &org.apache.xml.XMLScriptletRequest request
     * @param   &org.apache.xml.XMLScriptletResponse response
     * @return  bool
     */
    protected function doCreate(&$request, &$response) {
      $uri= $request->getURI();

      // Get product and language from the environment if necessary
      if (!($product= $request->getProduct())) $product= getenv('DEF_PROD');
      if (!($language= $request->getLanguage())) $language= getenv('DEF_LANG');

      // Get state and page from request
      $state= $request->getState();
      $page= $request->getPage();
      
      // Send redirect
      $response->sendRedirect(sprintf(
        '%s://%s/xml/%s;%s/%s?__page=%s', 
        $uri['scheme'],
        $uri['host'],          
        $product ? $product : 'site',
        $language ? $language : 'en_US',
        $state ? $state : 'static',
        $page ? $page : 'home'
      ));
      
      return FALSE; // Indicate no further processing is to be done
    }

    /**
     * Creates a session. 
     *
     * @access  protected
     * @return  bool processed
     * @param   &org.apache.HttpScriptletRequest request 
     * @param   &org.apache.HttpScriptletResponse response 
     */
    public function doCreateSession(&$request, &$response) {
      $uri= $request->getURI();
      
      // Get product and language from the environment if necessary
      if (!($product= $request->getProduct())) $product= getenv('DEF_PROD');
      if (!($language= $request->getLanguage())) $language= getenv('DEF_LANG');
      
      // Get state and page from request
      $state= $request->getState();
      $page= $request->getPage();
      
      // Send redirect
      $response->sendRedirect(sprintf(
        '%s://%s/xml/%s;%s;psessionid=%s/%s?__page=%s', 
        $uri['scheme'],
        $uri['host'],          
        $product ? $product : 'site',
        $language ? $language : 'en_US',
        $request->session->getId(),
        $state ? $state : 'static',
        $page ? $page : 'home'
      ));
      
      return FALSE; // Indicate no further processing is to be done
    }

    /**
     * Sets the responses XSL stylesheet
     *
     * @access  private
     * @param   &org.apache.scriptlet.XMLScriptletRequest
     * @param   &org.apache.scriptlet.XMLScriptletResponse
     */
    private function _setStylesheet(&$request, &$response) {
      $response->setStylesheet(sprintf(
        '%s%s/%s/%s/%s.xsl',
        $this->stylesheetBase,
        $request->getProduct(),
        $request->getLanguage(),
        $request->getState(),
        $request->getPage()
      ));
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
     * @access  protected
     * @return  bool processed
     * @param   &org.apache.HttpScriptletRequest request 
     * @param   &org.apache.HttpScriptletResponse response 
     * @throws  Exception to indicate failure
     * @see     xp://org.apache.HttpScriptlet#doGet
     */
    protected function doGet(&$request, &$response) {
    
      // Define special parameters
      $response->setParam('page',    $request->getPage());
      $response->setParam('state',   $request->getState());
      $response->setParam('frame',   $request->getFrame());
      $response->setParam('lang',    $request->getLanguage());
      $response->setParam('product', $request->getProduct());
      $response->setParam('sess',    $request->getSessionId());
      $response->setParam('query',   $request->getEnvValue('QUERY_STRING'));
      
      // Set XSL stylesheet
      self::_setStylesheet($request, $response);

      // Add all request parameters to the formvalue node
      foreach ($request->params as $key => $value) {
        $response->addFormValue($key, $value);
      }
    }
    
    /**
     * Simply call doGet
     *
     * @access  protected
     * @return  bool processed
     * @param   &org.apache.HttpScriptletRequest request 
     * @param   &org.apache.HttpScriptletResponse response 
     * @throws  Exception to indicate failure
     * @see     xp://org.apache.HttpScriptlet#doPost
     */
    protected function doPost(&$req, &$res) {
      self::doGet($req, $res);
    }
  }
?>
