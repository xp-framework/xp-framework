<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  /**
   * Webdav Object
   *
   * @purpose  Represent a "file" or "directory"
   */
  class WebdavObject extends Object {
    var
      $displayName      = '',
      $href             = '',
      $creationDate     = NULL,
      $lastModified     = NULL,
      $resourceType     = '',
      $contentLength    = 0,
      $contentType      = NULL,
      $status           = 0,
      $properties       = array();
      
    var
      $_data            = '';
 
    /**
     * Constructor
     *
     * @access  public
     * @param   string displayName
     * @param   string href
     * @param   &util.Date creationDate
     * @param   &util.Date lastModified
     * @param   string resourceType
     * @param   string contentLength default 0
     * @param   string contentType default NULL
     * @param   int status default HTTP_OK
     * @param   array properties default array()
     */
    function __construct(
      $displayName,
      $href,
      &$creationDate,
      &$lastModified,
      $resourceType,
      $contentLength= 0,
      $contentType= NULL,
      $status= HTTP_OK,
      $properties= array()
    ) {
      $this->displayName   = $displayName;       
      $this->href          = $href;              
      $this->creationDate  = $creationDate;      
      $this->lastModified  = $lastModified;      
      $this->resourceType  = $resourceType;      
      $this->contentLength = $contentLength;     
      $this->contentType   = $contentType;       
      $this->status        = $status;            
      $this->properties    = $properties;  
      parent::__construct();      
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
     * Get data
     *
     * @access  public
     * @return  &string data
     */
    function &getData() {
      return $this->_data;
    }
  } 
?>
