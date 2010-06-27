<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
 
  uses('scriptlet.HttpScriptletRequest');

  /**
   * Webdav scriptlet request
   *
   * @purpose  Request object
   */
  class WebdavScriptletRequest extends HttpScriptletRequest {
  
    public
      $user=     NULL,
      $path=     NULL,
      $rootURI=  NULL,
      $tree=     NULL;


    /**
     * Decode string in the right encoding (currently UTF-8 is used)
     *
     * @param  string string The string which should be encoded
     * @return string
     */    
    public function decode($string) {
      return utf8_decode($string);
    }
    
    /**
     * Set user
     *
     * @param   string user
     */
    public function setUser($user) {
      $this->user= $user;
    }

    /**
     * Get user
     *
     * @return  string
     */
    public function getUser() {
      return $this->user;
    }

    /**
     * Set the path
     * 
     * @param  string path The path
     */
    public function setPath($path) {
      $this->path= $path;
    }
    
    /**
     * Retrieve path part of the request URI (e.g.
     * http://webdav.host.com/fs/file.txt -> file.txt)
     *
     * @return  string
     */
    public function getPath() {
      return rtrim($this->path, "/");
    }

    /**
     * Sets the root URL of the webdav resource (e.g. http://webdav.server.tld/dav/)
     * without the complete path. This contains only the URL where the WebDav
     * service is connected to.
     *
     * @param  string uri The URL object
     */
    public function setRootURL($url) {
      $this->rootURL= $url;
    }
    
    /**
     * Returns URI to Webdav resource
     *
     * @return peer.URL
     */
    public function getRootURL() {
      return $this->rootURL;
    }
    
    /**
     * Decode the parts of a path 
     *
     * Example:
     * <pre>
     *   "/Test%20Folder/file.txt" -> "/Test Folder/file.txt"
     * </pre>
     *
     * @param   string path The path
     * @return  string
     * @see org.webdav.WebdavScriptletResponse#encodePath
     */
    public function decodePath($path) {
      $parts = explode('/', $path);
      for ($i = 0; $i < sizeof($parts); $i++) $parts[$i]= rawurldecode($parts[$i]);
      return implode('/', $parts);
    }

    /**
     * Convert absolute URL to relative path:
     *   "http://webdav.host.com/fs/dir/file.txt" -> "dir/file.txt"
     *
     * @param   string url
     * @return  string
     */
    public function getRelativePath($url) {
      $url= new URL($url);
      return $this->decodePath(substr(
        rawurldecode($url->getPath()),
        strlen($this->rootURL->getPath())
      ));
    }
    
    /**
     * Set the absolute Uri of requested directory
     *
     * @param   string uri (e.g. /path/to/resource/directory/test.txt => /path/to/resource/directory/)
     */
    public function setAbsoluteURI($uri) {
      $this->absoluteUri= is_file($uri) ? dirname($uri) : $uri;
    }
    
    /**
     * Retrieve the absolute Uri of requested directory
     *
     * @return  string uri
     */
    public function getAbsoluteURI() {
      return $this->absoluteUri;
    }
    
    /**
     * Set request's data and try to parse the request body (if available)
     *
     * @param  string data The request's data
     */
    public function setData($data) {
      parent::setData($data);
      
      try {
        $this->tree= Tree::fromString($data);
      } catch (Exception $e) {
        // Catch exception which occur when request body contains binary
        // data (e.g. when PUTting files)
      }
    }
    
    /**
     * Search for specific node using simple path string (e.g. "/propfind/set")
     *
     * @param  string path The path string
     * @return &xml.Node
     */
    public function getNode($path) {

      if (!$this->tree || $this->tree->root === NULL) return NULL;
      $node= $this->tree->root;
      $parts= explode('/', $path);
      array_shift($parts);
      if (array_shift($parts) != $node->getName()) return NULL;
      foreach ($parts as $p) {
        $found= FALSE;
        $name= array_shift($parts);
        for ($i= 0, $s= sizeof($node->children); $i<$s; $i++) {
          if ($name != $node->children[$i]->getName()) continue;
          
          $node= $node->children[$i];
          $found= TRUE;
          break;
        }
        if (!$found) return NULL;
      }
      return $node;
    }
    
    /**
     * Returns the given Namespaceprefix
     *
     * @return  string prefix; (e.g. skunk), default "D"
     */
    public function getNamespacePrefix() {
      $ns= explode(":", key($this->tree->root->attribute));
      return empty($ns[1]) ? 'D' : $ns[1];
    }
  
  }
?>
