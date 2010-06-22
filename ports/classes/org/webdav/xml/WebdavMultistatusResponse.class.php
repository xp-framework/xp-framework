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
  class WebdavMultistatusResponse extends WebdavScriptletResponse {
  
    /**
     * Constructor
     *
     */
    public function __construct() {
      $this->setStatus(WEBDAV_MULTISTATUS);
      $this->setRootNode(new Node(
        'D:multistatus',
        NULL,
        array('xmlns:D' => 'DAV:')
      ));
    }
    
    /**
     * Add a webdav object
     *
     * @param   org.webdav.WebdavObject object The webdav object
     * @param   org.webdav.WebdavProperty[] reqprops The requested properties
     */
    public function addWebdavObject($object, $reqprops) {

      // Get the property lists
      $propsList= $object->getProperties();         // properties available
      
      // Create the result nodes (for found and not found properties)
      $found_stat= new Node('D:propstat');
      $found_props= $found_stat->addChild(new Node('D:prop'));
      $notfound_stat= new Node('D:propstat');
      $notfound_props= $notfound_stat->addChild(new Node('D:prop'));
      
      $stdprops= array();
      
      // Always add the Resource type
      $rt= $found_props->addChild(new Node('D:resourcetype'));

      // Content type/length via resourceType
      if (NULL !== $object->resourceType) {
        $rt->addChild(new Node('D:'.(WEBDAV_COLLECTION == $object->resourceType ? 'collection' : $object->resourceType)));
      }
      $stdprops[]= 'resourcetype';

      // lockingpops, wenn POPERTY_ALL
      if (
        WEBDAV_COLLECTION != $object->resourceType and  
        (empty($reqprops) or !empty($reqprops['supportedlock']))
      ){
        $lock= $found_props->addChild(new Node('D:supportedlock'));
        $l1= $lock->addChild(new Node('D:lockentry'));
        $l2= $l1->addChild(new Node('D:lockscope'));
        $l2->addChild(new Node('D:exclusive'));
        $l2= $l1->addChild(new Node('D:locktype'));
        $l2->addChild(new Node('D:write'));
        $l1= $lock->addChild(new Node('D:lockentry'));
        $l2= $l1->addChild(new Node('D:lockscope'));
        $l2->addChild(new Node('D:shared'));
        $l2= $l1->addChild(new Node('D:locktype'));
        $l2->addChild(new Node('D:write'));
        $stdprops[]= 'supportedlock';
      }

      // lock discovery
      if ((empty($reqprops) or !empty($reqprops['lockdiscovery']))) {
        $lkif= $found_props->addChild(new Node('D:lockdiscovery'));
        $lockinfos= $object->getLockInfo();

        if ($lockinfos) {
          for ($t= 0; $t<sizeof($lockinfos); $t++) {
            $lockinfo= $lockinfos[$t];

            if (empty($lockinfo['type']) or empty($lockinfo['scope'])) continue;

            $ak= $lkif->addChild(new Node('D:activelock'));
            $l= $ak->addChild(new Node('D:locktype'));
            $l->addChild(new Node('D:'.$lockinfo['type']));
            $l= $ak->addChild(new Node('D:lockscope'));
            $l->addChild(new Node('D:'.$lockinfo['scope']));
            $l= $ak->addChild(new Node('D:owner', $lockinfo['owner']));
            $l= $ak->addChild(new Node('D:timeout', $lockinfo['timeout']));
            $l= $ak->addChild(new Node('D:locktoken'));
            $l->addChild(new Node('D:href', $lockinfo['token']));
            $l= $ak->addChild(new Node('D:depth', $lockinfo['depth']));
            $stdprops[]= 'lockdiscovery';
          }
        }
      }      

      // properties which we always know
      // get* on collection is not defined!
      foreach ($reqprops == NULL ? $propsList : $reqprops as $property) {
        $name= $property->getName();
        if (in_array($name, $stdprops)) continue;
        if ($found= isset($propsList[$name])) {
          $property= $propsList[$name];
        }
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

        if ($found) {
          $n= $found_props->addChild(new Node($name, utf8_encode($property->toString()), $attr));
        } else {
          $n= $notfound_props->addChild(new Node($name, utf8_encode($property->toString()), $attr));
        }
      }

      // Build result (href, properties, status, ...)
      $response= new Node('D:response');
      $response->addChild(new Node('D:href', $this->encodePath($object->getHref())));
      $found_stat->addChild(new Node('D:status' , 'HTTP/1.1 200 OK'));
      $response->addChild($found_stat);
      if (!empty($notfound_props->children)) {
        $notfound_stat->addChild(new Node('D:status' , 'HTTP/1.1 404 Not Found'));
        $response->addChild($notfound_stat);
      }
      $this->addChild($response);
    }
  }
?>
