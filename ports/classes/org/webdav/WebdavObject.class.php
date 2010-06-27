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
    public
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
     * @param   string href
     * @param   string resourceType
     * @param   string contentLength default 0
     * @param   string contentType default NULL
     * @param   util.Date creationDate
     * @param   util.Date lastModified
     * @param   array properties default array()
     */
    public function __construct(
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
     * @param   string href
     */
    public function setHref($href) {
      $this->href= $href;
    }
    
    /**
     * get Href
     *
     * @return  string href
     */
    public function getHref() {
      return $this->href;
    }
    
    /**
     * Set the Resourcetype
     *
     * @param   string resourcetype
     */
    public function setResourceType($restype) {
      $this->resourceType= $restype;
    }
    
    /**
     * Get the Resourcetype
     *
     * @return  string Resourcetype
     */
    public function getResourceType() {
      return $this->resourceType;
    }
    
    
    /**
     * Set the Contentlength
     *
     * @param   string contentlength
     */
    public function setContentLength($contentlength) {
      $this->contentLength= $contentlength;
    }
    
    /**
     * Get the contentlength
     *
     * @return  string contentlength
     */
    public function getContentLength() {
      return $this->contentLength;
    }
    
    /**
     * Set contenttype
     *
     * @param   string type
     */
    public function setContentType($type) {
      $this->contentType= $type;
    }
    
    /**
     * Get the contenttype
     *
     * @return  string contenttype
     */
    public function getContentType() {
      return $this->contentType;
    }
    
    /**
     * Set the creation date
     *
     * @param   util.Date date
     */
    public function setCreationDate($date) {
      $this->creationDate= $data;
    }
    
    /**
     * Get the creation date
     *
     * @return  util.Date date
     */
    public function getCreationDate() {
      return $this->creationDate;
    }
    
    /**
     * Set the last modified Date
     *
     * @param   util.Date date
     */
    public function setModifiedDate($date) {
      $this->modifiedDate= $date;
    }
    
    /**
     * Get the last modified date
     *
     * @return  util.Date date
     */
    public function getModifiedDate() {
      return $this->modifiedDate;
    }
    
    /**
     * Set properties
     *
     * @param   array properties
     */
    public function setProperties($properties) {
      $this->properties= $properties;
    }
    
    /**
     * Get the properties
     *
     * @return  array properties
     */
    public function getProperty() {
      return $this->properties;
    }

    /**
     * Add Well Known Properties
     *
     */
    protected function _calcProperties() {
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
        $p= new WebdavProperty($name, $propDef['value']);
        $p->setNamespaceName($propDef['ns']);
        $p->setProtected(TRUE);
        $this->addProperty($p);
      }
    }
    
    /**
     * Set data
     *
     * @param   string data
     */
    public function setData($data) {
      $this->_data= $data;
    }
    
    /**
     * Set encoding
     *
     * @param   string data
     */
    public function setEncoding($data) {
      $this->contentEncoding= $data;
    }

    /**
     * Get data
     *
     * @return  string data
     */
    public function getData() {
      return $this->_data;
    }

    /**
     * Get the Lockinfo
     *
     * @return  array lockinfo
     */
    public function getLockInfo() {
      return $this->_lockinfo;
    }
    
    /**
     * Set an Lockinfo
     *
     * @param   sting locktype
     * @param   string lockscope
     * @param   string owner
     * @param   int timeout
     * @param   string token
     * @param   string depth
     */
    public function addLockInfo($locktype, $lockscope, $owner, $timeout, $token, $depth) {
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
     * @param   string propname
     */      
    public function addProperty($property) {
      $this->properties[$property->getName()]= $property;
    }
    
    /**
     * Get Properties
     *
     * @return  array properties
     */
    public function getProperties() {
      $this->_calcProperties();
      return $this->properties;
    }
  } 
?>
