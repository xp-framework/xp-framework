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
  
  // HTTP methods for distributed authoring
  define('WEBDAV_METHOD_PROPFIND',  'PROPFIND');
  define('WEBDAV_METHOD_PROPPATCH', 'PROPPATCH');
  define('WEBDAV_METHOD_MKCOL',     'MKCOL');
  define('WEBDAV_METHOD_LOCK',      'LOCK');
  define('WEBDAV_METHOD_UNLOCK',    'UNLOCK');
  define('WEBDAV_METHOD_COPY',      'COPY');
  define('WEBDAV_METHOD_MOVE',      'MOVE');
  
  // Status code extensions to http/1.1 
  define('WEBDAV_PROCESSING',       102);
  define('WEBDAV_MULTISTATUS',      207);
  define('WEBDAV_UNPROCESSABLE',    422);
  define('WEBDAV_LOCKED',           423);
  define('WEBDAV_FAILEDDEPENDENCY', 424);
  define('WEBDAV_INSUFFICIENTSTOR', 507);
  
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
   * @see      http://sitten-polizei.de/php/webdav.patch Patch against current HEAD
   * @see      http://www.webdav.org/ WebDAV Resources
   * @see      http://www.webdav.org/other/faq.html DAV FAQ
   * @see      http://www.webdav.org/cadaver/ Command-line tool (*nix)
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
      $response->setHeader('MS-Author-Via', 'DAV');         // MS-clients want this
      $response->setHeader('Allow', implode(', ', array(
        HTTP_METHOD_GET,
        HTTP_METHOD_POST,
        HTTP_METHOD_HEAD,
        HTTP_METHOD_OPTIONS,
        HTTP_METHOD_PUT,
        HTTP_METHOD_DELETE,
        WEBDAV_METHOD_PROPFIND,
        WEBDAV_METHOD_PROPPATCH,
        WEBDAV_METHOD_MKCOL,
        WEBDAV_METHOD_LOCK,
        WEBDAV_METHOD_UNLOCK,
        WEBDAV_METHOD_COPY,
        WEBDAV_METHOD_MOVE
      )));
      $response->setHeader('DAV', '1,2,<http://apache.org/dav/propset/fs/1>');
    }

    /**
     * Handle DELETE
     *
     * @see     xp://org.apache.scriptlet.HttpScriptlet#doGet
     * @access  private
     * @return  bool processed
     * @public  request org.apache.HttpScriptletRequest
     * @access  response org.apache.HttpScriptletResponse
     * @throws  Exception to indicate failure
     */
    function doDelete(&$request, &$response) {
      try(); {
        $object= &$this->handlingImpl->delete($request->uri['path_translated']);
      } if (catch('ElementNotFoundException', $e)) {
      
        // Element not found
        $response->setStatus(HTTP_NOT_FOUND);
        $response->setContent($e->getStackTrace());
        return FALSE;
      } if (catch('Exception', $e)) {
      
        // Not allowd
        $response->setStatus(HTTP_METHOD_NOT_ALLOWED);
        $response->setContent($e->getStackTrace());
        return FALSE;
      } 
      
      $response->setStatus(HTTP_NO_CONTENT);
    }

    /**
     * Handle GET
     *
     * @see     xp://org.apache.scriptlet.HttpScriptlet#doGet
     * @access  private
     * @return  bool processed
     * @public  request org.apache.HttpScriptletRequest
     * @access  response org.apache.HttpScriptletResponse
     * @throws  Exception to indicate failure
     */
    function doGet(&$request, &$response) {
      try(); {
        $object= &$this->handlingImpl->get($request->uri['path_translated']);
      } if (catch('ElementNotFoundException', $e)) {
      
        // Element not found
        $response->setStatus(HTTP_NOT_FOUND);
        $response->setContent($e->getStackTrace());
        return FALSE;
      } if (catch('Exception', $e)) {
      
        // Conflict
        $response->setStatus(HTTP_CONFLICT);
        $response->setContent($e->getStackTrace());
        return FALSE;
      } 
      
      $response->setStatus(HTTP_OK);
      $response->setHeader('Content-type',   $object->contentType);
      $response->setHeader('Content-length', $object->contentLength);
      $response->setHeader('Last-modified',  $object->lastModified->toString('D, j M Y H:m:s \G\M\T'));
      $response->setContent($object->getData());
    }

    /**
     * Handle POST
     *
     * @see     xp://org.apache.scriptlet.HttpScriptlet#doGet
     * @access  private
     * @return  bool processed
     * @public  request org.apache.HttpScriptletRequest
     * @access  response org.apache.HttpScriptletResponse
     * @throws  Exception to indicate failure
     */
    function doPost(&$request, &$response) {
    }

    /**
     * Handle HEAD
     *
     * @see     xp://org.apache.scriptlet.HttpScriptlet#doGet
     * @access  private
     * @return  bool processed
     * @public  request org.apache.HttpScriptletRequest
     * @access  response org.apache.HttpScriptletResponse
     * @throws  Exception to indicate failure
     */
    function doHead(&$request, &$response) {
    }

    /**
     * Handle PUT
     *
     * @see     xp://org.apache.scriptlet.HttpScriptlet#doGet
     * @access  private
     * @return  bool processed
     * @public  request org.apache.HttpScriptletRequest
     * @access  response org.apache.HttpScriptletResponse
     * @throws  Exception to indicate failure
     */
    function doPut(&$request, &$response) {
      try(); {
        $created= $this->handlingImpl->put(
          $request->uri['path_translated'],
          $request->getData()
        );
      } if (catch('Exception', $e)) {
      
        // Conflict
        $response->setStatus(HTTP_CONFLICT);
        $response->setContent($e->getStackTrace());
        return FALSE;
      } 
      
      $response->setStatus($new ? HTTP_CREATED : HTTP_NO_CONTENT);
    }

    /**
     * Handle MKCOL
     *
     * @see     xp://org.apache.scriptlet.HttpScriptlet#doGet
     * @access  private
     * @return  bool processed
     * @public  request org.apache.HttpScriptletRequest
     * @access  response org.apache.HttpScriptletResponse
     * @throws  Exception to indicate failure
     */
    function doMkCol(&$request, &$response) {
    }

    /**
     * Handle MOVE
     *
     * @see     xp://org.apache.scriptlet.HttpScriptlet#doGet
     * @access  private
     * @return  bool processed
     * @public  request org.apache.HttpScriptletRequest
     * @access  response org.apache.HttpScriptletResponse
     * @throws  Exception to indicate failure
     */
    function doMove(&$request, &$response) {
    }

    /**
     * Handle COPY
     *
     * @see     xp://org.apache.scriptlet.HttpScriptlet#doGet
     * @access  private
     * @return  bool processed
     * @public  request org.apache.HttpScriptletRequest
     * @access  response org.apache.HttpScriptletResponse
     * @throws  Exception to indicate failure
     */
    function doCopy(&$request, &$response) {
    }

    /**
     * Handle LOCK
     *
     * @see     xp://org.apache.scriptlet.HttpScriptlet#doGet
     * @access  private
     * @return  bool processed
     * @public  request org.apache.HttpScriptletRequest
     * @access  response org.apache.HttpScriptletResponse
     * @throws  Exception to indicate failure
     */
    function doLock(&$request, &$response) {
    }

    /**
     * Handle UNLOCK
     *
     * @see     xp://org.apache.scriptlet.HttpScriptlet#doGet
     * @access  private
     * @return  bool processed
     * @public  request org.apache.HttpScriptletRequest
     * @access  response org.apache.HttpScriptletResponse
     * @throws  Exception to indicate failure
     */
    function doUnlock(&$request, &$response) {
    }
    
    /**
     * Receives an PROPFIND request from the <pre>process()</pre> method
     * and handles it.
     *
     * <pre>
     * All XML used in either requests or responses MUST be, at minimum, well 
     * formed.  If a server receives ill-formed XML in a request it MUST reject 
     * the entire request with a 400 (Bad Request).
     * </pre>
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
      } if (catch('ElementNotFoundException', $e)) {
      
        // Element not found
        $response->setStatus(HTTP_NOT_FOUND);
        $response->setContent($e->getStackTrace());
        return FALSE;
      } if (catch('FormatException', $e)) {
      
        // XML parse errors
        $response->setStatus(HTTP_BAD_REQUEST);
        $response->setContent($e->getStackTrace());
        return FALSE;
      } if (catch('Exception', $e)) {
      
        // Other exceptions - throw exception to indicate (complete) failure
        return throw(new HttpScriptletException($e->message));
      }
      
      // Send "HTTP/1.1 207 Multi-Status" response header
      $response->setStatus(WEBDAV_MULTISTATUS);
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
     * @see     rfc://2518#8 Description of methods
     */
    function _handleMethod($method) {
      static $methods= array(
        HTTP_METHOD_GET           => 'doGet',
        HTTP_METHOD_POST          => 'doPost',
        HTTP_METHOD_HEAD          => 'doHead',
        HTTP_METHOD_OPTIONS       => 'doOptions',
        HTTP_METHOD_PUT           => 'doPut',
        HTTP_METHOD_DELETE        => 'doDelete',
        WEBDAV_METHOD_PROPFIND    => 'doPropFind',
        WEBDAV_METHOD_PROPPATCH   => 'doPropPatch',
        WEBDAV_METHOD_MKCOL       => 'doMkCol',
        WEBDAV_METHOD_LOCK        => 'doLock',
        WEBDAV_METHOD_UNLOCK      => 'doUnlock',
        WEBDAV_METHOD_COPY        => 'doCopy',
        WEBDAV_METHOD_MOVE        => 'doMove'
      );
            
      $l= &Logger::getInstance();
      $c= &$l->getCategory();
      
      // Read input if we have a Content-length header,
      // else get data from QUERY_STRING
      if (
        (NULL !== ($len= $this->request->getHeader('Content-length'))) &&
        (FALSE !== ($fd= fopen('php://input', 'r')))
      ) {
        $data= fread($fd, $len);
        $c->debug($method, $len, $data);
        fclose($fd);
        
        $this->request->setData($data);
      } else {
        $this->request->setData(getenv('QUERY_STRING'));
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

      // Check if we recognize this method
      if (isset($methods[$method])) {
        $this->_method= $methods[$method];
        return $this->_method;  
      }
      
      return throw(new HttpScriptlet('Cannot handle method "'.$method.'"'));
    }
  }
?>
