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
      $url= NULL,
      $path= '';
  
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
     * @param   int depth, default 1
     * @return  &peer.http.HttpResponse response object
     */
    function &read($name= NULL, $depth= '1') {    
      try(); {
        $c= &$this->getConnection($name);
        $response= &$c->propfind('',
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
     * @param   string filename or directory
     * @return  &peer.http.HttpResponse response object
     */
    function &put(&$file, $name= NULL) {    
      if (!$file->isOpen()) $file->open(FILE_MODE_READ);
      try(); {
        $c= &$this->getConnection($name);
        $response= &$c->put(
          $file->read($file->size()),
          array(
            new Header('Content-Type', MimeType::getByFilename($name))
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
     * @param   string filename or directory
     * @return  &peer.http.HttpResponse response object
     */
    function &get($name= NULL) {
      try(); {
        $c= &$this->getConnection($name);
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
        $c= &$this->getConnection($name);
        $response= &$c->proppatch($properties);
      } if (catch('Exception', $e)) {
        return throw($e);
      }
      return $response;    
    }
  }
?>
