<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
 
  /**
   * Webdav lock object
   *
   * @see      org.webdav.impl.DavFileImpl
   * @purpose  Represent a Version of a File
   */
  class WebdavFileVersion extends Object {
    var
      $versnr=        NULL,
      $href=          NULL,
      $versionname=   NULL,
      $creatorname=   NULL,
      $contentlength= NULL,
      $lastmodified=  NULL;
   
    /**
     * Constructor
     *
     * @access  public
     * @param   string filename
     */   
    function __construct($filename) {
      $this->filename= $filename;
    }
    
    /**
     * Set the Filename
     *
     * @access  public
     * @param   string filename
     */
    function setFilename($filename) {
      $this->filename= $filename;
    }
    
    /**
     * Get the Filename
     *
     * @access  public
     * @return  string filename
     */
    function getFilename() {
      return $this->filename;
    }
    
    /**
     * Set the versionnumber
     *
     * @access  public
     * @param   float versnr
     */
    function setVersionNumber($versnr) {
      $this->versnr= $versnr;
    }
    
    /**
     * Get the versionnumber
     *
     * @access  public
     * @return  float versnr
     */
    function getVersionNumber() {
      return $this->versnr;
    }
    
    /**
     * Set the Href
     *
     * @access  public
     * @param   string href
     */
    function setHref($href) {
      $this->href= $href;
    }
    
    /**
     * Get the Href
     *
     * @access  public
     * @return  string href
     */
    function getHref() {
      return $this->href;
    }
    
    /**
     * Set the versionname (e.g. test_1.0.txt)
     *
     * @access  public
     * @param   string versionname
     */
    function setVersionName($versname) {
      $this->versionname= $versname;
    }
    
    /**
     * Get the versionname
     *
     * @access  public
     * @return  string versionname
     */
    function getVersionName() {
      return $this->versionname;
    }
    
    /**
     * Set the name of the creator
     *
     * @access  public
     * @param   string creator, default NULL
     */
    function setCreatorName($creator= NULL) {
      $this->creatorname= $creator;
    }
    
    /**
     * Get the name of the creator
     *
     * @access  public
     * @return  string creatorname
     */
    function getCreatorName() {
      return $this->creatorname;
    }
    
    /**
     * Set the conentlength
     *
     * @access  public
     * @param   int length
     */
    function setContentLength($length) {
      $this->contentlength= $length;
    }
    
    /**
     * Get the contentlength
     *
     * @access  public
     * @return  int contentlength
     */
    function getContentLength() {
      return $this->contentlength;
    }
    
    /**
     * Set date of last modification
     *
     * @access  public
     * @param   &util.Date date
     */
    function setLastModified(&$date) {
      $this->lastmodified= &$date;
    }
    
    /**
     * Get the date of last modification
     *
     * @access  public
     * @return  &util.Date
     */
    function &getLastModified() {
      return $this->lastmodified;
    }
    
    /**
     * Set the location (e.g. /dav/versions/test_1.0.txt )
     *
     * @access  public
     * @param   string location
     */
    function setLocation($loc) {
      $this->location= $loc;
    }
    
    /**
     * Get the location
     *
     * @access  public
     * @return  string location
     */
    function getLocation() {
      return $this->location;
    }
     
  }
?>
