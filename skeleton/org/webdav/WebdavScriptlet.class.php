<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('org.apache.HttpScriptlet');
  
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

    /**
     * Receives an PROPPATCH request from the <pre>process()</pre> method
     * and handles it.
     *
     * PROPFIND xml[1]:
     * <pre>
     *   <?xml version="1.0" encoding="utf-8"?>
     *   <propfind xmlns="DAV:">
     *     <allprop/>
     *   </propfind>
     * </pre>
     *
     * PROPFIND xml[2]:
     * <pre>
     *   <?xml version="1.0" encoding="utf-8"?>
     *   <propfind xmlns="DAV:">
     *     <prop>
     *       <getcontentlength xmlns="DAV:"/>
     *       <getlastmodified xmlns="DAV:"/>
     *       <displayname xmlns="DAV:"/>
     *       <executable xmlns="http://apache.org/dav/props/"/>
     *       <resourcetype xmlns="DAV:"/>
     *     </prop>
     *   </propfind>
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
