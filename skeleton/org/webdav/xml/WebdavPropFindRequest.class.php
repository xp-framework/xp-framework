<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('xml.Tree');

  // Get all properties
  define('WEBDAV_PROPERTY_ALL',     NULL);

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
  class WebdavPropFindRequest extends Tree {
    var
      $properties= array(),
      $baseurl=    '',
      $depth=      0;
    
    /**
     * Constructor
     *
     * @access  public
     * @param   &org.apache.HttpScriptletRequest request
     * @throws  Exception to indicate failure
     */
    function __construct(&$request) {
      if (FALSE === $this->fromString($request->getData())) {
        return FALSE;
      }
      $this->baseurl= $request->uri['path'];
      $this->depth= intval($request->getHeader('depth'));
      $this->setEncoding('utf-8');
      parent::__construct();
    }
    
    /**
     * Retreive base url of request
     *
     * @access  public
     * @return  string
     */
    function getBaseUrl() {
      return $this->baseurl;
    }
    
    /**
     * Add a property
     *
     * @access  public
     * @param   string name
     */
    function addProperty($name) {
      $this->properties[]= $name;
    }
    
    /**
     * Get all properties
     *
     * @access  public
     * @return  &string[] properties
     */
    function &getProperties() {
      return $this->properties;
    }

    /**
     * Returns an XPath expression for the current entry
     *
     * @access  private
     * @return  string path, e.g. /rdf:rdf/item/rc:summary/
     */
    function _pathname() {
      $path= '';
      for ($i= $this->_cnt; $i> 0; $i--) {
        $path= strtolower($this->_objs[$i]->name).'/'.$path;
      }
      return '/'.$path;
    }
  
    /**
     * Private callback function
     *
     * @access  private
     * @param   resource parser
     * @param   string name
     * @param   array attrs
     * @throws  FormatException in case of a parse error
     */
    function _pCallStartElement($parser, $name, $attrs) {
      parent::_pCallStartElement($parser, $name, $attrs);
      $path= $this->_pathname();
      
      // All properties
      if ('/propfind/allprop/' == $path) {
        $this->properties= PROPERTY_ALL;
        return;
      }
      
      // Selective properties
      if ('/propfind/' == substr($path, 0, 10)) {
        if (strlen($path) > 15) {
          $this->addProperty(substr($path, 15, -1));
        }
        return;
      }
      
      return throw(new FormatException('Parse error: Path "'.$path.'" not recognized'));
    }
  }
?>
