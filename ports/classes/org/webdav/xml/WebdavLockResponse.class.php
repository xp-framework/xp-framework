<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'org.webdav.WebdavScriptletResponse'
  );

  /**
   * LOCK Response
   *
   * <pre>
   *  <?xml version="1.0" encoding="UTF-8"?>
   *  <D:prop xmlns:D="DAV:">
   *    <D:lockdiscovery>  
   *      <D:activelock>    
   *        <D:locktype>      
   *          <D:write/>
   *        </D:locktype>
   *        <D:lockscope>      
   *          <D:exclusive/>
   *        </D:lockscope>
   *        <D:depth>0</D:depth>
   *        <D:owner>clang</D:owner>
   *        <D:timeout>Second-604800</D:timeout>
   *        <D:locktoken>      
   *          <D:href>opaquelocktoken:36844e80-4e80-1684-bc3d-48de5b3f07f4</D:href>
   *        </D:locktoken>
   *      </D:activelock>
   *    </D:lockdiscovery>
   *  </D:prop>
   * </pre>
   */
  class WebdavLockResponse extends WebdavScriptletResponse {
  
    /**
     * Constructor
     *
     */
    public function __construct() {
      $this->setRootNode(new Node(
        'D:prop',
        NULL,
        array('xmlns:D' => 'DAV:')
      ));
    }
    
    /**
     * Add a webdav object
     *
     * @param   org.webdav.WebdavObject object   The webdav object
     * @param   org.webdav.WebdavProperty[] reqprops The requested properties
     */
    public function addLock($lock) {
      $lockdiscovery= $this->addChild(new Node('D:lockdiscovery'));
      $activelock= $lockdiscovery->addChild(new Node('D:activelock'));
      
      $locktype= $activelock->addChild(new Node('D:locktype'));
      $locktype->addChild(new Node('D:'.$lock->getLockType()));
      
      $lockscope= $activelock->addChild(new Node('D:lockscope'));
      $lockscope->addChild(new Node('D:'.$lock->getLockScope()));
      
      $activelock->addChild(new Node('D:depth', $lock->getDepth()));
      $activelock->addChild(new Node('D:owner', $lock->getOwner()));
      $activelock->addChild(new Node('D:timeout', 'Second-'.$lock->getTimeout()));
      
      $locktoken= $activelock->addChild(new Node('D:locktoken'));
      $locktoken->addChild(new Node('D:href', $lock->getLockToken()));

      $this->setHeader('Lock-Token', '<'.$lock->getLockToken().'>');
    }
  }
?>
