<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  define('WEBDAV_COLLECTION',   'collection');

  uses(
    'xml.Tree',
    'util.Date'
  );


  /**
   * single PROPFIND response XML
   * <pre>
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
   * </pre>
   * @purpose  Represent a WebDavObject as PropFind-Response
   */


  class WebdavPropResponse extends Tree {
    var
      $namespace_map= array();
      
      
    /**
     * Constructor
     *
     * @access  public
     * @param   &org.webdav.xml.WebdavPropFindRequest request
     * @param   &org.webdav.xml.WebdavMultistatus response
     * @param   &org.webdav.xml.DavImpl  wdav
     */
    function __construct(&$request, &$response, &$wdav) {
      parent::__construct();

      $l= &Logger::getInstance();
      $this->c= &$l->getCategory();

      $this->_createRoot(&$response,&$request,&$wdav);
      $this->_applyWebDavObject(&$wdav,&$request);
        
    }

    /**
     * Create Root
     *
     * @access  private
     * @param   &org.webdav.xml.WebdavMultistatus response      
     * @param   &org.webdav.xml.WebdavPropFindRequest request
     * @param   &org.webdav.WebdavObject    
     */
    function _createRoot(&$response,&$request, &$o){
    
      $this->namespace_map= array();

      //  create namespaces from request and webdavobject and map them
      $nslist= array_merge($o->getNamespaces(), $request->namespaces);

      $xmlNSArray= array();
      if (!empty($nslist)){
        $cnt= 0;
        foreach ($nslist as $namesp => $dummy){
          $this->namespace_map[$namesp]= sprintf('i%d', $cnt);
          $xmlNSArray[sprintf('xmlns:%s', $this->namespace_map[$namesp])]= $namesp;
          $cnt++;
        }
      } 

      $this->root= &$response->addChild(new Node('D:response', NULL, $xmlNSArray));
    }
    
    /**
     * Apply an WebdavObject
     *
     * @access  private
     * @param   &org.webdav.WebdavObject 
     * @param   &org.webdav.xml.WebdavPropFindRequest request     
     */
    function _applyWebDavObject(&$o, &$request){
    
      $props_done= array();                     // properties handled
      $reqprops= &$request->getProperties();    // properties requested
      $propsList= &$o->getProperties();         // properties available
      
      // split url
      $this->root->addChild(new Node('D:href', $o->href));        
      
      // Propertiesdefines
      $stat= &$this->root->addChild(new Node('D:propstat'));
      $props= &$stat->addChild(new Node('D:prop'));

      // properties which we always know
      // get* on collection is not defined!

      foreach ($propsList as $propname => $propdef){

        if (!empty($reqprops) && empty($reqprops[$propname])) continue;
  
        $ns= !empty($this->namespace_map[$propdef[WEBDAV_OBJECT_PROP_NS]]) ?
          $this->namespace_map[$propdef[WEBDAV_OBJECT_PROP_NS]]:
          'D';
        $props->addChild(new Node(
          $ns.':'.$propname,
          &$propdef[WEBDAV_OBJECT_PROP_VAL],
          &$propdef[WEBDAV_OBJECT_PROP_XMLEXT]
          ));
          
        $props_done[$propname]= 1;
      }

      // Always add the Resource type
      $rt= &$props->addChild(new Node('D:resourcetype'));
      // Content type/length via resourceType
      if (WEBDAV_COLLECTION == $o->resourceType) {
        $rt->addChild(new Node('D:collection'));
      } else {
        if (NULL !== $o->resourceType) {
          $rt->addChild(new Node('D:'.$o->resourceType));
        } 
      }
      $props_done['resourcetype']=1;

      // lockingpops, wenn POPERTY_ALL
      if (
        WEBDAV_COLLECTION != $o->resourceType and  
        (empty($reqprops) or 
        !empty($reqprops['supportedlock']))
      ){
        $lock= &$props->addChild(new Node('D:supportedlock'));
        $l1= &$lock->addChild(new Node('D:lockentry'));
        $l2= &$l1->addChild(new Node('D:lockscope'));
        $l2->addChild(new Node('D:exclusive'));
        $l2= &$l1->addChild(new Node('D:locktype'));
        $l2->addChild(new Node('D:write'));
        $l1= &$lock->addChild(new Node('D:lockentry'));
        $l2= &$l1->addChild(new Node('D:lockscope'));
        $l2->addChild(new Node('D:shared'));
        $l2= &$l1->addChild(new Node('D:locktype'));
        $l2->addChild(new Node('D:write'));
        $props_done['supportedlock']= 1;      
      }

      // lock discovery
      if (
        (empty($reqprops) or 
        !empty($reqprops['lockdiscovery']))
      ) {
        $lkif= &$props->addChild(new Node('D:lockdiscovery'));
        $lockinfos= &$o->getLockInfo();

        if ($lockinfos){
          for ($t= 0; $t<sizeof($lockinfos); $t++){
            $lockinfo= $lockinfos[$t];

            if (
              empty($lockinfo['type']) or 
              empty($lockinfo['scope'])
            ) continue; // protect xml

            if (!$lockinfo['depth'])
              $lockinfo['depth']= 'infinity';

            $ak= &$lkif->addChild(new Node('D:activelock'));
            $l= &$ak->addChild(new Node('D:locktype'));
            $l->addChild(new Node('D:'.$lockinfo['type']));
            $l= &$ak->addChild(new Node('D:lockscope'));
            $l->addChild(new Node('D:'.$lockinfo['scope']));
            $l= &$ak->addChild(new Node('D:owner'));
            $l->addChild(new Node('D:href', $lockinfo['owner']));
            $l= &$ak->addChild(new Node('D:timeout', $lockinfo['timeout']));
            $l= &$ak->addChild(new Node('D:locktoken'));
            $l->addChild(new Node('D:href','opaquelocktoken:'.$lockinfo['token']));
            $l= &$ak->addChild(new Node('D:depth', $lockinfo['depth']));
          }
        }
        $props_done['lockdiscovery']= 1;
      }      

      $stat->addChild(new Node('D:status' , 'HTTP/1.1 200 OK'));

      $has_notfoundprops= 0;
      $stat= &new Node('D:propstat');
      $props= &$stat->addChild(new Node('D:prop'));

      foreach ($reqprops as $propname => $dummy ){
        if (!isset($props_done[$propname])){
          $ns= !empty($this->namespace_map[$propdef[WEBDAV_OBJECT_PROP_NS]]) ?
          $this->namespace_map[$propdef[WEBDAV_OBJECT_PROP_NS]]:
          'D';

          $props->addChild(new Node($ns.':'.$propname));
          $has_notfoundprops= 1;
          $props_done[$propname]= 1;
        }
      }
      if ($has_notfoundprops){
        $stat->addChild(new Node('D:status' , 'HTTP/1.1 404 Not Found'));
        $this->root->addChild($stat);
      }      
    
    return;
    }

  }

?>
