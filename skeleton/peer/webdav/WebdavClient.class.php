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
  
    /**
     * Constructor
     *
     * @access  public
     * @param   string host default 'localhost' Webdav server
     * @param   string directory default webdav
     * @param   int port default port 81
     */
    function __construct($url) {
      $this->c= new WebdavConnection($url);
      parent::__construct();
    }
    
    /**
     * Do a Propfind on Webdav server
     *
     * @access  public
     * @return  &peer.http.HttpResponse response object
     */
    function read() {    
      try(); {
        $response= &$this->c->propfind();
      } if (catch('Exception', $e)) {
        return throw($e);
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
      try(); {
        $response= &$this->c->put();
      } if (catch('Exception', $e)) {
        return throw($e);
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
      try(); {
        $response= &$this->c->put();
      } if (catch('Exception', $e)) {
        return throw($e);
      }
      return $response;
    }

  
  }
?>
