<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'org.apache.HttpScriptlet',
    'org.webdav.util.WebdavBool',
    'org.webdav.xml.WebdavPropFindRequest',
    'org.webdav.xml.WebdavPropPatchRequest',
    'org.webdav.xml.WebdavMultistatus'
  );

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
    const
      WEBDAV_METHOD_PROPFIND = 'PROPFIND',
      WEBDAV_METHOD_PROPPATCH = 'PROPPATCH',
      WEBDAV_METHOD_MKCOL = 'MKCOL',
      WEBDAV_METHOD_LOCK = 'LOCK',
      WEBDAV_METHOD_UNLOCK = 'UNLOCK',
      WEBDAV_METHOD_COPY = 'COPY',
      WEBDAV_METHOD_MOVE = 'MOVE',
      WEBDAV_PROCESSING = 102,
      WEBDAV_MULTISTATUS = 207,
      WEBDAV_UNPROCESSABLE = 422,
      WEBDAV_LOCKED = 423,
      WEBDAV_FAILEDDEPENDENCY = 424,
      WEBDAV_INSUFFICIENTSTOR = 507;

    public
      $impl         = array(),
      $handlingImpl = NULL;
      
    /**
     * Constructor
     *
     * @access  public
     * @param   array impl (associative array of pathmatch => org.webdav.impl.DavImpl)
     */  
    public function __construct($impl) {
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
    private function _relativeTarget($str) {
      $p= parse_url($str);
      return str_replace($this->request->uri['path_root'], '', $p['path']);
    }

    /**
     * Handle OPTIONS
     *
     * @see     xp://org.apache.scriptlet.HttpScriptlet#doGet
     * @access  private
     * @return  bool processed
     * @param   &org.apache.HttpScriptletRequest request
     * @param   &org.apache.HttpScriptletResponse response
     * @throws  Exception to indicate failure
     */
    public function doOptions(&$request, &$response) {
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
     * @see     rfc://2518#8.6
     * @access  private
     * @return  bool processed
     * @param   &org.apache.HttpScriptletRequest request
     * @param   &org.apache.HttpScriptletResponse response
     * @throws  Exception to indicate failure
     */
    public function doDelete(&$request, &$response) {
      try {
        $object= $this->handlingImpl->delete($request->uri['path_translated']);
      } catch (ElementNotFoundException $e) {
      
        // Element not found
        $response->setStatus(HTTP_NOT_FOUND);
        $response->setContent($e->toString());
        return FALSE;
      } catch (XPException $e) {
      
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
     * @param   &org.apache.HttpScriptletRequest request
     * @param   &org.apache.HttpScriptletResponse response
     * @throws  Exception to indicate failure
     */
    public function doGet(&$request, &$response) {
      try {
        $object= $this->handlingImpl->get($request->uri['path_translated']);
      } catch (ElementNotFoundException $e) {
      
        // Element not found
        $response->setStatus(HTTP_NOT_FOUND);
        $response->setContent($e->toString());
        return FALSE;
      } catch (XPException $e) {
      
        // Conflict
        $response->setStatus(HTTP_CONFLICT);
        $response->setContent($e->toString());
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
     * @see     rfc://2518#8.5
     * @access  private
     * @return  bool processed
     * @param   &org.apache.HttpScriptletRequest request
     * @param   &org.apache.HttpScriptletResponse response
     * @throws  Exception to indicate failure
     */
    public function doPost(&$request, &$response) {
    }

    /**
     * Handle HEAD
     *
     * @see     rfc://2518#8.4
     * @access  private
     * @return  bool processed
     * @param   &org.apache.HttpScriptletRequest request
     * @param   &org.apache.HttpScriptletResponse response
     * @throws  Exception to indicate failure
     */
    public function doHead(&$request, &$response) {
      try {
        $object= $this->handlingImpl->get($request->uri['path_translated']);
      } catch (ElementNotFoundException $e) {
      
        // Element not found
        $response->setStatus(HTTP_NOT_FOUND);
        $response->setContent($e->toString());
        return FALSE;
      } catch (XPException $e) {
      
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
     * @param   &org.apache.HttpScriptletRequest request
     * @param   &org.apache.HttpScriptletResponse response
     * @throws  Exception to indicate failure
     */
    public function doPut(&$request, &$response) {
      try {
        $created= $this->handlingImpl->put(
          $request->uri['path_translated'],
          $request->getData()
        );
      } catch (OperationFailedException $e) {
      
        // Conflict
        $response->setStatus(HTTP_CONFLICT);
        $response->setContent($e->toString());
        return FALSE;
      } catch (OperationNotAllowedException $e) {
      
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
     * @param   &org.apache.HttpScriptletRequest request
     * @param   &org.apache.HttpScriptletResponse response
     * @throws  Exception to indicate failure
     */
    public function doMkCol(&$request, &$response) {
      try {
        $created= $this->handlingImpl->mkcol($request->uri['path_translated']);
      } catch (OperationFailedException $e) {
      
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
     * @param   &org.apache.HttpScriptletRequest request
     * @param   &org.apache.HttpScriptletResponse response
     * @throws  Exception to indicate failure
     */
    public function doMove(&$request, &$response) {
      try {
        $created= $this->handlingImpl->copy(
          $request->uri['path_translated'],
          self::_relativeTarget($request->getHeader('Destination')),
          WebdavBool::fromString($request->getHeader('Overwrite'))
        );
      } catch (OperationFailedException $e) {
      
        // Conflict
        $response->setStatus(HTTP_CONFLICT);
        $response->setContent($e->toString());
        return FALSE;
      } catch (OperationNotAllowedException $e) {
      
        // Not allowed
        $response->setStatus(HTTP_METHOD_NOT_ALLOWED);
        $response->setContent($e->toString());
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
     * @param   &org.apache.HttpScriptletRequest request
     * @param   &org.apache.HttpScriptletResponse response
     * @throws  Exception to indicate failure
     */
    public function doCopy(&$request, &$response) {
      try {
        $created= $this->handlingImpl->copy(
          $request->uri['path_translated'],
          self::_relativeTarget($request->getHeader('Destination')),
          WebdavBool::fromString($request->getHeader('Overwrite'))
        );
      } catch (OperationFailedException $e) {
      
        // Conflict
        $response->setStatus(HTTP_CONFLICT);
        $response->setContent($e->toString());
        return FALSE;
      } catch (OperationNotAllowedException $e) {
      
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
     * @param   &org.apache.HttpScriptletRequest request
     * @param   &org.apache.HttpScriptletResponse response
     * @throws  Exception to indicate failure
     */
    public function doLock(&$request, &$response) {
    }

    /**
     * Handle UNLOCK
     *
     * @see     rfc://2518#8.11
     * @access  private
     * @return  bool processed
     * @public  request org.apache.HttpScriptletRequest
     * @access  response org.apache.HttpScriptletResponse
     * @throws  Exception to indicate failure
     */
    public function doUnlock(&$request, &$response) {
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
     * @param   &org.apache.HttpScriptletRequest request
     * @param   &org.apache.HttpScriptletResponse response
     * @throws  Exception to indicate failure
     */
    public function doPropFind(&$request, &$response) {
      try {
        $multistatus= $this->handlingImpl->propfind(
          new WebdavPropFindRequest($request),
          new WebdavMultistatus()
        );
      } catch (ElementNotFoundException $e) {
      
        // Element not found
        $response->setStatus(HTTP_NOT_FOUND);
        $response->setContent($e->toString());
        return FALSE;
      } catch (FormatException $e) {
      
        // XML parse errors
        $response->setStatus(HTTP_BAD_REQUEST);
        $response->setContent($e->toString());
        return FALSE;
      } catch (XPException $e) {
      
        // Other exceptions - throw exception to indicate (complete) failure
        throw (new HttpScriptletException($e->message));
      }
      
      // Send "HTTP/1.1 207 Multi-Status" response header
      $response->setStatus(WEBDAV_MULTISTATUS);
      $response->setHeader(
        'Content-Type', 
        'text/xml, charset="'.$multistatus->getEncoding().'"'
      );
      
      $response->setContent(
        $multistatus->getDeclaration()."\n".
        $multistatus->getSource(0)
      );
    }

    /**
     * Receives an PROPPATCH request from the <pre>process()</pre> method
     * and handles it.
     *
     * @see     rfc://2518#8.2
     * @access  private
     * @return  bool processed
     * @param   &org.apache.HttpScriptletRequest request
     * @param   &org.apache.HttpScriptletResponse response
     * @throws  Exception to indicate failure
     */
    public function doPropPatch(&$request, &$response) {
      try {
        $this->handlingImpl->proppatch(
          new WebdavPropPatchRequest($request)
        );
      } catch (ElementNotFoundException $e) {
      
        // Element not found
        $response->setStatus(HTTP_NOT_FOUND);
        $response->setContent($e->toString());
        return FALSE;
      } catch (FormatException $e) {
      
        // XML parse errors
        $response->setStatus(HTTP_BAD_REQUEST);
        $response->setContent($e->toString());
        return FALSE;
      } catch (OperationFailedException $e) {
      
        // Element not found
        $response->setStatus(HTTP_CONFLICT);
        $response->setContent($e->toString());
        return FALSE;
      } catch (XPException $e) {
      
        // Other exceptions - throw exception to indicate (complete) failure
        throw (new HttpScriptletException($e->message));
      }
      
      // TBD: MultiStatus response
    }
  
    /**
     * Handle methods
     *
     * @access  private
     * @return  string class method (one of doGet, doPost, doHead)
     * @param   string method Request-Method
     * @see     rfc://2518#8 Description of methods
     */
    private function _handleMethod($method) {
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
        $this->handlingImpl= $this->impl[$pattern];
        break;
      }
      
      // Implementation not found
      if (NULL === $this->handlingImpl) {
        trigger_error('No pattern match ['.implode(', ', array_keys($this->impl)).']', E_USER_NOTICE);
        throw (new HttpScriptlet('Cannot handle requests to '.$request->uri['path']));
      }

      // Check if we recognize this method
      if (isset($methods[$method])) {
        $this->_method= $methods[$method];
        return $this->_method;  
      }
      
      throw (new HttpScriptletException('Cannot handle method "'.$method.'"'));
    }
  }
?>
