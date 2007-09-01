<?php
/* This class is part of the XP framework
 *
 * $Id: WebdavReportResponse.class.php 8971 2006-12-27 15:27:10Z friebe $
 */

  namespace org::webdav::xml;
 
  ::uses('org.webdav.WebdavScriptletResponse');

  /**
   * REPORT Response
   *
   * <pre>
   *  <?xml version="1.0" encoding="UTF-8"?>
   *  <D:multistatus xmlns:D="DAV:">
   *  <D:response>
   *   <D:href>/slide/history/19/1.0</D:href>
   *   <D:propstat>
   *    <D:prop>
   *     <D:version-name>1.0</D:version-name>
   *     <D:creator-displayname>clang</D:creator-displayname>
   *     <D:getcontentlength>9</D:getcontentlength>
   *     <D:getlastmodified>Thu, 08 Jul 2004 13:36:55 GMT</D:getlastmodified>
   *     <D:successor-set>
   *      <D:href>/slide/history/19/1.1</D:href>
   *     </D:successor-set>
   *    </D:prop>
   *    <D:status>HTTP/1.1 200 OK</D:status>
   *   </D:propstat>
   *  </D:response>
   *  <D:response>
   *   <D:href>/slide/history/19/1.1</D:href>
   *   <D:propstat>
   *    <D:prop>
   *     <D:version-name>1.1</D:version-name>
   *     <D:creator-displayname>clang</D:creator-displayname>
   *     <D:getcontentlength>9</D:getcontentlength>
   *     <D:getlastmodified>Thu, 08 Jul 2004 13:37:37 GMT</D:getlastmodified>
   *     <D:successor-set />
   *    </D:prop>
   *    <D:status>HTTP/1.1 200 OK</D:status>
   *   </D:propstat>
   *  </D:response>
   * <pre>                                                                                                                                                             rp/
   *
   * @purpose  Represent a WebdavReport Response
   */
  class WebdavReportResponse extends org::webdav::WebdavScriptletResponse {
      
    /**
     * Constructor
     *
     * @param   &org.webdav.xml.WebdavPropFindRequest request
     * @param   &org.webdav.xml.WebdavMultistatus response
     */
    public function __construct($request, $response) {
      $this->setStatus(WEBDAV_MULTISTATUS);
      $this->setRootNode(new xml::Node(
        'D:multistatus',
        NULL,
        array('xmlns:D' => 'DAV:')
      ));
    }
    
    /**
     * Apply an WebdavObject
     *
     * @param   &org.webdav.version.WebdavVersionContainer container
     */
    public function addWebdavVersionContainer($container) {

      foreach ($container->getVersions() as $version) {

        $res= new xml::Node('D:response');
        $res->addChild(new xml::Node('D:href', $version->getHref()));
        $propstat= $res->addChild(new xml::Node('D:propstat'));

        with ($props= $propstat->addChild(new xml::Node('D:prop'))); {
          $props->addChild(new xml::Node('D:version-name', $version->getVersionNumber()));
          $props->addChild(new xml::Node('D:creator-displayname', $version->getCreatorName()));
          $props->addChild(new xml::Node('D:getcontentlength', $version->getContentLength()));
          $props->addChild(new xml::Node('D:getlastmodified',  $version->lastmodified->toString()));
        }

        $propstat->addChild(new xml::Node('D:status' , 'HTTP/1.1 200 OK'));   
        
        $this->addChild($res);  
      }
    }
  }
?>
