<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('xml.Tree', 'util.Date');
  
  define('WEBDAV_COLLECTION',   'collection');

  /**
   * PropFind response XML
   *
   * PROPFIND response XML
   * <pre>
   *   <?xml version="1.0" encoding="utf-8"?>
   *   <D:multistatus xmlns:D="DAV:">
   *     <D:response xmlns:i0="DAV:" xmlns:lp0="DAV:" xmlns:lp1="http://apache.org/dav/props/" xmlns:i1="http://apache.org/dav/props/">
   *       <D:href>/webdav-test/</D:href>
   *       <D:propstat>
   *         <D:prop>
   *           <lp0:getlastmodified xmlns:b="urn:uuid:c2f41010-65b3-11d1-a29f-00aa00c14882/" b:dt="dateTime.rfc1123">Sun, 05 Jan 2003 05:40:56 GMT</lp0:getlastmodified>
   *           <D:resourcetype><D:collection/></D:resourcetype>
   *         </D:prop>
   *         <D:status>HTTP/1.1 200 OK</D:status>
   *       </D:propstat>
   *       <D:propstat>
   *         <D:prop>
   *           <i0:getcontentlength/>
   *           <i0:displayname/>
   *           <i1:executable/>
   *         </D:prop>
   *         <D:status>HTTP/1.1 404 Not Found</D:status>
   *       </D:propstat>
   *       </D:response>
   *   </D:multistatus>
   * </pre>
   */
  class WebdavPropFindResponse extends Tree {
    
    /**
     * Constructor
     *
     * @access  public
     */
    function __construct() {
      parent::__construct();
      $this->root= &new Node('D:multistatus', NULL, array(
        'xmlns:D' => 'DAV:',
        'xmlns:P' => 'http://apache.org/dav/props/'
      ));
    }
    
    /**
     * Add an entry
     *
     * @access  public
     * @param   string displayName
     * @param   string href
     * @param   &util.Date creationDate
     * @param   &util.Date lastModified
     * @param   string resourceType
     * @param   string contentLength default 0
     * @param   string contentType default NULL
     * @param   int status default HTTP_STATUS_OK
     * @param   array properties default array()
     */
    function addEntry(
      $displayName,
      $href,
      &$creationDate,
      &$lastModified,
      $resourceType,
      $contentLength= 0,
      $contentType= NULL,
      $status= HTTP_OK,
      $properties= array()
    ) {
      $e= &$this->root->addChild(new Node('D:response'));
      /*, NULL, array(
        'xmlns:P' => 'http://apache.org/dav/props/'
      )));*/
      $e->addChild(new Node('D:href', $href));
      $e->addChild(new Node('D:status', 'HTTP/1.1 '.$status));

      // Properties
      $stat= &$e->addChild(new Node('D:propstat'));
      $props= &$stat->addChild(new Node('D:prop'));
      
      // Properties: Dates
      $props->addChild(new Node(
        'D:creationdate', $creationDate->toString('Y-m-d\TH-i-s\Z')
      ));
      $props->addChild(new Node(
        'D:getlastmodified', $creationDate->toString('D, j M Y H:m:s \G\M\T')
      ));
      
      // Resource type
      $rt= &$props->addChild(new Node('D:resourcetype'));
      if (NULL !== $resourceType) {
        $rt->addChild(new Node('D:'.$resourceType));
      }
      
      // Content type/length via resourceType
      if (WEBDAV_COLLECTION == $resourceType) {
        $contentType= 'httpd/unix-directory';
        $contentLength= 0;
      }
      
      // Additional properties
      foreach (array_merge_recursive(array(
        'P:displayname'       => $displayName,
        'D:getcontentlength'  => $contentLength,
        'D:getcontenttype'    => $contentType
      ), $properties) as $key => $val) {
        $props->addChild(new Node($key, $val));
      }
    }
  }
?>
