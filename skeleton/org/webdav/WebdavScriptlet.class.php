<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'org.apache.HttpScriptlet',
    'org.webdav.xml.WebdavPropFindRequest',
    'org.webdav.xml.WebdavPropFindResponse',
    'io.Folder',
    'util.MimeType'
  );
  
  define('WEBDAV_METHOD_PROPFIND',  'PROPFIND');
  define('WEBDAV_METHOD_PROPPATCH', 'PROPPATCH');
  
  /**
   * Webdav
   * 
   * <code>
   *   $s= &new WebdavScriptlet('/path/you/want/to/provide/via/dav');
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
   * @purpose  Provide the base for Webdav Services
   */
  class WebdavScriptlet extends HttpScriptlet {
    var
      $path = '';
      
    /**
     * Constructor
     *
     * @access  public
     * @param   string path default './'
     */  
    function __construct($path= './') {
      $this->path= $path;
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
      $response->setHeader('MS-Author-Via', 'DAV');
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
     * Find properties
     *
     * @access  public
     * @param   &org.webdav.xml.WebdavPropFindRequest request
     * @param   &org.webdav.xml.WebdavPropFindResponse response
     * @return  &org.webdav.xml.WebdavPropFindResponse response
     */
    function &findProperties(&$request, &$response) {
      if (
        (!is_a($request, 'WebdavPropFindRequest')) ||
        (!is_a($response, 'WebdavPropFindResponse'))
      ) {
        trigger_error('[request.type ] '.get_class($request), E_USER_NOTICE);
        trigger_error('[response.type] '.get_class($response), E_USER_NOTICE);
        return throw(new IllegalArgumentException('Parameters passed of wrong types'));
      }

      $l= &Logger::getInstance();
      $c= &$l->getCategory();
      $c->debug('Properties requested', $request->getProperties());
      
      $depth= 0;
      $f= &new Folder($this->path);
      try(); {
        $response->addEntry(
          $f->uri,
          $request->getBaseUrl().$f->pathname,
          new Date(filectime($f->uri)),
          new Date(filemtime($f->uri)),
          WEBDAV_COLLECTION
        );
        $depth++;
        
        // Recurse through folder
        while ($depth <= $request->depth && $entry= $f->getEntry()) {
          if (is_dir($f->uri.$entry)) {
            $restype= WEBDAV_COLLECTION;
            $size= $mime= NULL;
          } else {
            $restype= NULL;
            $size= filesize($f->uri.$entry);
            $mime= MimeType::getByFilename($entry);
          }
          $response->addEntry(
            $entry,
            $request->getBaseUrl().$entry,
            new Date(filectime($f->uri.$entry)),
            new Date(filemtime($f->uri.$entry)),
            $restype,
            $size,
            $mime
          );
        }
        
        $f->close();
      } if (catch('Exception', $e)) {
        return throw($e);
      }
      
      return $response;
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
        $multistatus= &$this->findProperties(
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
     * PROPPATCH xml
     * <pre>
     *   <?xml version="1.0" encoding="utf-8" ?>
     *   <D:propertyupdate xmlns:D="DAV:">
     *     <D:set>
     *       <D:prop>
     *         <key xmlns="http://webdav.org/cadaver/custom-properties/">value</key>
     *       </D:prop>
     *     </D:set>
     *   </D:propertyupdate>
     * </pre>
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
