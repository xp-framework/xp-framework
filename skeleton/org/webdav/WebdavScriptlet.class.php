<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'scriptlet.HttpScriptlet',
    'org.webdav.util.WebdavBool',
    'org.webdav.xml.WebdavPropFindRequest',
    'org.webdav.xml.WebdavPropPatchRequest',
    'org.webdav.xml.WebdavMultistatus',
    'org.webdav.xml.WebdavLockRequest'
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
  
  // Cienttypes 
  define ('WEBDAV_CLIENT_UNKNOWN', 0x0000);
  define ('WEBDAV_CLIENT_MS',      0x2000);
  define ('WEBDAV_CLIENT_NAUT',    0x4000);
  define ('WEBDAV_CLIENT_DAVFS',   0x8000);


  
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
   *     // Retreive standard "Internal Server Error"-Document
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
      $impl         = array(),
      $useragent    = 0,
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
     * Private helper function
     *
     * @access  private
     * @param   string s
     * @return  string
     */
    function _relativeTarget($str) {
      $p= parse_url($str);
      return substr( $p['path'] , strlen($this->request->uri['path_root']));
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
      
      $response->setHeader('Content-type', 'text/plain');
      $response->setHeader('Content-length', '0');            
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
        $object= &$this->handlingImpl->delete($request->uri['path_translated']);
      } if (catch('ElementNotFoundException', $e)) {
      
        // Element not found        
        $response->setStatus(HTTP_NOT_FOUND);
        $response->setContent($e->String());
        return FALSE;
      } if (catch('Exception', $e)) {
      
        // Not allowed
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
        $object= &$this->handlingImpl->get(
          $request->uri['path_translated'],
          $request->getHeader('token')
        );
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
      } 

      if (catch('Exception', $e)) {      
        // Conflict        
        $response->setStatus(HTTP_CONFLICT);        
        return FALSE;
      } 
            
      $response->setStatus(HTTP_OK);
      $response->setHeader('Content-type',   $object->contentType. ($object->contentEncoding?';  charset="'.$object->contentEncoding:''));
      $response->setHeader('Content-type',   $object->contentType);
      $response->setHeader('Content-length', $object->contentLength);
      $response->setHeader('Last-modified',  $object->modifiedDate->toString('D, j M Y H:m:s \G\M\T'));
      $response->setContent($object->getData());
    return;
    }

    /**
     * Handle POST
     *
     * @see     rfc://2518#8.5
     * @access  private
     * @return  MethodNotImplementedException
     * @param   &scriptlet.HttpScriptletRequest request
     * @param   &scriptlet.HttpScriptletResponse response
     * @throws  MethodNotImplementedException
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
     */
    function doHead(&$request, &$response) {
      try(); {
        $object= &$this->handlingImpl->get($request->uri['path_translated']);
      } if (catch('ElementNotFoundException', $e)) {
 
        // Element not found
        $response->setStatus(HTTP_NOT_FOUND);
        $response->setContent('');
        return FALSE;
      } if (catch('Exception', $e)) {
        
        // Conflict
        $response->setStatus(HTTP_CONFLICT);
        $response->setContent('');
        return FALSE;
      } 
      
      $response->setStatus(HTTP_OK);
      $response->setHeader('Content-type',   $object->contentType);
      $response->setHeader('Content-length', $object->contentLength);
      $response->setHeader('Last-modified',  $object->modifiedDate->toString('D, j M Y H:m:s \G\M\T'));
      $response->setContent('');
    return;
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
        $created= $this->handlingImpl->put(&$request);        
      } if (catch('OperationFailedException', $e)) {
      
        // Conflict        
        $response->setStatus(HTTP_METHOD_NOT_ALLOWED);
        $response->setContent('');
        return;
      } if (catch('OperationNotAllowedException', $e)) {

        // Not allowed
        $response->setStatus(HTTP_METHOD_NOT_ALLOWED);
        $response->setContent('');
        return FALSE;
      } if (catch('Exception', $e)) {

        // Not allowed
        $response->setStatus(HTTP_NOT_ACCEPTABLE); // generic error
        $response->setContent('');
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
        $created= $this->handlingImpl->mkcol($request->uri['path_translated']);
      } if (catch('OperationFailedException', $e)) {
      
        // Conflict
        $response->setStatus(HTTP_CONFLICT);
        $response->setContent($e->toString());
        return FALSE;
      } 
      
      $response->setStatus(HTTP_CREATED);
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
          $request->uri['path_translated'],
          $this->_relativeTarget($request->getHeader('Destination')),
          WebdavBool::fromString($request->getHeader('Overwrite'))
        );
      } if (catch('OperationFailedException', $e)) {
      
        // Conflict
        $response->setStatus(HTTP_CONFLICT);
        $response->setContent('');
        return FALSE;
      } if (catch('OperationNotAllowedException', $e)) {
      
        // Not allowed
        $response->setStatus(HTTP_METHOD_NOT_ALLOWED);
        $response->setContent('');
        return FALSE;
      } if (catch('Exception', $e)) {
        
        // Something went wrong
        $response->setStatus(HTTP_CONFLICT);
        $response->setContent('');
        return FALSE;
      }
      
      $response->setStatus($created ? HTTP_CREATED : HTTP_NO_CONTENT);
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
          $request->uri['path_translated'],
          $this->_relativeTarget($request->getHeader('Destination')),
          WebdavBool::fromString($request->getHeader('Overwrite'))
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
        new WebdavLockRequest($request),
        $response
        );
      } if (catch('ElementNotFoundException', $e)) {

        $response->setStatus(HTTP_NOT_FOUND);
        $response->setContent('');        
        return FALSE; 
      } if (catch('Exception', $e)) {

        $response->setStatus(WEBDAV_PRECONDFAILED);
        $response->setContent('');
        return FALSE; 
      }
      
      $response->setHeader('Content-Type','application/xml; charset="utf-8"');
      $response->setStatus(HTTP_OK);
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
      }  if (catch('ElementNotFoundException', $e)) {
    
        $response->setStatus(HTTP_NOT_FOUND);
        $response->setContent('');
        return FALSE; 
      } if (catch('Exception', $e)) {
    
        $response->setStatus(WEBDAV_PRECONDFAILED);
        $response->setContent('');
        return FALSE; 
      }
      
      $response->setStatus(HTTP_OK);
      $response->setContent('');    
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
        $multistatus= &new WebdavMultistatus();
        $this->handlingImpl->propfind(
          new WebdavPropFindRequest($request),
          $multistatus
        );
          
      } if (catch('ElementNotFoundException', $e)) {

        // Element not found
        $response->setStatus(HTTP_NOT_FOUND);
        $response->setHeader('Content-length', 0);
        $response->setContent('');
        return FALSE;
      } if (catch('OperationNotAllowedException', $e)) {

        $response->setStatus(HTTP_METHOD_NOT_ALLOWED); 
        $response->setContent('');
        return FALSE;
      } if (catch('FormatException', $e)) {

        // XML parse errors
        $response->setStatus(HTTP_BAD_REQUEST); 
        $response->setContent('');
        return FALSE;
      } if (catch('Exception', $e)) {

        // Other exceptions - throw exception to indicate (complete) failure
        $response->setStatus(HTTP_NOT_FOUND);
        $response->setContent('');
        return throw(new HttpScriptletException($e->message));
      } 
      
      // Send "HTTP/1.1 207 Multi-Status" response header
      $response->setStatus(WEBDAV_MULTISTATUS);
      $response->setHeader(
        'Content-Type', 
        'application/xml; charset="'.$multistatus->getEncoding().'"'
        );

      $src= &$multistatus->getSource(FALSE);
      $response->setContent(
        $multistatus->getDeclaration()."\n".$src
        );
      
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
          new WebdavPropPatchRequest(&$request),
          new WebdavMultistatus()
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
      } if (catch('Exception', $e)) {
      
        // Other exceptions - throw exception to indicate (complete) failure
        return throw(new HttpScriptletException($e->message));
      }
      
      $src= &$multistatus->getSource(FALSE);
      $src= preg_replace("/>([\s\t\n\r]+)</is",'><', $src);
      $response->setContent(
        $multistatus->getDeclaration()."\n".$src
      );
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
        WEBDAV_METHOD_MOVE        => 'doMove',
        'ERROR_URI'               => 'doNotFound'
      );
            
      // Read input if we have a Content-length header,
      // else get data from QUERY_STRING
      if (
        (NULL !== ($len= $this->request->getHeader('Content-length'))) &&
        (FALSE !== ($fd= fopen('php://input', 'r')))
      ) {
        $data= fread($fd, $len);
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
        return throw(new HttpScriptletException('Cannot handle requests to '.$request->uri['path']));
      }

      // determine Useragent
      $client= $this->request->getHeader('user-agent');

      switch (substr($client,0,3)){
        case 'Mic':
          $this->useragent= WEBDAV_CLIENT_MS;
          break;
          
        case 'gno':
          $this->useragent= WEBDAV_CLIENT_NAUT;
          break;
          
        default:
          $this->useragent= WEBDAV_CLIENT_UNKNOWN;
        }

      // Check if we recognize this method
      if (isset($methods[$method])) {
        $this->_method= $methods[$method];
        return $this->_method;  
      }
      
      trigger_error('No method defined for ['.$method, E_USER_NOTICE);
      return throw(new HttpScriptletException('Cannot handle method "'.$method.'"'));
    }
    
  }
?>
