<?php
/* This class is part of the XP framework
 *
 * $Id: WebdavPropFindRequest.class.php 8971 2006-12-27 15:27:10Z friebe $
 */

  namespace org::webdav::xml;

  ::uses(
    'org.webdav.WebdavScriptletRequest',
    'org.webdav.WebdavProperty'
  );

  /**
   * PropFind request XML
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
   * @purpose  Encapsulate PROPFIND XML request
   * @see      xp://org.webdav.WebdavScriptlet#doPropFind
   */
  class WebdavPropFindRequest extends org::webdav::WebdavScriptletRequest {
    public
      $request    = NULL,
      $properties = array(),
      $path       = '',
      $webroot    = '',
      $depth      = 0;
    
    /**
     * Set data and parse for properties
     *
     * @param  string data The data
     */
    public function setData($data) {
      parent::setData($data);
      
      // Set properties
      if (
        !$this->getNode('/propfind/allprop') &&
        ($propfind= $this->getNode('/propfind/prop'))
      ) {
        foreach ($propfind->children as $node) {
          $name= $node->getName();
          $ns= 'xmlns';
          $nsprefix= '';
          if (($p= strpos($name, ':')) !== FALSE) {
            $ns.= ':'.($nsprefix= substr($name, 0, $p));
            $name= substr($name, $p+1);
          }
          $p= new org::webdav::WebdavProperty($name);
          if ($nsname= $node->getAttribute($ns)) {
            $p->setNamespaceName($nsname);
            if ($nsprefix) $p->setNamespacePrefix($nsprefix);
          }
          $this->addProperty($p);
        }
      }
    }
    
    /**
     * Return Depth header field
     *
     * @return string
     */
    public function getDepth() {
      switch ($this->getHeader('Depth')) {
        case 'infinity': return 0x7FFFFFFF; break;
        case 1:          return 0x00000001; break;
        default:         return 0x00000000; break;
      }
      
    }
    
    /**
     * Retrieve base uri of request
     *
     * @return  string
     */
    public function getWebroot() {
      return $this->webroot;
    }

    /**
     * Add a property
     *
     * @param   org.webdav.WebdavProperty property The property object
     */
    public function addProperty($property) {
      $this->properties[]= $property;
    }
    
    /**
     * Get all properties
     *
     * @return  &org.webdav.WebdavProperty[]
     */
    public function getProperties() {
      return $this->properties;
    }
  }
?>
