<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'org.apache.HttpScriptlet',
    'org.webdav.xml.WebdavPropFindRequest',
    'org.webdav.xml.WebdavPropFindResponse'
  );
  
  define('WEBDAV_METHOD_PROPFIND',  'PROPFIND');
  define('WEBDAV_METHOD_PROPPATCH', 'PROPPATCH');
  
  /**
   * Webdav
   * 
   * <code>
   *   $s= &new WebdavScriptlet(array(
   *    '/webdav/' => new DavFileImpl('/path/to/files/you/want/do/provide/')
   *   ));
   *   try(); {
   *     $s->init();
   *     $response= &$s->process();
   *   } if (catch('HttpScriptletException', $e)) {
   *     // Retreive standard "Internal Server Error"-Document
   *     $response= &$e->getResponse(); 
   *   }
   *   
   *   $response->sendHeaders();
   *   $response->sendContent();
   *   $s->finalize();  
   * </code>
   *
   * Note: Needs PHP patched to work!
   *
   * @see      http://sitten-polizei.de/php/webdav.patch
   * @see      http://www.webdav.org/
   * @see      http://www.webdav.org/cadaver/
   * @see      rfc://2518 (WebDAV)
   * @see      rfc://2616 (HTTP/1.1)
   * @see      rfc://3253 (DeltaV)
   * @purpose  Provide the base for Webdav Services
   */
  class WebdavScriptlet extends HttpScriptlet {
    var
      $impl         = array(),
      $handlingImpl = NULL;
      
    /**
     * Constructor
     *
     * @access  public
     * @param   array impl (associative array of pathmatch => org.webdav.impl.DavImpl)
     */  
    function __construct($impl) {
      $this->impl= $impl;
      parent::__construct();
    }

    /**
     * Handle OPTIONS
     *
     * @see     xp://org.apache.scriptlet.HttpScriptlet#doGet
     * @access  private
     * @return  bool processed
     * @public  request org.apache.HttpScriptletRequest
     * @access  response org.apache.HttpScriptletResponse
     * @throws  Exception to indicate failure
     */
    function doOptions(&$request, &$response) {
      $response->setHeader('MS-Author-Via', 'DAV'); // MS-clients
      $response->setHeader('Allow', implode(', ', array(
        HTTP_METHOD_OPTIONS,
        HTTP_METHOD_GET,
        HTTP_METHOD_PUT,
        HTTP_METHOD_DELETE,
        WEBDAV_METHOD_PROPFIND,
        WEBDAV_METHOD_PROPPATCH
      )));
      $response->setHeader('DAV', '1, 2');
    }
    
    /**
     * Receives an PROPPATCH request from the <pre>process()</pre> method
     * and handles it.
     *
     * @see     xp://org.apache.scriptlet.HttpScriptlet#doGet
     * @access  private
     * @return  bool processed
     * @public  request org.apache.HttpScriptletRequest
     * @access  response org.apache.HttpScriptletResponse
     * @throws  Exception to indicate failure
     */
    function doPropFind(&$request, &$response) {
      try(); {
        $multistatus= &$this->handlingImpl->propfind(
          new WebdavPropFindRequest($request),
          new WebdavPropFindResponse()
        );
      } if (catch('Exception', $e)) {
        return throw(new HttpScriptletException($e->message));
      }
      
      // Send "HTTP/1.1 207 Multi-Status" response header
      $response->setStatus(207);
      $response->setHeader('Content-Type', 'text/xml');
      
      $response->setContent($multistatus->getSource(0));
    }

    /**
     * Receives an PROPPATCH request from the <pre>process()</pre> method
     * and handles it.
     *
     * @see     xp://org.apache.scriptlet.HttpScriptlet#doGet
     * @access  private
     * @return  bool processed
     * @public  request org.apache.HttpScriptletRequest
     * @access  response org.apache.HttpScriptletResponse
     * @throws  Exception to indicate failure
     */
    function doPropPatch(&$request, &$response) {
    }
  
    /**
     * Handle methods
     *
     * @access  private
     * @return  string class method (one of doGet, doPost, doHead)
     * @param   string method Request-Method
     * @see     http://www.webdav.org/
     */
    function _handleMethod($method) {
      $l= &Logger::getInstance();
      $c= &$l->getCategory();
      
      // Read input if we have a 
      if (
        (NULL !== ($len= $this->request->getHeader('Content-Length'))) &&
        (FALSE !== ($fd= fopen('php://input', 'r')))
      ) {
        $data= fread($fd, $len);
        $c->debug($method, $len, $data);
        fclose($fd);
      }
      
      // Select implementation
      $this->handlingImpl= NULL;
      foreach (array_keys($this->impl) as $pattern) {
        if (0 !== strpos($this->request->uri['path'], $pattern)) continue;
        
        $this->request->uri['path_root']= $pattern;
        $this->request->uri['path_translated']= (string)substr(
          $this->request->uri['path'], 
          strlen($pattern)
        );
        $this->handlingImpl= &$this->impl[$pattern];
        break;
      }
      
      // Implementation not found
      if (NULL === $this->handlingImpl) {
        trigger_error('No pattern match ['.implode(', ', array_keys($this->impl)).']', E_USER_NOTICE);
        return throw(new HttpScriptlet('Cannot handle requests to '.$request->uri['path']));
      }

      switch ($method) {
        case HTTP_METHOD_OPTIONS:
          $this->request->setData(getenv('QUERY_STRING'));
          $this->request->setParams(array_change_key_case($_REQUEST, CASE_LOWER));
          $this->_method= 'doOptions';
          break;
          
        case WEBDAV_METHOD_PROPFIND:
          $this->request->setData($data);
          $this->request->setParams(array_change_key_case($_REQUEST, CASE_LOWER));
          $this->_method= 'doPropFind';
          break;

        case WEBDAV_METHOD_PROPPATCH:
          $this->request->setData($data);
          $this->request->setParams(array_change_key_case($_REQUEST, CASE_LOWER));
          $this->_method= 'doPropPatch';
          break;
          
        // TBD: COPY MOVE OPTIONS...
          
        default:
          $this->_method= parent::_handleMethod($method);
      }
      
      return $this->_method;
    }
  }
?>
