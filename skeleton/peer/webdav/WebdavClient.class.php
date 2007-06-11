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
   *   } if (catch('Exception', $e)) {
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
    public 
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
     * @param   mixed url either a string or a peer.URL object
     */
    public function __construct($url) {
      if (!is('URL', $url)) $this->url= new URL($url); else $this->url= $url;
    }
    
    /**
     * Get a Connection
     *
     * @param   string uri
     * @return  peer.webdav.WebdavConnection
     */
    public function getConnection($uri= NULL) {
      $url= new URL($this->url->getURL().$this->path.($uri === NULL ? '' : '/'.$uri));
      return new WebdavConnection($url);
    }
    
    /**
     * Helper Method to set the path
     *
     * @param   string path
     */
    public function setPath($path) {
      $this->path= $path;
    }
    
    /**
     * Do a Head Request to check if file exists
     *
     * @param   string uri, filename or directory
     * @return  peer.http.HttpResponse response object
     * @see     rfc://2518
     */
    public function exists($uri) {    
      try {
        $c= $this->getConnection($uri);
        $response= $c->head();
      } catch (Exception $e) {
        throw($e);
      }
      return $response;  
    }
    
    /**
     * Do a Propfind on Webdav server
     *
     * @param   string uri, filename or directory
     * @param   string xml, The XML of the Propfind Request (e.g. to select properties)  
     * @param   int depth, default 1
     * @return  peer.http.HttpResponse response object
     * @see     rfc://2518
     */
    public function read($uri= NULL, $xml= NULL, $depth= '1') {     
      try {
        $c= $this->getConnection($uri);
        $response= $c->propfind(
          $xml,
          array(
            new Header('Depth', $depth)
          )
        );
      } catch (Exception $e) {
        throw($e);
      }
      return $response;
    }
    
    /**
     * Do a Put on Webdav server
     *
     * @param   io.File file
     * @param   string uri, filename or directory
     * @return  peer.http.HttpResponse response object
     * @see     rfc://2518
     */
    public function put($file, $uri= NULL) {  
      // If no uri or filename is specified, take the original filename  
      if ($uri === NULL) $uri= $file->getFilename();
            
      // Encode uri to handle files/directories containing spaces
      $uri= rawurlencode($uri);    
      
      if (!$file->isOpen()) $file->open(FILE_MODE_READ);
      try {
        $c= $this->getConnection($uri);
        $response= $c->put(
          $file->read($file->size()),
          array(
            new Header('Content-Type', MimeType::getByFilename($uri))
          )
        );
      } catch (Exception $e) {
        throw($e);
      }
      return $response;
    }
    
    /**
     * Do a Get on Webdav server
     *
     * @param   string uri, filename or directory
     * @return  peer.http.HttpResponse response object
     * @see     rfc://2518
     */
    public function get($uri= NULL) {    
      try {
        $c= $this->getConnection($uri);
        $response= $c->get();
      } catch (Exception $e) {
        throw($e);
      }
      return $response;
    }
    
    /**
     * Do a Proppatch request
     *
     * @param   string xml, The XML Representation of the Properties
     * @param   string uri, filename or directory
     * @return  peer.http.HttpResponse response object
     * @see     rfc://2518
     */
    public function proppatch($xml, $uri= NULL) {          
      try {
        $c= $this->getConnection($uri);
        $response= $c->proppatch($xml);
      } catch (Exception $e) {
        throw($e);
      }
      return $response;    
    }
    
    /**
     * Do a MkCol Request
     *
     * @param   string uri, The uri of the new collection
     * @return  peer.http.HttpResponse response object
     * @see     rfc://2518
     */
    public function mkcol($uri) {        
      try {
        $c= $this->getConnection($uri);
        $response= $c->mkcol();
      } catch (Exception $e) {
        throw($e);
      }
      return $response;      
    }
        
    /**
     * Do a Copy Request
     *
     * @param   string source
     * @param   string destination
     * @param   bool overwrite, default FALSE
     * @param   mixed depth, default Infinity
     * @return  peer.http.HttpResponse response object
     * @see     rfc://2518
     */
    public function copy($source, $destination, $overwrite= FALSE, $depth= 'Infinity') {        
      try {
        $c= $this->getConnection($source);
        $response= $c->copy(
          NULL,
          array(
            new Header('Overwrite', $overwrite ? 'T' : 'F'),
            new Header('Destination', $destination),
            new Header('Depth', $depth)
          )
        );
      } catch (Exception $e) {
        throw($e);
      }
      return $response;      
    }
    
    /**
     * Do a Move Request
     *
     * @param   string source
     * @param   string destination
     * @param   bool overwrite, default FALSE
     * @return  peer.http.HttpResponse response object
     * @see     rfc://2518
     */
    public function move($source, $destination, $overwrite= FALSE) {   
      try {
        $c= $this->getConnection($source);
        $response= $c->move(
          NULL,
          array(
            new Header('Overwrite', $overwrite ? 'T' : 'F'),
            new Header('Destination', $destination)
          )
        );
      } catch (Exception $e) {
        throw($e);
      }
      return $response;     
    }
    
    /**
     * Do a Lock Request
     *
     * @param   string uri The uri of the collection or file
     * @param   string xml, The XML of the lockrequest
     * @return  peer.http.HttpResponse response object
     * @see     rfc://2518
     */
    public function lock($uri, $xml) {    
      try {
        $c= $this->getConnection($uri);
        $response= $c->lock(
          $xml,
          array(
            new Header('Timeout', 'Infinity'),
            new Header('Content-Type', 'text/xml'),
            new Header('Content-Length', strlen($xml))
          )
        );
      } catch (Exception $e) {
        throw($e);
      }
      return $response;   
    }  
    
    /**
     * Do a Unlock Request
     *
     * @param   string uri, filename or directory
     * @param   string locktoken
     * @return  peer.http.HttpResponse response object
     * @see     rfc://2518
     */
    public function unlock($uri, $locktoken) {    
      try {
        $c= $this->getConnection($uri);
        $response= $c->unlock(
          NULL,
          array(
            new Header('Lock-Token', $locktoken)
          )
        );
      } catch (Exception $e) {
        throw($e);
      }
      return $response;  
    }
    
    /**
     * Do a Delete Request
     *
     * @param   string uri, filename or directory
     * @return  peer.http.HttpResponse response object
     * @see     rfc://2518
     */
    public function delete($uri) {    
      try {
        $c= $this->getConnection($uri);
        $response= $c->delete();
      } catch (Exception $e) {
        throw($e);
      }
      return $response;  
    }
    
    /**
     * Activate VersionControl on a file
     *
     * @param   string uri, filename or directory
     * @return  peer.http.HttpResponse response object
     * @see     rfc://2518
     */
    public function version($uri) {    
      try {
        $c= $this->getConnection($uri);
        $response= $c->version();
      } catch (Exception $e) {
        throw($e);
      }
      return $response;  
    }
    
    /**
     * Do a Report Request
     *
     * @param   string uri, filename or directory
     * @return  peer.http.HttpResponse response object
     * @see     rfc://2518
     */
    public function report($uri) {    
      try {
        $c= $this->getConnection($uri);
        $response= $c->report();
      } catch (Exception $e) {
        throw($e);
      }
      return $response;  
    }
    
  }
?>
