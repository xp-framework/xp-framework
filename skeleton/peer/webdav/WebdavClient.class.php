<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
  
  uses(
    'peer.webdav.WebdavConnection',
    'peer.Header',
    'peer.http.BasicAuthorization'
  );

  /**
   * Webdavclient
   *
   * @purpose  Provide a client to access an webdav server
   */
  class WebdavClient extends Object {
    var
      $username=  NULL,
      $passwd=    NULL,
      $dir=       NULL;
  
    /**
     * Constructor
     *
     * @access  public
     * @param   string host default 'localhost' Webdav server
     * @param   string directory default webdav
     * @param   int port default port 81
     */
    function __construct($host= 'localhost', $dir= 'webdav', $port= 80) {
      $this->host= $host;
      $this->dir=  $dir;
      $this->port= $port;
      parent::__construct();
    }
    
    /**
     * Set Username
     *
     * @access  public 
     * @param   string username
     */
    function setUsername($username) {
      $this->username= $username;
    }
    
    /**
     * Set Password
     *
     * @access  public
     * @param   string passwd
     */
    function setPassword($passwd) {
      $this->passwd= $passwd;
    }
    
    /**
     * Set directory which you want to enter
     *
     * @access  public
     * @param   string directory
     */
    function setDirectory($dir) {
      $this->dir= $dir;
    }
    
    /**
     * Connect to the webdav server
     *
     * @access  private
     * @return  bool
     */
    function _connect() {
      $this->c= &new WebdavConnection($this->host.$this->dir);      
    return TRUE;
    }
    
    
    /**
     * Do a Propfind on Webdav server
     *
     * @access  public
     * @return  &peer.http.HttpResponse response object
     */
    function read() {    
      if (!$this->c) $this->_connect();
      
      try(); {
        $response= &$this->c->propfind(
          NULL,
          array(
            new BasicAuthorization(
              $this->username,
              $this->passwd
            )
          )
        );
        
      } if (catch('IOException', $e)) {
        $e->printStackTrace();
        exit();
      } if (catch('Exception', $e)) {
        $e->printStackTrace();
        exit();
      }
      
    return $response;
    }
    
    /**
     * Do a Put on Webdav server
     *
     * @access  public
     * @return  &peer.http.HttpResponse response object
     */
    function put() {
      if (!$this->c) $this->_connect();
      
      try(); {
        $response= &$this->c->put(
          NULL,
          array(            
            new BasicAuthorization(
              $this->username,
              $this->passwd
            )
          )
        );
      
      } if (catch('IOException', $e)) {
        $e->printStackTrace();
        exit();
      } if (catch('Exception', $e)) {
        $e->printStackTrace();
        exit();
      }
        
    return $response;
    }
    
    /**
     * Do a Get on Webdav server
     *
     * @access  public
     * @return  &peer.http.HttpResponse response object
     */
    function get() {
      if (!$this->c) $this->_connect();
      
      try(); {
        $response= &$this->c->put(
          NULL,
          array(            
            new BasicAuthorization(
              $this->username,
              $this->passwd
            )
          )
        );
        
      } if (catch('IOException', $e)) {
        $e->printStackTrace();
        exit();
      } if (catch('Exception', $e)) {
        $e->printStackTrace();
        exit();
      }
         
    return $response;
    }

  
  }
?>
