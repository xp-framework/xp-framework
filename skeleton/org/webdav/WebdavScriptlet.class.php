<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'org.apache.HttpScriptlet',
    'org.webdav.xml.WebdavPropFindRequest'
  );
  
  define('WEBDAV_METHOD_PROPFIND',  'PROPFIND');
  define('WEBDAV_METHOD_PROPPATCH', 'PROPPATCH');
  
  /**
   * Webdav
   *
   * Note: Needs PHP patched to work!
   *
   * @see      http://sitten-polizei.de/php/webdav.patch
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
        $p= &new WebdavPropFindRequest($request->getData());
        $prop= $p->getProperties();
      } if (catch('Exception', $e)) {
        return throw(new HttpScriptletException($e->message));
      }

      $l= &Logger::getInstance();
      $c= &$l->getCategory();
      $c->debug('Properties requested', $prop);
      
      // Check which properties were requested
      if (WEBDAV_PROPERTY_ALL == $prop) {
        
      }
      
      // Send "HTTP/1.1 207 Multi-Status" response header
      $response->setStatus(207);
      $response->setHeader('Content-Type', 'text/xml');
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
