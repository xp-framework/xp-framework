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
   * represented in the <pre>OutputDocument</pre> class by corresponding
   * member variables. For ease of their manipulation, there are three
   * method in <pre>XMLSriptletResponse</pre> to add nodes to them. The
   * XSL stylesheet is applied against this XML.
   *
   * All request parameters are imported into the formvalues node to give
   * you access to the request parameters withing your XSL stylesheet (e.g.,
   * via /document/formresult/formvalues/param[@name= 'query']). You might
   * want to define an xsl:variable containing the formvalues for easier
   * access.
   *
   * Farthermore, the following attributes are passed as external parameters:
   * <pre>
   *   page      the page displayed
   *   lang      the language in which this page is displayed
   *   product   the product (think of it as "theme")
   *   sess      the session's id
   * </pre>
   * 
   * @see org.apache.xml.XMLSriptletRequest
   * @see org.apache.xml.XMLSriptletResponse
   * @see org.apache.xml.XMLSriptletResponse#addFormValue
   * @see org.apache.xml.XMLSriptletResponse#addFormError
   * @see org.apache.xml.XMLSriptletResponse#addFormResult
   */
  class XMLScriptlet extends HttpScriptlet {
    var 
      $document         = NULL,
      $stylesheetBase   = '';
      
    /**
     * Constructor
     *
     * @access  public
     * @param   string stylesheetBase
     */
    function __construct($stylesheetBase) {
      $this->stylesheetBase= $stylesheetBase;
      parent::__construct();
    }
    
    /**
     * Set our own response object
     *
     * @access  protected
     * @see     xp://org.apache.HttpScriptlet#_response
     */
    function _response() {
      $this->response= &new XMLScriptletResponse();
    }

    /**
     * Set our own request object
     *
     * @access  protected
     * @see     xp://org.apache.HttpScriptlet#_request
     */
    function _request() {
      $this->request= &new XMLScriptletRequest();
    }
    
    /**
     * Initialize. Calls doCreate if necessary (the environment variable
     * "PRODUCT" is not set - which it will be if the RewriteRule has
     * taken control).
     *
     * @access  public
     */
    function init() {
      parent::init();
      if (FALSE === getenv('PRODUCT')) $this->request->method= 'CREATE';
    }
    
    /**
     * Handle method
     *
     * @access  protected
     * @param   string method
     * @return  string method
     * @see     xp://org.apache.xml.XMLScriptlet#_handleMethod
     */
    function _handleMethod($method) {
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
    function doCreate(&$request, &$response) {
      $uri= $request->getURI();
      $defaultProduct= getenv('DEF_PROD');
      $defaultLanguage= getenv('DEF_LANG');
      $response->sendRedirect(sprintf(
        '%s://%s/xml/%s:%s/static', 
        $uri['scheme'],
        $uri['host'],          
        $defaultProduct ? $defaultProduct : 'site',
        $defaultLanguage ? $defaultLanguage : 'en_US'
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
     * @throws  Exception to indicate failure
     */
    function doCreateSession(&$request, &$response) {
      $uri= $request->getURI();
      $defaultProduct= getenv('DEF_PROD');
      $defaultLanguage= getenv('DEF_LANG');
      $response->sendRedirect(sprintf(
        '%s://%s/xml/%s:%s;psessionid=%s/static', 
        $uri['scheme'],
        $uri['host'],          
        $defaultProduct ? $defaultProduct : 'site',
        $defaultLanguage ? $defaultLanguage : 'en_US',
        $request->session->getId()
      ));
      
      return FALSE; // Indicate no further processing is to be done
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
    function doGet(&$request, &$response) {
    
      // Define special parameters
      $response->setParam('page',    $request->getPage());
      $response->setParam('frame',   $request->getFrame());
      $response->setParam('lang',    $request->getLanguage());
      $response->setParam('product', $request->getProduct());
      $response->setParam('sess',    $request->getSessionId());
      
      // Add all request parameters to the formvalue node
      foreach (array_keys($request->params) as $key) {
        $response->addFormValue($key, $request->params[$key]);
      }
      
      // Set XSL stylesheet
      $response->setStylesheet(sprintf(
        '%s%s/%s/%s.xsl',
        $this->stylesheetBase,
        $response->getParam('product'),
        $response->getParam('lang'),
        $response->getParam('page')
      ));
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
    function doPost(&$req, &$res) {
      $this->doGet($req, $res);
    }
  }
?>
