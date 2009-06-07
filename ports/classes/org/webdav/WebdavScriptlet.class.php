<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'io.File',
    'peer.URL',
    'peer.http.BasicAuthorization',
    'scriptlet.HttpScriptlet',
    'org.webdav.util.WebdavBool',
    'org.webdav.WebdavScriptletRequest',
    'org.webdav.WebdavScriptletResponse',
    'org.webdav.xml.WebdavLockRequest',
    'org.webdav.xml.WebdavPropFindRequest',
    'org.webdav.xml.WebdavPropPatchRequest',
    'org.webdav.xml.WebdavPropResponse',
    'org.webdav.xml.WebdavPropPatchResponse',
    'org.webdav.xml.WebdavMultistatusResponse',
    'org.webdav.xml.WebdavLockResponse',
    'org.webdav.xml.WebdavReportResponse',
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
  define('WEBDAV_METHOD_REPORT',    'REPORT');
  define('WEBDAV_METHOD_VERSIONCONTROL', 'VERSION-CONTROL');
  
  
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
  define('WEBDAV_LOCK_NULL',    'lock-null');
  
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
   *   $s= new WebdavScriptlet(array(
   *    '/webdav/' => new DavFileImpl('/path/to/files/you/want/do/provide/')
   *   ));
   *   try {
   *     $s->init();
   *     $response= $s->process();
   *   } catch (HttpScriptletException $e) {
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
    public
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
        WEBDAV_METHOD_MOVE       => 'doMove',        
        WEBDAV_METHOD_REPORT     => 'doReport',
        WEBDAV_METHOD_VERSIONCONTROL => 'doVersionControl'
      ),
      $permissions  = array(),
      $impl         = array(),
      $handlingImpl = NULL,
      $auth         = NULL,
      $handlingAuth = NULL,
      $perm         = NULL;
      
    /**
     * Constructor
     *
     * @param   array impl (associative array of pathmatch => org.webdav.impl.DavImpl)
     */  
    public function __construct($impl) {

      // Make sure patterns are always with trailing /
      foreach (array_keys($impl) as $pattern) {
        $path= rtrim($pattern, '/').'/';
        if (isset($impl[$pattern]['impl'])) {
          $this->impl[$path]= $impl[$pattern]['impl'];
        } else {
          $this->impl[$path]= $impl[$pattern];
        }
        if (isset($impl[$pattern]['auth'])) {
          $this->auth[$path]= $impl[$pattern]['auth'];
        }
        if (isset($impl[$pattern]['backend'])) {
          $this->backend[$path]= $impl[$pattern]['backend'];
        }
        if (isset($impl[$pattern]['mapping'])) {
          $this->mapping[$path]= $impl[$pattern]['mapping'];
        }
        if (isset($impl[$pattern]['user'])) {
          $this->user[$path]= $impl[$pattern]['user'];
        }
      }
    }
    
    /**
     * Returns a Webdav request object depending on the REQUEST_METHOD
     *
     * @return org.webdav.WebdavScriptletRequest
     */
    protected function _request() {
      switch (getenv('REQUEST_METHOD')) {
        case WEBDAV_METHOD_PROPFIND:
          return new WebdavPropFindRequest();
        case WEBDAV_METHOD_PROPPATCH:
          return new WebdavPropPatchRequest();
        case WEBDAV_METHOD_LOCK:
          return new WebdavLockRequest();
        default:
          return new WebdavScriptletRequest();
      }
    }
    
    /**
     * Returns a Webdav response object depending on the REQUEST_METHOD
     *
     * @return org.webdav.WebdavResponse
     */
    protected function _response() {
      switch (getenv('REQUEST_METHOD')) {
        case WEBDAV_METHOD_PROPFIND:
          return new WebdavMultistatusResponse($this->map);
        case WEBDAV_METHOD_LOCK:
          return new WebdavLockResponse();
        case WEBDAV_METHOD_PROPPATCH:
          return new WebdavPropPatchResponse();
        case WEBDAV_METHOD_REPORT:
          return new WebdavReportResponse();
        default:
          return new WebdavScriptletResponse();
      }
    }

    /**
     * Handle OPTIONS
     *
     * @see     xp://scriptlet.scriptlet.HttpScriptlet#doGet
     * @return  bool processed
     * @param   scriptlet.HttpScriptletRequest request
     * @param   scriptlet.HttpScriptletResponse response
     * @throws  lang.XPException to indicate failure
     */
    public function doOptions($request, $response) {
      $response->setHeader('MS-Author-Via', 'DAV');         // MS-clients want this
      $response->setHeader('Allow', implode(', ', array_keys($this->methods)));
      $response->setHeader('DAV', '1,2,<http://apache.org/dav/propset/fs/1>');
    }

    /**
     * Handle DELETE
     *
     * @see     rfc://2518#8.6
     * @return  bool processed
     * @param   scriptlet.HttpScriptletRequest request
     * @param   scriptlet.HttpScriptletResponse response
     * @throws  lang.XPException to indicate failure
     */
    public function doDelete($request, $response) {
      try {
        $object= $this->handlingImpl->delete($request->getPath());
      } catch (ElementNotFoundException $e) {
        $response->setStatus(HTTP_NOT_FOUND);
        $response->setContent($e->toString());
        return FALSE;
      } catch (XPException $e) {
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
     * @return  bool processed
     * @param   scriptlet.HttpScriptletRequest request
     * @param   scriptlet.HttpScriptletResponse response
     * @throws  lang.XPException to indicate failure
     */
    public function doGet($request, $response) {
      try {
        $object= $this->handlingImpl->get($request->getPath());
      } catch (ElementNotFoundException $e) {
        $response->setStatus(HTTP_NOT_FOUND);
        return FALSE;
      } catch (OperationNotAllowedException $e) {
        $response->setStatus(HTTP_CONFLICT);
        return FALSE;
      } catch (IllegalArgumentException $e) {
        $response->setStatus(WEBDAV_LOCKED);       
        return FALSE;
      } catch (XPException $e) {      
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
     * @return  bool processed
     * @param   scriptlet.HttpScriptletRequest request
     * @param   scriptlet.HttpScriptletResponse response
     * @throws  lang.XPException to indicate failure
     */
    public function doPost($request, $response) {
      throw new MethodNotImplementedException($this->getName().'::post not implemented');
    }

    /**
     * Handle HEAD
     *
     * @see     rfc://2518#8.4
     * @return  bool processed
     * @param   scriptlet.HttpScriptletRequest request
     * @param   scriptlet.HttpScriptletResponse response
     * @throws  lang.XPException to indicate failure
     */
    public function doHead($request, $response) {
      try {
        $object= $this->handlingImpl->get($request->getPath());
      } catch (ElementNotFoundException $e) {
        $response->setStatus(HTTP_NOT_FOUND);
        $response->setContent($e->toString());
        return FALSE;
      } catch (XPException $e) {
        $response->setStatus(HTTP_CONFLICT);
        $response->setContent($e->toString());
        return FALSE;
      }
   
      $response->setHeader('ETag', $object->properties['getetag']->value);
      $response->setHeader('Content-type',   $object->contentType);
      $response->setHeader('Content-length', $object->contentLength);
      $response->setHeader('Last-modified',  $object->properties['getlastmodified']->toString());      
      $response->setStatus(HTTP_OK);
    }

    /**
     * Handle PUT
     *
     * @see     rfc://2518#8.7
     * @return  bool processed
     * @param   scriptlet.HttpScriptletRequest request
     * @param   scriptlet.HttpScriptletResponse response
     * @throws  lang.XPException to indicate failure
     */
    public function doPut($request, $response) {
      try {
        $created= $this->handlingImpl->put(
          $request->getPath(),
          $request->getData()
        );
      } catch (OperationFailedException $e) {
        $response->setStatus(HTTP_CONFLICT);
        $response->setContent($e->toString());
        return FALSE;
      } catch (OperationNotAllowedException $e) {
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
     * @return  bool processed
     * @param   scriptlet.HttpScriptletRequest request
     * @param   scriptlet.HttpScriptletResponse response
     * @throws  lang.XPException to indicate failure
     */
    public function doMkCol($request, $response) {
      try {
        $created= $this->handlingImpl->mkcol($request->getPath());
      } catch (OperationFailedException $e) {
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
     * @return  bool processed
     * @param   scriptlet.HttpScriptletRequest request
     * @param   scriptlet.HttpScriptletResponse response
     * @throws  lang.XPException to indicate failure
     */
    public function doMove($request, $response) {
      try {
        $created= $this->handlingImpl->move(
          $request->getPath(),
          $request->getRelativePath($request->getHeader('Destination')),
          WebdavBool::fromString($request->getHeader('Overwrite'))
        );
      } catch (OperationFailedException $e) {
        $response->setStatus(HTTP_CONFLICT);
        $response->setContent($e->toString());
        return FALSE;
      } catch (OperationNotAllowedException $e) {
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
     * @return  bool processed
     * @param   scriptlet.HttpScriptletRequest request
     * @param   scriptlet.HttpScriptletResponse response
     * @throws  lang.XPException to indicate failure
     */
    public function doCopy($request, $response) {
      try {
        $created= $this->handlingImpl->copy(
          $request->getPath(),
          $request->getRelativePath($request->getHeader('Destination')),
          WebdavBool::fromString(
            $request->getHeader('Overwrite') === NULL ?
            'f' :
            $request->getHeader('Overwrite')
          )
        );
      } catch (OperationFailedException $e) {
        $response->setStatus(HTTP_CONFLICT);
        $response->setContent($e->toString());
        return FALSE;
      } catch (OperationNotAllowedException $e) {
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
     * @return  bool processed
     * @param   scriptlet.HttpScriptletRequest request
     * @param   scriptlet.HttpScriptletResponse response
     * @throws  lang.XPException to indicate failure
     */
    public function doLock($request, $response) {
      try {
        $this->handlingImpl->lock(
          $request,
          $response
        );
      } catch (ElementNotFoundException $e) {
        $response->setStatus(HTTP_PRECONDITION_FAILED);
        $response->setContent($e->toString());        
        return FALSE; 
      } catch (XPException $e) {
        $response->setStatus(HTTP_LOCKED);
        $response->setContent($e->toString());
        return FALSE; 
      }
      return TRUE;
    }

    /**
     * Handle UNLOCK
     *
     * @see     rfc://2518#8.11
     * @return  bool processed
     * @param   &scriptlet.HttpScriptletRequest request
     * @param   &scriptlet.HttpScriptletResponse response
     * @throws  lang.XPException to indicate failure
     */
    public function doUnlock($request, $response) {
      try {
        $this->handlingImpl->unlock(
          $request,
          $response
        );
      } catch (ElementNotFoundException $e) {
        $response->setStatus(HTTP_NOT_FOUND);
        $response->setContent($e->toString());
        return FALSE; 
      } catch (OperationFailedException $e) {
        $response->setStatus(WEBDAV_PRECONDFAILED);
        $response->setContent($e->toString());
        return FALSE;
      } catch (XPException $e) {
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
     * @return  bool processed
     * @param   scriptlet.HttpScriptletRequest request
     * @param   scriptlet.HttpScriptletResponse response
     * @throws  lang.XPException to indicate failure
     */
    public function doPropFind($request, $response) {
      try {
        $this->handlingImpl->propfind(
          $request,
          $response
        );
      } catch (ElementNotFoundException $e) {
        $response->setStatus(HTTP_NOT_FOUND);
        $response->setContent($e->toString());
        return FALSE;
      } catch (OperationNotAllowedException $e) {
        $response->setStatus(HTTP_METHOD_NOT_ALLOWED); 
        $response->setContent($e->toString());
        return FALSE;
      } catch (FormatException $e) {

        // XML parse errors
        $response->setStatus(HTTP_BAD_REQUEST);
        $response->setContent($e->toString());
        return FALSE;
      } catch (XPException $e) {
        
        // Other exceptions - throw exception to indicate (complete) failure
        throw new HttpScriptletException($e->message);
      }
      
      return TRUE;
    }

    /**
     * Receives an PROPPATCH request from the <pre>process()</pre> method
     * and handles it.
     *
     * @see     rfc://2518#8.2
     * @return  bool processed
     * @param   &scriptlet.HttpScriptletRequest request
     * @param   &scriptlet.HttpScriptletResponse response
     * @throws  lang.XPException to indicate failure
     */
    public function doPropPatch($request, $response) {
      try {
        $this->handlingImpl->proppatch(
          $request,
          $response
        );
      } catch (ElementNotFoundException $e) {
        $response->setStatus(HTTP_NOT_FOUND);
        $response->setContent($e->toString());
        return FALSE;
      } catch (FormatException $e) {
      
        // XML parse errors
        $response->setStatus(HTTP_BAD_REQUEST);
        $response->setContent($e->toString());
        return FALSE;
      } catch (OperationFailedException $e) {
        $response->setStatus(HTTP_CONFLICT);
        $response->setContent($e->toString());
        return FALSE;
      } catch (OperationNotAllowedException $e) {
        $response->setStatus(HTTP_FORBIDDEN);
        $response->setContent($e->toString());
        return FALSE;

      } catch (XPException $e) {
        
        // Other exceptions - throw exception to indicate (complete) failure
        throw new HttpScriptletException($e->message);
      } 
      
      $rootURL= $request->getRootURL();
      $response->setHref($rootURL->getPath().$request->getPath());
      $response->setStatus(HTTP_CREATED);
    }
    
    /**
     * Do a Version-Control Request
     *
     * @param   scriptlet.HttpScriptletRequest request
     * @param   scriptlet.HttpScriptletResponse response  
     */
    public function doVersionControl($request, $response) {
      try {
        $this->handlingImpl->versionControl(
          $request->getPath(),
          new File($this->handlingImpl->base.$request->getPath())
        );
      } catch (ElementNotFoundException $e) {
        $response->setStatus(HTTP_NOT_FOUND);
        $response->setContent($e->toString());
        return FALSE;
      } catch (XPException $e) {        
        $response->setStatus(HTTP_BAD_REQUEST);
        $response->setContent($e->toString());
        return FALSE;
      } 
      $response->setStatus(HTTP_OK);
    }
    
    /**
     * Do a REPORT Request
     *
     * @param   &scriptlet.HttpScriptletRequest request
     * @param   &scriptlet.HttpScriptletResponse response  
     */
    public function doReport($request, $response) {
      try {
        $this->handlingImpl->report($request, $response);
      } catch (ElementNotFoundException $e) {
        $response->setStatus(HTTP_NOT_FOUND);
        $response->setContent($e->toString());
        return FALSE;
      } catch (XPException $e) {        
        $response->setStatus(HTTP_BAD_REQUEST);
        $response->setContent($e->toString());
        return FALSE;
      } 
      $response->setStatus(HTTP_OK);
    }


    /**
     * Errorhandler not-found impl
     *
     * @see     rfc://2518#8.2
     * @return  bool processed
     * @param   &scriptlet.HttpScriptletRequest request
     * @param   &scriptlet.HttpScriptletResponse response
     * @throws  lang.XPException to indicate failure
     */
    public function doNotFound($request, $response) {
      $response->setStatus(HTTP_NOT_FOUND);
      return FALSE;
    }
    
    /**
     * Called when a authorization is required
     *
     * @param   &scriptlet.HttpScriptletRequest request
     * @param   &scriptlet.HttpScriptletResponse response
     * @return  bool processed
     * @throws  lang.XPException to indicate failure
     */
    public function doAuthorizationRequest($request, $response) {
      $response->setStatus(HTTP_AUTHORIZATION_REQUIRED);
      $response->setHeader('WWW-Authenticate',  'Basic realm="WebDAV Authorization"');
      return TRUE;
    }
    
    /**
     * Called when user hasn't permissions to do something
     *
     * @param   &scriptlet.HttpScriptletRequest request
     * @param   &scriptlet.HttpScriptletResponse response
     * @return  bool processed
     * @throws  lang.XPException to indicate failure
     */
    public function doAuthorizationDeny($request, $response) {
      $response->setStatus(HTTP_FORBIDDEN);
      return TRUE;
    }
    
  
    /**
     * Handle methods
     *
     * @return  string class method (one of doGet, doPost, doHead)
     * @param   string method Request-Method
     * @see     rfc://2518#8 Description of methods
     */
    public function handleMethod($request) {

      // Check if we recognize this method
      if (!isset($this->methods[$request->method])) {
        throw new HttpScriptletException('Cannot handle method "'.$request->method.'"');
      }

      // Select implementation
      $this->handlingImpl= NULL;
      foreach (array_keys($this->impl) as $pattern) {
        if (0 !== strpos(rtrim($request->uri->getPath(), '/').'/', $pattern)) continue;
        
        // Set the root URL (e.g. http://wedav.host.com/dav/)
        $request->setRootURL($rootURL= new URL(sprintf(
          '%s://%s%s',
          $request->uri->getScheme(),
          $request->uri->Host(),
          $pattern
        )));
        
        // Set request path (e.g. /directory/file)
        $request->setPath($request->decodePath(substr(
          $request->uri->getPath(), 
          strlen($pattern)
        )));
        
        $this->handlingImpl= $this->impl[$pattern];
        
        // Set absolute Uri
        $request->setAbsoluteURI($this->handlingImpl->base.$request->getPath());
        break;
      }
      
      // Implementation not found
      if (NULL === $this->handlingImpl) {
        throw new HttpScriptletException('Cannot handle requests to '.$request->uri->getPath());
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
        $this->handlingAuth= $this->auth[$rootURL->getPath()];
        $auth= BasicAuthorization::fromValue($request->getHeader('Authorization'));
        
        // Can not get username/password from Authorization header
        if (!$auth) return 'doAuthorizationRequest';
        
        // Use an own User object if you want to save more than username and password
        if ($this->user[$rootURL->getPath()]) {          
          $c= XPClass::forName($this->user[$pattern]);
          with ($user= $c->newInstance()); {
            $user->setUsername($auth->getUser());
            $user->setUserPassword($auth->getPassword());            
          }
          $request->setUser($user); 
        } else {

          // Create a normal WebdavUser object
          $request->setUser(new WebdavUser($auth->getUser(), $auth->getPassword()));
        }

        // Check user
        if (!$this->handlingAuth->authorize($request->getUser())) return 'doAuthorizationRequest';

        // Check for permissions
        if (!$this->handlingAuth->isAuthorized(
          $this->handlingImpl->base.$request->getPath(),
          $request->getUser(),
          $request)
        ) {
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

      // Check for mapping
      if (($mapping =$this->mapping[$rootURL->getPath()]) !== NULL)
        $this->map= $mapping->init($request, $pattern); 
 
      return $this->methods[$request->method];
    }
 
  }
?>
