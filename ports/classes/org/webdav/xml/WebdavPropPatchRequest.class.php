<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'org.webdav.WebdavScriptletRequest',
    'org.webdav.WebdavProperty'
  );

  /**
   * PropPatch request XML
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
   *
   * @purpose  Encapsulate PROPPATCH XML request
   * @see      xp://org.webdav.WebdavScriptlet#doPropPatch
   */
  class WebdavPropPatchRequest extends WebdavScriptletRequest {
    public
      $filename=   '',
      $properties= array(),
      $baseurl=    '';
    
    /**
     * Set data and parse for properties
     *
     * @param  string data The data
     */
    public function setData($data) {
      static $trans;
      parent::setData($data);

      // Get the NamespacePrefix
      $ns= $this->getNamespacePrefix();
      
      // Select properties which should be set
      foreach (array(
        FALSE => $this->getNode('/'.$ns.':propertyupdate/'.$ns.':set/'.$ns.':prop'),
        TRUE  => $this->getNode('/'.$ns.':propertyupdate/'.$ns.':remove/'.$ns.':prop')
      ) as $remove => $propupdate) {
        if (!$propupdate) continue;
        
        // Copied from WebdavPropFindRequest::setData()
        foreach ($propupdate->children as $node) {
          $name= $node->getName();
          $ns= 'xmlns';
          $nsprefix= '';
          if (($p= strpos($name, ':')) !== FALSE) {
            $ns.= ':'.($nsprefix= substr($name, 0, $p));
            $name= substr($name, $p+1);
          }
          $p= new WebdavProperty(
            $name,
            $this->decode($node->getContent())
          );
          if ($nsname= $node->getAttribute($ns)) {
            $p->setNamespaceName($nsname);
            if ($nsprefix) $p->setNamespacePrefix($nsprefix);
          }
          $this->addProperty($p, $remove);
        }
      }
      
    }
    
    /**
     * Retrieve base url of request
     *
     * @return  string
     */
    public function getFilename() {
      return $this->filename;
    }
    
    /**
     * Add a property
     *
     * @param   org.webdav.WebdavProperty property The property object
     */
    public function addProperty($property, $remove= FALSE) {
      $this->properties[$remove][]= $property;
    }
    
    /**
     * Get all properties
     *
     * @return  org.webdav.WebdavProperty[]
     */
    public function getProperties($remove= FALSE) {
      return $this->properties[$remove];
    }
  }
?>
