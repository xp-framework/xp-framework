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
  
    var
      $user=     NULL,
      $path=     NULL,
      $rootURI=  NULL,
      $tree=     NULL;

    /**
     * Set user
     *
     * @access  public
     * @param   string user
     */
    function setUser(&$user) {
      $this->user= &$user;
    }

    /**
     * Get user
     *
     * @access  public
     * @return  string
     */
    function &getUser() {
      return $this->user;
    }

    /**
     * Set the path
     * 
     * @access public
     * @param  string path The path
     */
    function setPath($path) {
      $this->path= $path;
    }
    
    /**
     * Retrieve path part of the request URI (e.g.
     * http://webdav.host.com/fs/file.txt -> file.txt)
     *
     * @access  public
     * @return  string
     */
    function getPath() {
      return $this->path;
    }

    /**
     * Sets the root URL of the webdav resource (e.g. http://webdav.server.tld/dav/)
     * without the complete path. This contains only the URL where the WebDav
     * service is connected to.
     *
     * @access private
     * @param  string uri The URL object
     */
    function setRootURL($url) {
      $this->rootURL= $url;
    }
    
    /**
     * Returns URI to Webdav resource
     *
     * @access public
     * @return peer.URL
     */
    function getRootURL() {
      return $this->rootURL;
    }
    
    function encodePath($path) {
      $parts = explode('/', $path);
      for ($i = 0; $i < count($parts); $i++) $parts[$i]= rawurlencode($parts[$i]);
      return implode('/', $parts);
    }
    
    function decodePath($path) {
      $parts = explode('/', $path);
      for ($i = 0; $i < count($parts); $i++) $parts[$i]= rawurldecode($parts[$i]);
      return implode('/', $parts);
    }

    /**
     * Convert absolute URL to relative path:
     *   "http://webdav.host.com/fs/dir/file.txt" -> "dir/file.txt"
     *
     * @access  public
     * @param   string url
     * @return  string
     */
    function getRelativePath($url) {
      $url= &new URL($url);
      return $this->decodePath(substr(
        rawurldecode($url->getPath()),
        strlen($this->rootURL->getPath())
      ));
    }
    
    /**
     * Set request's data and try to parse the request body (if available)
     *
     * @access private
     * @param  string  data The request's data
     */
    function setData(&$data) {
      parent::setData($data);
      
      try(); {
        $this->tree= Tree::fromString($data);
      } if (catch('Exception', $e)) {
        // Catch exception which occur when request body contains binary
        // data (e.g. when PUTting files)
      }
    }
    
    /**
     * Search for specific node using simple path string (e.g. "/propfind/set")
     *
     * @access public
     * @param  string path The path string
     * @return xml.Node
     */
    function &getNode($path) {
      if (!$this->tree || $this->tree->root === NULL) return NULL;
      $node= &$this->tree->root;
      $parts= explode('/', $path);
      array_shift($parts);
      if (array_shift($parts) != $node->getName()) return NULL;
      foreach($parts as $p) {
        $found= FALSE;
        $name= array_shift($parts);
        for($i= 0, $s= sizeof($node->children); $i<$s; $i++) {
          if ($name != $node->children[$i]->getName()) continue;
          
          $node= &$node->children[$i];
          $found= TRUE;
          break;
        }
        if (!$found) return NULL;
      }
      return $node;
    }
  
  }
?>
