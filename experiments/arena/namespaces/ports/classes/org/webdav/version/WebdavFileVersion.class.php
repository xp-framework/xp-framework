<?php
/* This class is part of the XP framework
 *
 * $Id: WebdavFileVersion.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace org::webdav::version;
 
  /**
   * Webdav lock object
   *
   * @see      org.webdav.impl.DavFileImpl
   * @purpose  Represent a Version of a File
   */
  class WebdavFileVersion extends lang::Object {
    public
      $versnr=        NULL,
      $href=          NULL,
      $versionname=   NULL,
      $creatorname=   NULL,
      $contentlength= NULL,
      $lastmodified=  NULL;
   
    /**
     * Constructor
     *
     * @param   string filename
     */   
    public function __construct($filename) {
      $this->filename= $filename;
    }
    
    /**
     * Set the Filename
     *
     * @param   string filename
     */
    public function setFilename($filename) {
      $this->filename= $filename;
    }
    
    /**
     * Get the Filename
     *
     * @return  string filename
     */
    public function getFilename() {
      return $this->filename;
    }
    
    /**
     * Set the versionnumber
     *
     * @param   float versnr
     */
    public function setVersionNumber($versnr) {
      $this->versnr= $versnr;
    }
    
    /**
     * Get the versionnumber
     *
     * @return  float versnr
     */
    public function getVersionNumber() {
      return $this->versnr;
    }
    
    /**
     * Set the Href
     *
     * @param   string href
     */
    public function setHref($href) {
      $this->href= $href;
    }
    
    /**
     * Get the Href
     *
     * @return  string href
     */
    public function getHref() {
      return $this->href;
    }
    
    /**
     * Set the versionname (e.g. test_1.0.txt)
     *
     * @param   string versionname
     */
    public function setVersionName($versname) {
      $this->versionname= $versname;
    }
    
    /**
     * Get the versionname
     *
     * @return  string versionname
     */
    public function getVersionName() {
      return $this->versionname;
    }
    
    /**
     * Set the name of the creator
     *
     * @param   string creator, default NULL
     */
    public function setCreatorName($creator= NULL) {
      $this->creatorname= $creator;
    }
    
    /**
     * Get the name of the creator
     *
     * @return  string creatorname
     */
    public function getCreatorName() {
      return $this->creatorname;
    }
    
    /**
     * Set the conentlength
     *
     * @param   int length
     */
    public function setContentLength($length) {
      $this->contentlength= $length;
    }
    
    /**
     * Get the contentlength
     *
     * @return  int contentlength
     */
    public function getContentLength() {
      return $this->contentlength;
    }
    
    /**
     * Set date of last modification
     *
     * @param   &util.Date date
     */
    public function setLastModified($date) {
      $this->lastmodified= $date;
    }
    
    /**
     * Get the date of last modification
     *
     * @return  &util.Date
     */
    public function getLastModified() {
      return $this->lastmodified;
    }
    
    /**
     * Set the location (e.g. /dav/versions/test_1.0.txt )
     *
     * @param   string location
     */
    public function setLocation($loc) {
      $this->location= $loc;
    }
    
    /**
     * Get the location
     *
     * @return  string location
     */
    public function getLocation() {
      return $this->location;
    }
     
  }
?>
