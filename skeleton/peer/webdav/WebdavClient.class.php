<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
  
  uses(
    'peer.webdav.WebdavConnection',
    'peer.Header'
  );

  /**
   * Webdavclient
   *
   * @purpose  Provide a client to access an webdav server
   */
  class WebdavClient extends Object {
    var 
      $url= NULL,
      $path= '';
  
    /**
     * Constructor
     *
     * @access  public
     * @param   string url or peer.Url
     */
    function __construct($url) {
      if (!is_a($url, 'URL')) $this->url= &new URL($url);
      parent::__construct();
    }
    
    /**
     * Get e Connection
     *
     * @access  public
     * @param   string name
     * @return  &peer.webdav.WebdavConnection
     */
    function &getConnection($name= NULL) {
      $url= &new URL($this->url->getURL().$this->path.($name === NULL ? '' : '/'.$name));
      return new WebdavConnection($url);
    }
    
    /**
     * Helper Method to set the path
     *
     * @access  public
     * @param   string path
     */
    function setPath($path) {
      $this->path= $path;
    }
    
    /**
     * Do a Propfind on Webdav server
     *
     * @access  public
     * @param   string filename or directory
     * @return  &peer.http.HttpResponse response object
     */
    function &read($name= NULL) {    
      try(); {
        $c= &$this->getConnection($name === NULL ? NULL : $name);
        $response= &$c->propfind();
      } if (catch('Exception', $e)) {
        return throw($e);
      }
    return $response;
    }
    
    /**
     * Do a Put on Webdav server
     *
     * @access  public
     * @param   io.File
     * @param   string filename or directory
     * @return  &peer.http.HttpResponse response object
     */
    function &put($file, $name= NULL) {    
      
      if (!$file->isOpen()) $file->open(FILE_MODE_READ);
      
      try(); {
        $c= &$this->getConnection($name === NULL ? NULL : $name);
        $response= &$c->put(
          $file->read($file->size()),
          new header('Content-Type', $file->getExtension())
        );
      } if (catch('Exception', $e)) {
        return throw($e);
      }
    return $response;
    }
    
    /**
     * Do a Get on Webdav server
     *
     * @access  public
     * @param   string filename or directory
     * @return  &peer.http.HttpResponse response object
     */
    function &get($name= NULL) {
      try(); {
        $c= &$this->getConnection($name === NULL ? NULL : $name);
        $response= &$c->get();
      } if (catch('Exception', $e)) {
        return throw($e);
      }
    return $response;
    }
    
    /**
     * Do a Proppatch request
     *
     * @access  public
     * @param   string properties
     * @param   string filename or directory
     * @return  &peer.http.HttpResponse response object
     */
    function &proppatch($properties, $name= NULL) {
      
      try(); {
        $c= &$this->getConnection($name === NULL ? NULL : $name);
        $response= &$c->proppatch($properties);
      } if (catch('Exception', $e)) {
        return throw($e);
      }
    return $response;    
    }

  
  }
?>
