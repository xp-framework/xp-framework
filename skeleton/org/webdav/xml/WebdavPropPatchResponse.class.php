<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'xml.Tree',
    'util.Date'   
  );

  /**
   * single PROPPATCH response XML
   * Status Code  Meaning
   * 200 (OK) The command succeeded.
   * 403 (Forbidden)  The client is unable to alter one of the properties.
   * 409 (Conflict) The client has provided an inappropriate value for this property. For example, the client tried to set a read-only property.
   * 423 (Locked) The destination resource is locked.
   * 507 (Insufficient Storage) The server did not have enough storage space to record the property.
   * <pre>
   *
   *  <a:response  xmlns:b="urn:schemas-microsoft-com:office:office" xmlns:a="DAV:">>
   *    <a:href>http://www.contoso.com/docs/myfile.doc</a:href>
   *    <a:propstat>
   *      <a:status>HTTP/1.1 200 OK</a:status>
   *        <a:prop>
   *          <b:Author/>
   *     </a:prop>
   * </a:propstat>
   *  </a:response>
   * </pre>
   * @purpose  Represent a WebDavObject as PropPatch-Response
   */


  class WebdavPropPatchResponse extends Tree {
    var
      $namespace_map= array();
      
    /**
     * Constructor
     *
     * @access  public
     * @param   &org.webdav.xml.WebdavPropFindRequest request
     * @param   &org.webdav.xml.WebdavMultistatus response
     */
    function __construct(&$request, &$response) {
      parent::__construct();

      if (
        (!is_a($request, 'WebdavPropPatchRequest')) ||
        (!is_a($response, 'WebdavMultistatus'))
      ) {
        return throw(new IllegalArgumentException('Parameters passed of wrong types'));
      }

      $l= &Logger::getInstance();
      $this->c= &$l->getCategory();

      $this->_createRoot($response, $request);
    }

    /**
     * Create Root
     *
     * @access  private
     * @param   &org.webdav.xml.WebdavMultistatus response 
     * @param   &org.webdav.xml.WebdavPropFindRequest request         
     */
    function _createRoot(&$response, &$request){
    
      $this->namespace_map= array();
      $xmlNSArray= array();
      $this->root= &$response->addChild(new Node('D:response', NULL, $xmlNSArray));
    }

    /**
     * Create Answer
     *
     * @access  private
     * @param   string status
     * @param   string property
     * @param   string namespace, default DAV:
     * @return  bool   
     */
    function _createAnswer($status, $property, $namespace= 'DAV:'){
    
      if (!isset($this->statusNode[$status])){
        $this->statusNode[$status]= &$this->root->addChild(new Node('D:prop'));
        $this->statusNode[$status]->addChild(new Node('D:status','HTTP/1.1 '.$status));
      }
    
      $this->statusNode[$status]->addChild(new Node(
        $property,
        NULL,
        array('xmlns' => $namespace))
      );
    return TRUE;
    }
      
    /**
     * Find status_ok
     *
     * @access  public
     * @param   string property
     * @param   string namespace, default DAV:
     * @return  bool true
     */
    function status_ok($property, $namespace= 'DAV:'){
      return $this->_createAnswer(200, $property, $namespace);
    }
    
    /**
     * Find status_forbidden
     *
     * @access  public
     * @param   string property
     * @param   string namespace, default DAV:
     * @return  bool true
     */
    function status_forbidden($property, $namespace= 'DAV:'){
      return $this->_createAnswer(403, $property, $namespace);
    }

    /**
     * Find status_conflict
     *
     * @access  public
     * @param   string property
     * @param   string namespace, default DAV:
     * @return  bool true
     */
    function status_conflict($property, $namespace= 'DAV:'){
      return $this->_createAnswer(409, $property, $namespace);
    }

  }

?>
