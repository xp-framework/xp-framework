<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('org.webdav.util.WebdavBool');

  define ('WEBDAV_XMLTYPE_STRING',     0x0001);
  define ('WEBDAV_XMLTYPE_INT',        0x0002);
  define ('WEBDAV_XMLTYPE_BOOL',       0x0003);
  define ('WEBDAV_XMLTYPE_DATE',       0x0004);
  define ('WEBDAV_XMLTYPE_DATE_RFC',   0x0005);
  
  define ('WEBDAV_OBJECT_PROP_VAL',      0x0000);
  define ('WEBDAV_OBJECT_PROP_NS',       0x0001);
  define ('WEBDAV_OBJECT_PROP_XMLEXT',   0x0002);


  /**
   * Webdav Object
   *
   * @purpose   Represent a "file" or "directory"
   */
  class WebdavObject extends Object {
    var
      $href             = '',
      $_data            = '',
      $contentLength    = 0,
      $resourceType     = NULL,
      $contentType      = NULL,
      $contentEncoding  = NULL,
      $impl             = NULL,
      $properties       = array(),
      $_lockinfo        = array(),      
      $nameSpaces       = array();

 
    /**
     * Constructor
     *
     * @access  public
     * @param   string href
     * @param   string resourceType
     * @param   string contentLength default 0
     * @param   string contentType default NULL
     * @param   &util.Date creationDate
     * @param   &util.Date lastModified
     * @param   array properties default array()
     */
    function __construct(
      $href,
      $resourceType,
      $contentLength= 0,
      $contentType= NULL,
      $creationDate= NULL,
      $modifiedDate= NULL,
      $properties= array()
      ) {
      
      $this->href=          $href;              
      $this->resourceType=  $resourceType;      
      $this->contentType=   $contentType;      
      $this->contentLength= $contentLength;      
      $this->creationDate=  $creationDate;
      $this->modifiedDate=  $modifiedDate;
      $this->properties=    $properties;
        
      $this->_calcProperties();
      parent::__construct();      
    }
    
    /**
     * Set href
     *
     * @access  public 
     * @param   string href
     */
    function setHref($href) {
      $this->href= $href;
    }
    
    /**
     * get Href
     *
     * @access  public 
     * @return  string href
     */
    function getHref() {
    return $this->href;
    }
    
    /**
     * Set the Resourcetype
     *
     * @access  public
     * @param   string resourcetype
     */
    function setResourceType($restype) {
      $this->resourceType= $restype;
    }
    
    /**
     * Get the Resourcetype
     *
     * @access  public
     * @return  string Resourcetype
     */
    function getResourceType() {
      return $this->resourceType;
    }
    
    
    /**
     * Set the Contentlength
     *
     * @access  public
     * @param   string contentlength
     */
    function setContentLength($contentlength) {
      $this->contentLength= $contentlength;
    }
    
    /**
     * Get the contentlength
     *
     * @access  public
     * @return  string contentlength
     */
    function getContentLength() {
      return $this->contentLength;
    }
    
    /**
     * Set contenttype
     *
     * @access  public
     * @param   string type
     */
    function setContentType($type) {
      $this->contentType= $type;
    }
    
    /**
     * Get the contenttype
     *
     * @access  public
     * @return  string contenttype
     */
    function getContentType() {
      return $this->contentType;
    }
    
    /**
     * Set the creation date
     *
     * @access  public
     * @param   &util.Date date
     */
    function setCreationDate($date) {
      $this->creationDate= &$data;
    }
    
    /**
     * Get the creation date
     *
     * @access  public
     * @return  &util.Date date
     */
    function getCreationDate() {
      return $this->creationDate;
    }
    
    /**
     * Set the last modified Date
     *
     * @access  public
     * @param   &util.Date date
     */
    function setModifiedDate($date) {
      $this->modifiedDate= &$date;
    }
    
    /**
     * Get the last modified date
     *
     * @access  public
     * @return  &util.Date date
     */
    function getModifiedDate() {
      return $this->modifiedDate;
    }
    
    /**
     * Set properties
     *
     * @access  public
     * @param   array properties
     */
    function setProperty($properties) {
      $this->properties= $properties;
    }
    
    /**
     * Get the properties
     *
     * @access  public
     * @return  array properties
     */
    function getProperty() {
      return $this->properties;
    }

    /**
     * Add Well Known Properties
     *
     * @access  private
     */
    function _calcProperties() {
    
      if (!isset($this->properties['creationdate']) and $this->creationDate)
        $this->addProperty('creationdate', $this->creationDate,'DAV:', WEBDAV_XMLTYPE_DATE_RFC);
        
      if (!isset($this->properties['getlastmodified']) and $this->modifiedDate)
        $this->addProperty('getlastmodified', $this->modifiedDate,'DAV:', WEBDAV_XMLTYPE_DATE_RFC);

      // Microsoft
      if (!isset($this->properties['iscollection']))
        $this->addProperty('iscollection', WEBDAV_COLLECTION == $this->resourceType,'DAV:', WEBDAV_XMLTYPE_BOOL);
        
      if (!isset($this->properties['isfolder']))
        $this->addProperty('isfolder', WEBDAV_COLLECTION == $this->resourceType,'DAV:' ,WEBDAV_XMLTYPE_BOOL);

      // Nautilus
      if (!isset($this->properties['nautilus-treat-as-directory']))
        $this->addProperty('nautilus-treat-as-directory', WEBDAV_COLLECTION == $this->resourceType,'http://services.eazel.com/namespaces', WEBDAV_XMLTYPE_BOOL);

      // Standard
      if (!isset($this->properties['getcontentlength']) and WEBDAV_COLLECTION != $this->resourceType)
        $this->addProperty('getcontentlength', $this->contentLength,'DAV:', WEBDAV_XMLTYPE_INT);
        
      if (!isset($this->properties['getcontenttype']) and $this->contentType)
        $this->addProperty('getcontenttype', $this->contentType,'DAV:', WEBDAV_XMLTYPE_STRING);
      
      // DAV-FS
      if (!isset($this->properties['executable']) and WEBDAV_COLLECTION != $this->resourceType)
        $this->addProperty('executable',0,'http://apache.org/dav/props/', WEBDAV_XMLTYPE_BOOL);
      
      // URI-Specifica
      if (!isset($this->properties['displayname']))
        $this->addProperty('displayname',basename($this->href),'DAV:', WEBDAV_XMLTYPE_STRING);

      // href again, because of an bug in the MS Internet Explorer
      if (!isset($this->properties['getetag'])  && WEBDAV_COLLECTION != $this->resourceType)
        $this->addProperty('getetag',sprintf(
          '%s-%s-%s',
          substr(md5($urlarr['path']),0,7),
          substr(md5($urlarr['path']),7,4),
          substr(md5($urlarr['path']),11,8)),
          'DAV:',
          WEBDAV_XMLTYPE_STRING);
      return;

    }

    
    /**
     * Set data
     *
     * @access  public 
     * @param   &string data
     */
    function setData(&$data) {
      $this->_data= &$data;
    }
    
    /**
     * Set encoding
     *
     * @access  public 
     * @param   &string data
     */
    function setEncoding($data) {
      $this->contentEncoding= $data;
    }

    /**
     * Get data
     *
     * @access  public
     * @return  &string data
     */
    function &getData() {
      return $this->_data;
    }

    /**
     * Get the Lockinfo
     *
     * @access  public
     * @return  array lockinfo
     */
    function &getLockInfo(){
      return $this->_lockinfo;
    }
    
    /**
     * Set an Lockinfo
     *
     * @access  public
     * @param   sting locktype
     * @param   string lockscope
     * @param   string owner
     * @param   int timeout
     * @param   string token
     * @param   string depth
     */
    function &addLockInfo($locktype, $lockscope, $owner, $timeout, $token, $depth) {
      $this->_lockinfo[]=array(
        'owner'   => $owner,
        'type'    => $locktype,
        'scope'   => $lockscope,
        'timeout' => $timeout,
        'token'   => $token,
        'depth'   => $depth );
    }

    /**
     * Add Property
     *
     * @access  public
     * @param   string propname
     * @param   string value
     * @param   string defaultnamespace
     * @param   constant xmltype
     * @param   string extension
     */      
    function &addProperty($propname, $value, $defaultnamespace= 'DAV:', $xmlType= WEBDAV_XMLTYPE_STRING, $extension= NULL) {
      
      switch ($xmlType){
        case WEBDAV_XMLTYPE_BOOL:
          $this->properties[$propname]= array(WebdavBool::fromBool($value), $defaultnamespace);                                           
          break;
        case WEBDAV_XMLTYPE_DATE:
          $this->properties[$propname]= array($value->toString('Y-m-d\TH:i:s\Z'), $defaultnamespace);                                             
          break;
        
        case WEBDAV_XMLTYPE_DATE_RFC:
          $this->properties[$propname]= array(
            $value->toString('D, j M Y H:m:s \G\M\T'), 'DAV:',
              array('xmlns:b' => 'urn:uuid:c2f41010-65b3-11d1-a29f-00aa00c14882/',
              'b:dt' =>  'dateTime.rfc1123')
            );
          break;
        default:
          $this->properties[$propname]= array($value, $defaultnamespace, $extension);
          break;
      }

      if (!isset($this->nameSpaces[$defaultnamespace]))
        $this->nameSpaces[$defaultnamespace]= sizeof($this->nameSpaces[$defaultnamespace]);
      
      return TRUE;
    }
    
    /**
     * Get Properties
     *
     * @access  public
     * @return  array properties
     */
    function &getProperties() {
      $this->_calcProperties();
      return $this->properties;
    }
    
    /**
     * Get Namespaces
     *
     * @access  public
     * @return  array namespaces
     */
    function &getNameSpaces() {
      return $this->nameSpaces;
    }

  } 
?>
