<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'peer.URL',
    'peer.http.BasicAuthorization',
    'scriptlet.HttpScriptlet',
    'org.webdav.util.WebdavBool',
    'org.webdav.WebdavScriptletRequest',
    'org.webdav.WebdavScriptletResponse',
    'org.webdav.xml.WebdavPropFindRequest',
    'org.webdav.xml.WebdavPropPatchRequest',
    'org.webdav.xml.WebdavPropResponse',
    'org.webdav.xml.WebdavMultistatusResponse',
    'org.webdav.xml.WebdavLockResponse',
    'org.webdav.auth.WebdavUser'
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
  define('WEBDAV_PRECONDFAILED',    HTTP_PRECONDITION_FAILED);

  // Clienttypes 
  define ('WEBDAV_CLIENT_UNKNOWN', 0x0000);
  define ('WEBDAV_CLIENT_MS',      0x2000);
  define ('WEBDAV_CLIENT_NAUT',    0x4000);
  define ('WEBDAV_CLIENT_DAVFS',   0x8000);
  
  define('WEBDAV_COLLECTION',   'collection');
  
  /**
   * <quote>
   * WebDAV is an extension to the HTTP/1.1 protocol that
   * allows clients to perform remote web content authoring operations.
   * This extension provides a coherent set of methods, headers, request
   * entity body formats, and response entity body formats that provide
   * operations for:
   * 
   * Properties: The ability to create, remove, and query information
   * about Web pages, such as their authors, creation dates, etc. Also,
   * the ability to link pages of any media type to related pages.
   * 
   * Collections: The ability to create sets of documents and to retrieve
   * a hierarchical membership listing (like a directory listing in a file
   * system).
   * 
   * Locking: The ability to keep more than one person from working on a
   * document at the same time. This prevents the "lost update problem,"
   * in which modifications are lost as first one author then another
   * writes changes without merging the other author's changes.
   * 
   * Namespace Operations: The ability to instruct the server to copy and
   * move Web resources.
   * </quote>
   *
   * <code>
   *   $s= &new WebdavScriptlet(array(
   *    '/webdav/' => new DavFileImpl('/path/to/files/you/want/do/provide/')
   *   ));
   *   try(); {
   *     $s->init();
   *     $response= &$s->process();
   *   } if (catch('HttpScriptletException', $e)) {
   *     // Retrieve standard "Internal Server Error"-Document
   *     $response= &$e->getResponse(); 
   *   }
   *   
   *   $response->sendHeaders();
   *   $response->sendContent();
   *   $s->finalize();  
   * </code>
   *
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
      $methods= array(
        HTTP_GET                 => 'doGet',
        HTTP_POST                => 'doPost',
        HTTP_HEAD                => 'doHead',
        HTTP_OPTIONS             => 'doOptions',
        HTTP_PUT                 => 'doPut',
        HTTP_DELETE              => 'doDelete',
        WEBDAV_METHOD_PROPFIND   => 'doPropFind',
        WEBDAV_METHOD_PROPPATCH  => 'doPropPatch',
        WEBDAV_METHOD_MKCOL      => 'doMkCol',
        WEBDAV_METHOD_LOCK       => 'doLock',
        WEBDAV_METHOD_UNLOCK     => 'doUnlock',
        WEBDAV_METHOD_COPY       => 'doCopy',
        WEBDAV_METHOD_MOVE       => 'doMove'
      ),
      $permissions  = array(),
      $impl         = array(),
      $handlingImpl = NULL,
      $auth         = NULL,
      $handlingAuth = NULL;
      
    /**
     * Constructor
     *
     * @access  public
     * @param   array impl (associative array of pathmatch => org.webdav.impl.DavImpl)
     */  
    function __construct($impl) {

      // Make sure patterns are always with trailing /
      foreach (array_keys($impl) as $pattern) {
        $path= rtrim($pattern, '/').'/';
        if (isset($impl[$pattern]['impl']))
          $this->impl[$path]= &$impl[$pattern]['impl'];
        else
          $this->impl[$path]= &$impl[$pattern];
        if (isset($impl[$pattern]['auth'])) {
          $this->auth[$path]= &$impl[$pattern]['auth'];
        }
      }
    }
    
    /**
     * Returns a Webdav request object depending on the REQUEST_METHOD
     *
     * @access private
     * @return org.webdav.WebdavScriptletRequest
     */
    function &_request() {
      switch (getenv('REQUEST_METHOD')) {
        case 'PROPFIND':
          return new WebdavPropFindRequest();
        case 'PROPPATCH':
          return new WebdavPropPatchRequest();
        case 'LOCK':
          return new WebdavLockRequest();
        default:
          return new WebdavScriptletRequest();
      }
    }
    
    /**
     * Returns a Webdav response object depending on the REQUEST_METHOD
     *
     * @access private
     * @return org.webdav.WebdavResponse
     */
    function &_response() {
      switch(getenv('REQUEST_METHOD')) {
        case 'PROPFIND':
          return new WebdavMultistatusResponse();
        case 'LOCK':
          return new WebdavLockResponse();
        case 'PROPPATCH':
        default:
          return new WebdavScriptletResponse();
      }
    }
    
    /**
     * Handle OPTIONS
     *
     * @see     xp://scriptlet.scriptlet.HttpScriptlet#doGet
     * @access  private
     * @return  bool processed
     * @param   &scriptlet.HttpScriptletRequest request
     * @param   &scriptlet.HttpScriptletResponse response
     * @throws  Exception to indicate failure
     */
    function doOptions(&$request, &$response) {
      $response->setHeader('MS-Author-Via', 'DAV');         // MS-clients want this
      $response->setHeader('Allow', implode(', ', array_keys($this->methods)));
      $response->setHeader('DAV', '1,2,<http://apache.org/dav/propset/fs/1>');
    }

    /**
     * Handle DELETE
     *
     * @see     rfc://2518#8.6
     * @access  private
     * @return  bool processed
     * @param   &scriptlet.HttpScriptletRequest request
     * @param   &scriptlet.HttpScriptletResponse response
     * @throws  Exception to indicate failure
     */
    function doDelete(&$request, &$response) {
      try(); {
        $object= &$this->handlingImpl->delete($request->getPath());
      } if (catch('ElementNotFoundException', $e)) {
      
        // Element not found
        $response->setStatus(HTTP_NOT_FOUND);
        $response->setContent($e->toString());
        return FALSE;
      } if (catch('Exception', $e)) {
      
        // Not allowd
        $response->setStatus(HTTP_METHOD_NOT_ALLOWED);
        $response->setContent($e->toString());
        return FALSE;
      } 
      
      $response->setStatus(HTTP_NO_CONTENT);
    }

    /**
     * Handle GET
     *
     * @see     rfc://2518#8.4
     * @access  private
     * @return  bool processed
     * @param   &scriptlet.HttpScriptletRequest request
     * @param   &scriptlet.HttpScriptletResponse response
     * @throws  Exception to indicate failure
     */
    function doGet(&$request, &$response) {
      try(); {
        $object= &$this->handlingImpl->get($request->getPath());
      } if (catch('ElementNotFoundException', $e)) {

        // Element not found
         $response->setStatus(HTTP_NOT_FOUND);
         return FALSE;
      } if (catch('OperationNotAllowedException', $e)) {
      
        // Conflict
        $response->setStatus(HTTP_CONFLICT);
        return FALSE;
      } if (catch('IllegalArgumentException', $e)) {
      
        // Conflict       
        $response->setStatus(WEBDAV_LOCKED);       
        return FALSE;
      } if (catch('Exception', $e)) {      
      
        // Conflict        
        $response->setStatus(HTTP_CONFLICT);        
        return FALSE;
      } 
      
      $modified_date= $object->getModifiedDate();
      $response->setStatus(HTTP_OK);
      $response->setHeader('Content-type',   $object->getContentType());
      $response->setHeader('Content-length', $object->getContentLength());
      $response->setHeader('Last-modified',  $modified_date->toString('D, j M Y H:m:s \G\M\T'));
      $response->setContent($object->getData());
    }

    /**
     * Handle POST
     *
     * @see     rfc://2518#8.5
     * @access  private
     * @return  bool processed
     * @param   &scriptlet.HttpScriptletRequest request
     * @param   &scriptlet.HttpScriptletResponse response
     * @throws  Exception to indicate failure
     */
    function doPost(&$request, &$response) {
      return throw(new MethodNotImplementedException($this->getName().'::post not implemented'));
    }

    /**
     * Handle HEAD
     *
     * @see     rfc://2518#8.4
     * @access  private
     * @return  bool processed
     * @param   &scriptlet.HttpScriptletRequest request
     * @param   &scriptlet.HttpScriptletResponse response
     * @throws  Exception to indicate failure
     */
    function doHead(&$request, &$response) {
      try(); {
        $object= &$this->handlingImpl->get($request->getPath());
      } if (catch('ElementNotFoundException', $e)) {
      
        // Element not found
        $response->setStatus(HTTP_NOT_FOUND);
        $response->setContent($e->toString());
        return FALSE;
      } if (catch('Exception', $e)) {

        // Conflict
        $response->setStatus(HTTP_CONFLICT);
        $response->setContent($e->toString());
        return FALSE;
      } 
      
      $response->setStatus(HTTP_OK);
      $response->setHeader('Content-type',   $object->contentType);
      $response->setHeader('Content-length', $object->contentLength);
      $response->setHeader('Last-modified',  $object->lastModified->toString('D, j M Y H:m:s \G\M\T'));
    }

    /**
     * Handle PUT
     *
     * @see     rfc://2518#8.7
     * @access  private
     * @return  bool processed
     * @param   &scriptlet.HttpScriptletRequest request
     * @param   &scriptlet.HttpScriptletResponse response
     * @throws  Exception to indicate failure
     */
    function doPut(&$request, &$response) {
      try(); {
        $created= $this->handlingImpl->put(
          $request->getPath(),
          $request->getData()
        );
      } if (catch('OperationFailedException', $e)) {
      
        // Conflict
        $response->setStatus(HTTP_CONFLICT);
        $response->setContent($e->toString());
        return FALSE;
      } if (catch('OperationNotAllowedException', $e)) {
      
        // Not allowed
        $response->setStatus(HTTP_METHOD_NOT_ALLOWED);
        $response->setContent($e->toString());
        return FALSE;
      }
      
      $response->setStatus($created ? HTTP_CREATED : HTTP_NO_CONTENT);
    }

    /**
     * <quote>
     * The MKCOL method is used to create a new collection. All DAV
     * compliant resources MUST support the MKCOL method.
     * </quote>
     *
     * @see     rfc://2518#8.3
     * @access  private
     * @return  bool processed
     * @param   &scriptlet.HttpScriptletRequest request
     * @param   &scriptlet.HttpScriptletResponse response
     * @throws  Exception to indicate failure
     */
    function doMkCol(&$request, &$response) {
      try(); {
        $created= $this->handlingImpl->mkcol($request->getPath());
      } if (catch('OperationFailedException', $e)) {
      
        // Conflict
        $response->setStatus(HTTP_CONFLICT);
        $response->setContent($e->toString());
        return FALSE;
      } 
      $response->setStatus(HTTP_CREATED);
      return TRUE;
    }
    
    /**
     * Handle MOVE
     *
     * @see     rfc://2518#8.9
     * @access  private
     * @return  bool processed
     * @param   &scriptlet.HttpScriptletRequest request
     * @param   &scriptlet.HttpScriptletResponse response
     * @throws  Exception to indicate failure
     */
    function doMove(&$request, &$response) {
      try(); {
        $created= $this->handlingImpl->move(
          $request->getPath(),
          $request->getRelativePath($request->getHeader('Destination')),
          WebdavBool::fromString(
            $request->getHeader('Overwrite') === NULL ?
            'f' :
            $request->getHeader('Overwrite')
          )
        );
      } if (catch('OperationFailedException', $e)) {
      
        // Conflict
        $response->setStatus(HTTP_CONFLICT);
        $response->setContent($e->toString());
        return FALSE;
      } if (catch('OperationNotAllowedException', $e)) {
      
        // Not allowed
        $response->setStatus(HTTP_METHOD_NOT_ALLOWED);
        $response->setContent($e->toString());
        return FALSE;
      }
      $response->setStatus($created ? HTTP_CREATED : HTTP_NO_CONTENT);
      return TRUE;
    }

    /**
     * Handle COPY
     *
     * @see     rfc://2518#8.8
     * @access  private
     * @return  bool processed
     * @param   &scriptlet.HttpScriptletRequest request
     * @param   &scriptlet.HttpScriptletResponse response
     * @throws  Exception to indicate failure
     */
    function doCopy(&$request, &$response) {
      try(); {
        $created= $this->handlingImpl->copy(
          $request->getPath(),
          $request->getRelativePath($request->getHeader('Destination')),
          WebdavBool::fromString(
            $request->getHeader('Overwrite') === NULL ?
            'f' :
            $request->getHeader('Overwrite')
          )
        );
      } if (catch('OperationFailedException', $e)) {
      
        // Conflict
        $response->setStatus(HTTP_CONFLICT);
        $response->setContent($e->toString());
        return FALSE;
      } if (catch('OperationNotAllowedException', $e)) {
      
        // Not allowed
        $response->setStatus(HTTP_METHOD_NOT_ALLOWED);
        $response->setContent($e->toString());
        return FALSE;
      }
      $response->setStatus($created ? HTTP_CREATED : HTTP_NO_CONTENT);
      return TRUE;
    }

    /**
     * <quote>
     * A LOCK method invocation creates the lock specified by the lockinfo
     * XML element on the Request-URI.
     * [...]
     * In order to indicate the lock token associated with a newly created
     * lock, a Lock-Token response header MUST be included in the response
     * for every successful LOCK request for a new lock.  Note that the
     * Lock-Token header would not be returned in the response for a
     * successful refresh LOCK request because a new lock was not created
     * </quote>
     *
     * @see     rfc://2518#8.10
     * @access  private
     * @return  bool processed
     * @param   &scriptlet.HttpScriptletRequest request
     * @param   &scriptlet.HttpScriptletResponse response
     * @throws  Exception to indicate failure
     */
    function doLock(&$request, &$response) {
      try(); {
        $this->handlingImpl->lock(
          $request,
          $response
        );
      } if (catch('ElementNotFoundException', $e)) {

        $response->setStatus(HTTP_NOT_FOUND);
        $response->setContent($e->toString());        
        return FALSE; 
      } if (catch('Exception', $e)) {

        $response->setStatus(WEBDAV_PRECONDFAILED);
        $response->setContent($e->toString());
        return FALSE; 
      }
      return TRUE;
    }

    /**
     * Handle UNLOCK
     *
     * @see     rfc://2518#8.11
     * @access  private
     * @return  bool processed
     * @public  request scriptlet.HttpScriptletRequest
     * @access  response scriptlet.HttpScriptletResponse
     * @throws  Exception to indicate failure
     */
    function doUnlock(&$request, &$response) {
      try(); {
        $this->handlingImpl->unlock(
          $request,
          $response
          );
          
      } if (catch('ElementNotFoundException', $e)) {
    
        $response->setStatus(HTTP_NOT_FOUND);
        $response->setContent($e->toString());
        return FALSE; 
      } if (catch('OperationFailedException', $e)) {
      
        $response->setStatus(WEBDAV_PRECONDFAILED);
        $response->setContent($e->toString());
        return FALSE;
      } if (catch('Exception', $e)) {

        $response->setStatus(HTTP_LOCKED);
        $response->setContent($e->toString());
        return FALSE; 
      }
      return TRUE; 
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
     * @see     rfc://2518#8.1
     * @access  private
     * @return  bool processed
     * @param   &scriptlet.HttpScriptletRequest request
     * @param   &scriptlet.HttpScriptletResponse response
     * @throws  Exception to indicate failure
     */
    function doPropFind(&$request, &$response) {
      try(); {
        $this->handlingImpl->propfind(
          $request,
          $response
        );
      } if (catch('ElementNotFoundException', $e)) {
      
        // Element not found
        $response->setStatus(HTTP_NOT_FOUND);
        $response->setContent($e->toString());
        return FALSE;
      } if (catch('OperationNotAllowedException', $e)) {

        $response->setStatus(HTTP_METHOD_NOT_ALLOWED); 
        $response->setContent($e->toString());
        return FALSE;
      } if (catch('FormatException', $e)) {

        // XML parse errors
        $response->setStatus(HTTP_BAD_REQUEST);
        $response->setContent($e->toString());
        return FALSE;
      } if (catch('Exception', $e)) {
        
        // Other exceptions - throw exception to indicate (complete) failure
        return throw(new HttpScriptletException($e->message));
      }
      return TRUE;
    }

    /**
     * Receives an PROPPATCH request from the <pre>process()</pre> method
     * and handles it.
     *
     * @see     rfc://2518#8.2
     * @access  private
     * @return  bool processed
     * @param   &scriptlet.HttpScriptletRequest request
     * @param   &scriptlet.HttpScriptletResponse response
     * @throws  Exception to indicate failure
     */
    function doPropPatch(&$request, &$response) {
      try(); {
        $this->handlingImpl->proppatch(
          $request,
          new WebdavMultiStatus()
        );
        
      } if (catch('ElementNotFoundException', $e)) {

        // Element not found
        $response->setStatus(HTTP_NOT_FOUND);
        $response->setContent($e->toString());
        return FALSE;
      } if (catch('FormatException', $e)) {
      
        // XML parse errors
        $response->setStatus(HTTP_BAD_REQUEST);
        $response->setContent($e->toString());
        return FALSE;
      } if (catch('OperationFailedException', $e)) {
      
        // Element not found
        $response->setStatus(HTTP_CONFLICT);
        $response->setContent($e->toString());
        return FALSE;
      } if (catch('OperationNotAllowedException', $e)) {

        // Forbidden
        $response->setStatus(HTTP_FORBIDDEN);
        $response->setContent($e->toString());
        return FALSE;

      } if (catch('Exception', $e)) {
        
        // Other exceptions - throw exception to indicate (complete) failure
        return throw(new HttpScriptletException($e->message));
      } 
      
      
      $response->setStatus(HTTP_CREATED);
    }


    /**
     * Errorhandler not-found impl
     *
     * @see     rfc://2518#8.2
     * @access  private
     * @return  bool processed
     * @param   &scriptlet.HttpScriptletRequest request
     * @param   &scriptlet.HttpScriptletResponse response
     * @throws  Exception to indicate failure
     */
    function doNotFound(&$request, &$response) {
      $response->setStatus(HTTP_NOT_FOUND);
      
      return FALSE;
    }
    
    /**
     * Called when a authorization is required
     *
     * @access  private
     * @param   &scriptlet.HttpScriptletRequest request
     * @param   &scriptlet.HttpScriptletResponse response
     * @return  bool processed
     * @throws  Exception to indicate failure
     */
    function doAuthorizationRequest(&$request, &$response) {
      $response->setStatus(HTTP_AUTHORIZATION_REQUIRED);
      $response->setHeader('WWW-Authenticate',  'Basic realm="WebDAV Authorization"');
      
      return TRUE;
    }
    
    /**
     * Called when user hasn't permissions to do something
     *
     * @access  private
     * @param   &scriptlet.HttpScriptletRequest request
     * @param   &scriptlet.HttpScriptletResponse response
     * @return  bool processed
     * @throws  Exception to indicate failure
     */
    function doAuthorizationDeny(&$request, &$response) {
      $response->setStatus(HTTP_FORBIDDEN);
      
      return TRUE;
    }
    
  
    /**
     * Handle methods
     *
     * @access  private
     * @return  string class method (one of doGet, doPost, doHead)
     * @param   string method Request-Method
     * @see     rfc://2518#8 Description of methods
     */
    function handleMethod(&$request) {
      // Check if we recognize this method
      if (!isset($this->methods[$request->method])) {
        return throw(new HttpScriptletException('Cannot handle method "'.$request->method.'"'));
      }
      
      // Select implementation
      $this->handlingImpl= NULL;
      foreach (array_keys($this->impl) as $pattern) {
        if (0 !== strpos(rtrim($request->uri['path'], '/').'/', $pattern)) continue;
        
        // Set the root URL (e.g. http://wedav.host.com/dav/)
        $request->setRootURL($rootURL= &new URL(sprintf(
          '%s://%s%s',
          $request->uri['scheme'],
          $request->uri['host'],
          $pattern
        )));
        
        // Set request path (e.g. /directory/file)
        $request->setPath($request->decodePath(substr(
          $request->uri['path'], 
          strlen($pattern)
        )));
        
        $this->handlingImpl= &$this->impl[$pattern];
        break;
      }
      
      // Implementation not found
      if (NULL === $this->handlingImpl) {
        return throw(new HttpScriptlet('Cannot handle requests to '.$request->uri['path']));
      }

      // determine Useragent
      $client= $request->getHeader('user-agent');

      switch (substr($client, 0, 3)) {
        case 'Mic':
          $this->useragent= WEBDAV_CLIENT_MS;
          break;
          
        case 'gno':
          $this->useragent= WEBDAV_CLIENT_NAUT;
          break;
          
        default:
          $this->useragent= WEBDAV_CLIENT_UNKNOWN;
      }
      
      // Check for authorization handler
      if (isset($this->auth[$rootURL->getPath()])) {
        $this->handlingAuth= &$this->auth[$rootURL->getPath()];
        $auth= &BasicAuthorization::fromValue($request->getHeader('Authorization'));
        
        // Can not get username/password from Authorization header
        if (!$auth) return 'doAuthorizationRequest';
        $request->setUser(new WebdavUser($auth->getUser(), $auth->getPassword()));
        
        // Check user
        if (!$this->handlingAuth->authorize($request->getUser())) return 'doAuthorizationRequest';
        
        // Check for permissions
        if (!$this->handlingAuth->isAuthorized($request)) {
          return 'doAuthorizationDeny';
        }
      }
      
      // Read input if we have a Content-length header,
      // else get data from QUERY_STRING
      if (
        (NULL !== ($len= $request->getHeader('Content-length'))) &&
        (FALSE !== ($fd= fopen('php://input', 'r')))
      ) {
        $data= fread($fd, $len);
        fclose($fd);
        
        $request->setData($data);
      } else {
        $request->setData(getenv('QUERY_STRING'));
      }
      
      return $this->methods[$request->method];
    }
  }
?>
