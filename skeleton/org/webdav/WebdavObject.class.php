<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('org.webdav.util.WebdavBool', 'org.webdav.WebdavProperty');

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
      $_lockinfo        = array();
      
 
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
      $this->contentLength= $contentLength;      
      $this->contentType=   $contentType;      
      $this->creationDate=  $creationDate;
      $this->modifiedDate=  $modifiedDate;
      $this->properties=    $properties;
        
      $this->_calcProperties();
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
    function setProperties($properties) {
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
      $etag= md5($this->href);
      $etag= sprintf(
        '%s-%s-%s',
        substr($etag, 0, 7),
        substr($etag, 7, 4),
        substr($etag, 11, 8)
      );
      foreach (array(

        // Default
        'creationdate'     => array('value' => $this->creationDate, 'ns' => 'DAV:'),
        'getlastmodified'  => array('value' => $this->modifiedDate, 'ns' => 'DAV:'),
        'getcontentlength' => array('value' => $this->contentLength, 'ns' => 'DAV:'),
        'getcontenttype'   => array('value' => $this->contentType, 'ns' => 'DAV:'),
        
        // Microsoft
        'isfolder'         => array('value' => WEBDAV_COLLECTION == $this->resourceType, 'ns' => 'DAV:'),
        
        // DAV-FS
        'executable'       => array('value' => WEBDAV_COLLECTION != $this->resourceType ? FALSE : NULL, 'ns' => 'http://apache.org/dav/props/'),
        
        // URI-Specification
        'displayname'      => array('value' => basename($this->href), 'ns' => 'DAV:'),
        
        // Nautilus
        'nautilus-treat-as-directory' => array('value' => WEBDAV_COLLECTION == $this->resourceType, 'ns' => 'http://services.eazel.com/namespaces'),
        'getlastmodified'  => array('value' => $this->modifiedDate, 'ns' => 'DAV:'),
        
        // etag generation (aka Entity Tags)
        'getetag'          => array('value' => WEBDAV_COLLECTION != $this->resourceType ? $etag : NULL, 'ns' => 'DAV:')
        
      ) as $name => $propDef) {
        if ($propDef['value'] === NULL) continue;
        $p= &new WebdavProperty($name, $propDef['value']);
        $p->setNamespaceName($propDef['ns']);
        $p->setProtected(TRUE);
        $this->addProperty($p);
      }
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
    function &getLockInfo() {
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
      $this->_lockinfo[]= array(
        'owner'   => $owner,
        'type'    => $locktype,
        'scope'   => $lockscope,
        'timeout' => $timeout,
        'token'   => $token,
        'depth'   => $depth
      );
    }

    /**
     * Add Property
     *
     * @access  public
     * @param   string propname
     */      
    function &addProperty($property) {
      $this->properties[$property->getName()]= $property;
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
  } 
?>
