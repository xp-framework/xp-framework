<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
 
  uses('util.Date');
 
  /**
   * Webdav lock object
   *
   * @see      org.webdav.ipl.DavFileImpl#lock
   * @purpose  Represent a Webdav lock
   */
  class WebdavLock extends Object {
    public
      $owner=        NULL,
      $locktype=     NULL,
      $lockscope=    NULL,
      $locktoken=    NULL,
      $uri=          NULL,
      $timeout=      NULL,
      $date=         NULL,
      $depth=        'infinity';
   
   
    /**
     * Constructor
     *
     * @param   string uri
     */   
    public function __construct($uri) {
      $this->uri= $uri;
      $this->setCreationDate(Date::now());
    }
    
    /**
     * Set the Uri
     *
     * @param   string uri
     */
    public function setURI($uri) {
      $this->uri= $uri;
    }
    
    /**
     * Get the Uri
     *
     * @return  string uri
     */
    public function getURI() {
      return $this->uri;
    }
    
    /**
     * Set the Owner
     *
     * @param   string owner
     */
    public function setOwner($owner) {
      $this->owner= $owner;
    }
    
    /**
     * Get the Owner
     *
     * @return  string owner
     */
    public function getOwner() {
      return $this->owner;
    }
    
    /**
     * Set the Locktype
     *
     * @param   string locktype, default 'write'
     */
    public function setLockType($locktype= 'write') {
      $this->locktype= $locktype;
    }
    
    /**
     * Get the Locktype
     *
     * @return  string locktype
     */
    public function getLockType() {
      return $this->locktype;
    }
    
    /**
     * Set the lockscope
     *
     * @param   string scope, default exclusive
     */
    public function setLockScope($scope= 'exclusive') {
      $this->lockscope= $scope;
    }
    
    /**
     * Get the Lockscope
     *
     * @return  string lockscope
     */
    public function getLockScope() {
      return $this->lockscope;
    }
    
    /**
     * Set the Locktoken
     *
     * @param   string token (e.g. opaquelocktoken:e97a3400-3400-197a-84ce-48de5b3f07f4)
     */
    public function setLockToken($token) {
      $this->locktoken= $token;
    }
    
    /**
     * Get the Locktoken
     *
     * @return  string 
     */
    public function getLockToken() {
      return $this->locktoken;
    }
    
    /**
     * Set the Timeout
     *
     * @param   int timeout, default 604800  seconds
     */
    public function setTimeout($timeout= 604800 ) {
      $this->timeout= $timeout;
    }
        
    /**
     * Get the Timeout
     *
     * @return  int timeout
     */
    public function getTimeout() {
      return $this->timeout == NULL ? 604800  : $this->timeout;
    }
    
    /**
     * Set the Depth
     *
     * @param   string depth, default 'infinity'
     */
    public function setDepth($depth= 'infinity') {
      $this->depth= $depth;
    }
    
    /**
     * Get the depth
     *
     * @return  string depth
     */
    public function getDepth() {
      return $this->depth;
    }
    
    /**
     * Set the time of creation
     *
     * @param   util.Date date
     */
    public function setCreationDate($date) {
      $this->date= $date;
    }
    
    /**
     * Get the time
     *
     * @return  util.Date
     */
    public function getCreationDate() {
      return $this->date;
    }
  }
?>
