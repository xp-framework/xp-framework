<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('xml.Tree');

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
    const
      WEBDAV_PROPERTY_ALL = NULL;

    public
      $properties = array(),
      $path       = '',
      $webroot    = '',
      $depth      = 0;
    
    /**
     * Constructor
     *
     * @access  public
     * @param   &org.apache.HttpScriptletRequest request
     * @throws  Exception to indicate failure
     */
    public function __construct(&$request) {
      if (FALSE === self::fromString($request->getData())) {
        return FALSE;
      }
      $this->path= $request->uri['path_translated'];
      $this->webroot= $request->uri['path_root'];
      switch ($request->getHeader('depth')) {
        case 'infinity': $this->depth= 0x7FFFFFFF; break;
        case 1:          $this->depth= 0x00000001; break;
        default:         $this->depth= 0x00000000; break;
      }
      self::setEncoding('utf-8');
      parent::__construct();
    }
    
    /**
     * Retrieve base url of request
     *
     * @access  public
     * @return  string
     */
    public function getPath() {
      return $this->path;
    }

    /**
     * Retrieve base uri of request
     *
     * @access  public
     * @return  string
     */
    public function getWebroot() {
      return $this->webroot;
    }

    /**
     * Retrieve depth
     *
     * @access  public
     * @return  int
     */
    public function getDepth() {
      return $this->depth;
    }
    
    /**
     * Add a property
     *
     * @access  public
     * @param   string name
     */
    public function addProperty($name) {
      $this->properties[]= $name;
    }
    
    /**
     * Get all properties
     *
     * @access  public
     * @return  &string[] properties
     */
    public function getProperties() {
      return $this->properties;
    }

    /**
     * Returns an XPath expression for the current entry
     *
     * @access  private
     * @return  string path, e.g. /rdf:rdf/item/rc:summary/
     */
    private function _pathname() {
      $path= '';
      for ($i= $this->_cnt; $i> 0; $i--) {
        if (FALSE !== ($p= strpos($name= strtolower($this->_objs[$i]->name), ':'))) {
          $name= substr($name, $p+ 1);
        }
        $path= $name.'/'.$path;
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
    private function _pCallStartElement($parser, $name, $attrs) {
      parent::_pCallStartElement($parser, $name, $attrs);
      $path= self::_pathname();
      
      // All properties
      if ('/propfind/allprop/' == $path) {
        $this->properties= PROPERTY_ALL;
        return;
      }
      
      // Selective properties
      if ('/propfind/' == substr($path, 0, 10)) {
        if (strlen($path) > 15) {
          self::addProperty(substr($path, 15, -1));
        }
        return;
      }
      
      throw (new FormatException('Parse error: Path "'.$path.'" not recognized'));
    }
  }
?>
