<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
  
  uses(
    'peer.webdav.WebdavConnection',
    'peer.Header',
    'util.MimeType'
  );

  /**
   * WebDAV Client.
   * 
   * WebDAV stands for "Web-based Distributed Authoring and
   * Versioning". It is a set of extensions to the HTTP protocol
   * which allows users to collaboratively edit and manage files
   * on remote web servers.
   *
   * <code>
   *   uses(
   *     'peer.webdav.WebdavClient',
   *     'peer.URL'
   *   );
   *   
   *   $client= &new WebdavClient(new URL('http://kiesel:password@xp-framework.net/xp/doc'));
   *   try(); {
   *     $response= &$client->get('class.xslt');
   *     while ($r= $response->readData()) {
   *       Console::write($r);
   *     }
   *   } if (catch ('Exception', $e)) {
   *     $e->printStackTrace();
   *     exit(-1);
   *   }
   * </code>
   *
   * @see       http://www.webdav.org
   * @see       rfc://2518
   * @see       rfc://3253
   * @see       rfc://3648
   * @purpose   Provide a client to access an webdav server
   */
  class WebdavClient extends Object {
    var 
      $url=     NULL,
      $uri=     '',
      $path=    '',
      $depth=   '1',
      $xml=     NULL,
      $source=        '',
      $destination=   '',
      $overwrite=     '1';   
  
    /**
     * Constructor.
     *
     * @access  public
     * @param   mixed url either a string or a peer.URL object
     */
    function __construct($url) {
      if (!is_a($url, 'URL')) $this->url= &new URL($url); else $this->url= &$url;
    }
    
    /**
     * Get a Connection
     *
     * @access  public
     * @param   string uri
     * @return  &peer.webdav.WebdavConnection
     */
    function &getConnection($uri= NULL) {
      $url= &new URL($this->url->getURL().$this->path.($uri === NULL ? '' : '/'.$uri));
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
     * @param   string uri, filename or directory
     * @param   string xml, The XML of the Propfind Request (e.g. to select properties)  
     * @param   int depth, default 1
     * @return  &peer.http.HttpResponse response object
     * @see     rfc://2518
     */
    function &read($uri= NULL, $xml= NULL, $depth= '1') {     
      try(); {
        $c= &$this->getConnection($uri);
        $response= &$c->propfind(
          $xml,
          array(
            new Header('Depth', $depth)
          )
        );
      } if (catch('Exception', $e)) {
        return throw($e);
      }
      return $response;
    }
    
    /**
     * Do a Put on Webdav server
     *
     * @access  public
     * @param   &io.File file
     * @param   string uri, filename or directory
     * @return  &peer.http.HttpResponse response object
     * @see     rfc://2518
     */
    function &put(&$file, $uri= NULL) {    
      if (!$file->isOpen()) $file->open(FILE_MODE_READ);
      try(); {
        $c= &$this->getConnection($uri);
        $response= &$c->put(
          $file->read($file->size()),
          array(
            new Header('Content-Type', MimeType::getByFilename($uri))
          )
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
     * @param   string uri, filename or directory
     * @return  &peer.http.HttpResponse response object
     * @see     rfc://2518
     */
    function &get($uri= NULL) {    
      try(); {
        $c= &$this->getConnection($uri);
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
     * @param   string xml, The XML Representation of the Properties
     * @param   string uri, filename or directory
     * @return  &peer.http.HttpResponse response object
     * @see     rfc://2518
     */
    function &proppatch($xml, $uri= NULL) {          
      try(); {
        $c= &$this->getConnection($uri);
        $response= &$c->proppatch($xml);
      } if (catch('Exception', $e)) {
        return throw($e);
      }
      return $response;    
    }
        
    /**
     * Do a Copy Request
     *
     * @access  public
     * @param   string source
     * @param   string destination
     * @param   bool overwrite, default FALSE
     * @param   mixed depth, default Infinity
     * @return  &peer.http.HttpResponse response object
     * @see     rfc://2518
     */
    function copy($source, $destination, $overwrite= FALSE, $depth= 'Infinity') {        
      try(); {
        $c= &$this->getConnection($source);
        $response= &$c->copy(
          NULL,
          array(
            new Header('Overwrite', $overwrite ? 'T' : 'F'),
            new Header('Destination', $destination),
            new Header('Depth', $depth)
          )
        );
      } if (catch('Exception', $e)) {
        return throw($e);
      }
      return $response;      
    }
    
    /**
     * Do a Move Request
     *
     * @access  public 
     * @param   string source
     * @param   string destination
     * @param   bool overwrite, default FALSE
     * @return  &peer.http.HttpResponse response object
     * @see     rfc://2518
     */
    function move($source, $destination, $overwrite= FALSE) {   
      try(); {
        $c= &$this->getConnection($source);
        $response= &$c->move(
          NULL,
          array(
            new Header('Overwrite', $overwrite ? 'T' : 'F'),
            new Header('Destination', $destination)
          )
        );
      } if (catch('Exception', $e)) {
        return throw($e);
      }
      return $response;     
    }
    
    /**
     * Do a Lock Request
     *
     * @access  public  
     * @param   string uri The uri of the collection or file
     * @param   string xml, The XML of the lockrequest
     * @return  &peer.http.HttpResponse response object
     * @see     rfc://2518
     */
    function lock($uri, $xml) {    
      try(); {
        $c= &$this->getConnection($uri);
        $response= &$c->lock(
          $xml,
          array(
            new Header('Timeout', 'Infinity'),
            new Header('Content-Type', 'text/xml'),
            new Header('Content-Length', strlen($xml))
          )
        );
      } if (catch('Exception', $e)) {
        return throw($e);
      }
      return $response;   
    }  
    
    /**
     * Do a Unlock Request
     *
     * @access  public
     * @param   string uri, filename or directory
     * @param   string locktoken
     * @return  &peer.http.HttpResponse response object
     * @see     rfc://2518
     */
    function unlock($uri, $locktoken) {    
      try(); {
        $c= &$this->getConnection($uri);
        $response= &$c->unlock(
          NULL,
          array(
            new Header('Lock-Token', $locktoken)
          )
        );
      } if (catch('Exception', $e)) {
        return throw($e);
      }
      return $response;  
    }
    
    /**
     * Do a Delete Request
     *
     * @access  public  
     * @param   string uri, filename or directory
     * @return  &peer.http.HttpResponse response object
     * @see     rfc://2518
     */
    function delete($uri) {    
      try(); {
        $c= &$this->getConnection($uri);
        $response= &$c->delete();
      } if (catch('Exception', $e)) {
        return throw($e);
      }
      return $response;  
    }
    
  }
?>
