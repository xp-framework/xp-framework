<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'org.webdav.WebdavScriptletResponse'
  );

  /**
   * single PROPPATCH response XML
   *
   * Status Code  Meaning
   * 200 (OK) The command succeeded.
   * 403 (Forbidden)  The client is unable to alter one of the properties.
   * 409 (Conflict) The client has provided an inappropriate value for this property. For example, the client tried to set a read-only property.
   * 423 (Locked) The destination resource is locked.
   * 507 (Insufficient Storage) The server did not have enough storage space to record the property.
   *
   * <pre>
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
   *
   * @purpose  Represent a WebDavObject as PropPatch-Response
   */
  class WebdavPropPatchResponse extends WebdavScriptletResponse {
    
    public
      $hrefNode=     NULL,
      $propstatNode= array(),
      $propNode=     array();
    
    /**
     * Constructor
     *
     * @param   org.webdav.xml.WebdavPropFindRequest request
     * @param   org.webdav.xml.WebdavMultistatus response
     */
    public function __construct() {
      $this->setRootNode(new Node(
        'D:response',
        NULL,
        array('xmlns:D' => 'DAV:')
      ));
      $this->hrefNode= $this->addChild(new Node('D:href'));
    }
    
    /**
     * Sets the href attribute for the response
     *
     * @param  string href The href
     */
    public function setHref($href) {
      $this->hrefNode->setContent($href);
    }

    /**
     * Create Answer
     *
     * @param   string status
     * @param   string property
     * @param   string namespace default DAV:
     * @return  bool   
     */
    public function addProperty($property, $status= HTTP_OK) {
      if (!isset($this->statusNode[$status])) {
        $this->propstatNode[$status]= $this->addChild(new Node('D:propstat'));
        $this->propNode[$status]= $this->propstatNode[$status]->addChild(new Node('D:prop'));
      }

      $name= $property->getName();
      $attr= $property->getAttributes();
      $nsname= $property->getNamespaceName();
      $nsprefix= $property->getNamespacePrefix();
      $stdprop= $nsname == 'DAV:';
      if ($stdprop) {
        $name = 'D:'.$name;
      } else if ($nsname) {
        $attr['xmlns'.(!empty($nsprefix) ? (':'.$nsprefix) : '')]= $nsname;
        if (!empty($nsprefix)) $name= $nsprefix.':'.$name;
      }
      $this->propNode[$status]->addChild(new Node($name, NULL, $attr));
      
      return TRUE;
    }

    /**
     * Process response and add the status codes
     *
     */    
    public function process() {
      foreach (array_keys($this->propstatNode) as $status) {
        $this->propstatNode[$status]->addChild(new Node('D:status', 'HTTP/1.0 '.$status));
      }

      parent::process();
    }
  }
?>
