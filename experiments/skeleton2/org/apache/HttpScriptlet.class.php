<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'org.apache.HttpScriptletRequest',
    'org.apache.HttpScriptletResponse',
    'org.apache.HttpScriptletException',
    'org.apache.HttpSessionInvalidException',
    'org.apache.HttpSession'
  );

  // HTTP methods
  define('HTTP_METHOD_GET',     'GET');
  define('HTTP_METHOD_POST',    'POST');
  define('HTTP_METHOD_HEAD',    'HEAD');
  define('HTTP_METHOD_PUT',     'PUT');
  define('HTTP_METHOD_DELETE',  'DELETE');
  define('HTTP_METHOD_OPTIONS', 'OPTIONS');
 
  
  
  /**
   * Scriptlets are the counterpart to Java's Servlets - as one might
   * have guessed from their name. Scriptlets, in comparison to Java
   * servlets, are terminated at the end of a request, their resources
   * freed and (non-persistent) connections, files etc. closed. 
   * Scriptlets are not a 1:1 implementation of Servlets though one
   * might find a lot of similarities!
   * 
   * This class is the base class for your application and really does
   * nothing except for providing you whith a simple way of creating
   * dynamic web pages. 
   *
   * For the beginning, in your class extending this one, simply override
   * the <pre>doGet()</pre> method and put any source there to be executed 
   * on a HTTP GET request.
   *
   * Example:
   * <code>
   *   uses('org.apache.HttpScriptlet');
   * 
   *   class MyScriptlet extends HttpScriplet {
   *     function doGet(&$request, &$response) {
   *       $response->write('Hello World');
   *     }
   *   }
   * </code>
   *
   * <code>
   *   uses('foo.bar.MyScriptlet');
   *
   *   $s= new MyScriptlet();
   *   try(); {
   *     $s->init();
   *     $response= $s->process();
   *   } if (catch('HttpScriptletException', $e)) {
   *     // Retrieve standard "Internal Server Error"-Document
   *     $response= $e->getResponse(); 
   *   }
   * 
   *   $response->sendHeaders();
   *   $response->sendContent();
   * 
   *   $s->finalize();
   * </code>
   */
  class HttpScriptlet extends Object {
    public
      $request          = NULL,
      $response         = NULL,
      $needsSession     = FALSE,
      $sessionURIFormat = '%1$s://%2$s%3$s/%6$s?%s&psessionid=%7$s';
    
    protected
      $_method= NULL;
  
    /**
     * Constructor
     *
     * @access  public
     */  
    public function __construct() {
      self::_request();
      self::_response();
      
    }

    /**
     * Destructor
     *
     * @access  public
     */  
    public function __destruct() {
      $this->request->__destruct();
      $this->response->__destruct();
      
    }
    
    /**
     * Create a request object. Override this method to define
     * your own request object
     *
     * @access  protected
     */
    protected function _request() {
      $this->request= new HttpScriptletRequest();
    }
    
    /**
     * Create a session object. Override this method to define
     * your own session object
     *
     * @access  protected
     */
    protected function _session() {
      $this->request->setSession(new HttpSession());
    }
    
    /**
     * Create a response object. Override this method to define
     * your own response object
     *
     * @access  protected
     */
    protected function _response() {
      $this->response= new HttpScriptletResponse();
    }
    
    /**
     * Handle the case when we find the given session invalid.
     * By default, we just return error for this, a derived class
     * may choose to gracefully handle this case.
     *
     * This function must return TRUE if the scriptlet is
     * supposed to continue processing the request.
     *
     * @access  protected
     * @return  bool continue
     */
    protected function _handleInvalidSession() {
      return FALSE;
    }
    
    /**
     * Handles the different HTTP methods. Supports GET, POST and
     * HEAD - other HTTP methods pose security risks if not handled
     * properly and are used very uncommly anyway. 
     * If you want to support these methods, override this method - 
     * make sure you call <pre>parent::_handleMethod($method)</pre>
     * so that the request object gets set up correctly before any
     * of your source is executed
     *
     * @access  protected
     * @return  string class method (one of doGet, doPost, doHead)
     * @param   string method Request-Method
     * @see     rfc://2616
     */
    protected function _handleMethod($method) {
      switch ($method) {
        case HTTP_METHOD_POST:
          $this->request->setData($GLOBALS['HTTP_RAW_POST_DATA']);
          $this->_method= 'doPost';
          break;
          
        case HTTP_METHOD_GET:
          $this->request->setData(getenv('QUERY_STRING'));
          $this->_method= 'doGet';
          break;
          
        case HTTP_METHOD_HEAD:
          $this->request->setData(getenv('QUERY_STRING'));
          $this->_method= 'doHead';
          break;        
          
        default:
          $this->_method= FALSE;
      }
      
      return $this->_method;
    }
    
    /**
     * Receives an HTTP GET request from the <pre>process()</pre> method
     * and handles it.
     *
     * When overriding this method, request parameters are read and acted
     * upon and the response object is used to set headers and add
     * output. The request objects contains a session object if one was
     * requested via <pre>needsSession</pre>. Return FALSE to indicate no
     * farther processing is needed - the response object's method 
     * <pre>process</pre> will not be called.
     * 
     * Example:
     * <code>
     *   function doGet(&$request, &$response) {
     *     if (NULL === ($name= $request->getParam('name'))) {
     *       // Display a form where name is entered
     *       // ...
     *       return;
     *     }
     *     $response->write('Hello '.$name);
     *   }
     * </code>
     *
     * @access  protected
     * @return  bool processed
     * @param   &org.apache.HttpScriptletRequest request 
     * @param   &org.apache.HttpScriptletResponse response 
     * @throws  Exception to indicate failure
     */
    public function doGet(HttpScriptletRequest $request, HttpScriptletResponse $response) {
    }
    
    /**
     * Receives an HTTP POST request from the <pre>process()</pre> method
     * and handles it.
     *
     * @access  protected
     * @return  bool processed
     * @param   &org.apache.HttpScriptletRequest request 
     * @param   &org.apache.HttpScriptletResponse response 
     * @throws  Exception to indicate failure
     */
    public function doPost(HttpScriptletRequest $request, HttpScriptletResponse $response) {
    }
    
    /**
     * Receives an HTTP HEAD request from the <pre>process()</pre> method
     * and handles it.
     *
     * Remember:
     * The HEAD method is identical to GET except that the server MUST NOT
     * return a message-body in the response. The metainformation contained
     * in the HTTP headers in response to a HEAD request SHOULD be identical
     * to the information sent in response to a GET request. This method can
     * be used for obtaining metainformation about the entity implied by the
     * request without transferring the entity-body itself. This method is
     * often used for testing hypertext links for validity, accessibility,
     * and recent modification.
     *
     * @access  protected
     * @return  bool processed
     * @param   &org.apache.HttpScriptletRequest request 
     * @param   &org.apache.HttpScriptletResponse response 
     * @throws  Exception to indicate failure
     */
    public function doHead(HttpScriptletRequest $request, HttpScriptletResponse $response) {
    }
    
    /**
     * Creates a session. This method will only be called if 
     * <pre>needsSession</pre> is set to TRUE and no session
     * is available or the session is unvalid.
     *
     * The member variable <pre>sessionURIFormat</pre> is used
     * to sprintf() the new URI:
     * <pre>
     * Ord Fill            Example
     * --- --------------- --------------------
     *   1 scheme          http
     *   2 host            host.foo.bar
     *   3 path            /foo/bar/index.html
     *   4 dirname(path)   /foo/bar/
     *   5 basename(path)  index.html
     *   6 query           a=b&b=c
     *   7 session id      cb7978876218bb7
     *   8 fraction        #test
     * </pre>
     *
     * @access  protected
     * @return  bool processed
     * @param   &org.apache.HttpScriptletRequest request 
     * @param   &org.apache.HttpScriptletResponse response 
     * @throws  Exception to indicate failure
     */
    public function doCreateSession(HttpScriptletRequest $request, HttpScriptletResponse $response) {
      $uri= $request->getURI();
      $response->sendRedirect(sprintf(
        $this->sessionURIFormat,
        $uri['scheme'],
        $uri['host'],
        $uri['path'],
        dirname($uri['path']),
        basename($uri['path']),
        $uri['query'],
        $request->session->getId(),
        $uri['fraction']
      ));
      return FALSE;
    }
    
    /**
     * Initialize the scriptlet. This method is called before any 
     * method processing is done.
     *
     * In this method, you can set up "global" requirements such as a 
     * configuration manager.
     *
     * @access  public
     */
    public function init() {
      $this->request->headers= array_change_key_case(getallheaders(), CASE_LOWER);
      $this->request->setParams(array_change_key_case($_REQUEST, CASE_LOWER));
      $this->request->method= getenv('REQUEST_METHOD');
      $this->request->setURI(parse_url(
        ('on' == getenv('HTTPS') ? 'https' : 'http').'://'.
        getenv('HTTP_HOST').
        getenv('REQUEST_URI')
      ));
    }
    
    /**
     * Finalize the scriptlet. This method is called after all response
     * headers and data has been sent and allows you to handle things such
     * as cleaning up resources or closing database connections.
     *
     * @access  public
     */
    public function finalize() {
    }
    
    /**
     * This method is called to process any request and dispatches
     * it to on of the do* -methods of the scriptlet. It will also
     * call the <pre>doCreateSession()</pre> method if necessary.
     *
     * @access  public
     * @return  &org.apache.HttpScriptletResponse the response object
     * @throws  org.apache.HttpScriptletException indicating fatal errors
     */
    public function process() {
      if (FALSE === self::_handleMethod($this->request->method)) {
        throw (new HttpScriptletException(sprintf(
          'HTTP method "%s" not supported - request was: %s',
          $this->request->method,
          var_export($this->request->headers, 1)
        )));
      }
      
      // Check if a session is present. This is either the case when a session
      // is already in the URL or if the scriptlet explicetly states it needs 
      // one (by setting the member variable "needsSession" to TRUE).
      if ($this->needsSession || $this->request->getSessionId()) {
        self::_session();
        try {
          $this->request->session->initialize($this->request->getSessionId());
          $valid= $this->request->session->isValid();
        } catch (XPException $e) {
          if (!self::_handleInvalidSession()) {
            throw (new HttpSessionInvalidException(
              'Session initialize failed: '.$e->message,
              HTTP_BAD_REQUEST
            ));
          }
        }
        
        // Do we need a new session?
        if (
          ($this->request->session->isNew()) ||
          (!$valid)
        ) {
          $this->_method= 'doCreateSession';
        }
      }

      // Perform locking when this is a SerializedScriptlet
      if (is('org.apache.SerializedScriptlet', $this)) {
        try { 
          self::lock(); 
        } catch (XPException $e) {
          throw (new HttpScriptletException(
            'Semaphore locking failed: '.$e->getMessage()
          ));
        }
      }

      // Call method handler and response processor
      try {
        if (FALSE !== call_user_func_array(
          array(&$this, $this->_method), 
          array(&$this->request, &$this->response)
        )) {
          $this->response->process();
        }
      } catch (XPException $e) {
        throw (new HttpScriptletException(
          'Request processing failed ['.$this->_method.']: '.$e->getMessage()
        ));
      }
      
      // Remove the semaphore
      if (is('org.apache.SerializedScriptlet', $this)) {
        try {
          self::unlock();
        } catch (XPException $e) {
          throw (new HttpScriptletException('Semaphore unlocking failed: '.$e->getMessage()));
        }
      }

      // Return it
      return $this->response;
    }
  }
?>
