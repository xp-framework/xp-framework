<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'org.webdav.WebdavScriptletResponse'
  );

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
  class WebdavLockResponse extends WebdavScriptletResponse {
  
    /**
     * Constructor
     *
     * @access  public
     */
    function __construct() {
      $this->setRootNode(new Node(
        'D:prop',
        NULL,
        array('xmlns:D' => 'DAV:')
      ));
    }
    
    /**
     * Add a webdav object
     *
     * @access  public
     * @param   &org.webdav.WebdavObject     object   The webdav object
     * @param   &org.webdav.WebdavProperty[] reqprops The requested properties
     */
    function addLock(&$lock) {
      $lockdiscovery= &$this->addChild(new Node('D:lockdiscovery'));
      $activelock= &$lockdiscovery->addChild(new Node('D:activelock'));
      
      $locktype= &$activelock->addChild(new Node('D:locktype'));
      $locktype->addChild(new Node('D:'.$lock->getLockType()));
      
      $lockscope= &$activelock->addChild(new Node('D:lockscope'));
      $lockscope->addChild(new Node('D:'.$lock->getLockScope()));
      
      $activelock->addChild(new Node('D:depth', $lock->getDepth()));
      $activelock->addChild(new Node('D:owner', $lock->getOwner()));
      $activelock->addChild(new Node('D:timeout', 'Second-'.$lock->getTimeout()));
      
      $locktoken= &$activelock->addChild(new Node('D:locktoken'));
      $locktoken->addChild(new Node('D:href', $lock->getLockToken()));

      $this->setHeader('Lock-Token', '<'.$this->lock->getLockToken().'>');
    }
  }
?>
