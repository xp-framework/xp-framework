<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'util.Hashmap',
    'webservices.rest.server.routing.RestRouter',
    'webservices.rest.server.routing.RestMethodRoute'
  );
  
  /**
   * Represent REST resource path
   *
   * @test xp://net.xp_framework.webservices.rest.server.routing.RestPathTest
   * @purpose Path
   */
  class RestPath extends Object {
    protected
      $path= '',
      $query= array(),
      $names= array(),
      $pattern= '';
    
    /**
     * Constructor
     * 
     * @param string path The path
     */
    public function __construct($path) {
      static $search= '/\{([\w]*)\}/';
      static $replace= '([%\w]*)';
      
      list ($this->path, $this->query)= $this->splitParams($path);
      
      foreach ($this->query as $name => $param) {
        if (preg_match($search, $param, $matches)) {
          $this->query[$name]= $matches[1];
        } else {
          unset($this->query[$name]);
        }
      }
      
      // Check if placeholders are in path
      if (preg_match_all($search, $this->path, $names)) {
        $this->names= $names[1];
        $this->pattern= preg_replace($search, $replace, $this->path);
      } else {
        $this->pattern= $this->path;
      }
    }
    
    /**
     * Split path and query parameters
     * 
     * @param string path The path and query string
     * @return var[]
     */
    protected function splitParams($path) {
      $query= array();
      
      if (FALSE !== ($p= strpos($path, '?'))) {
        parse_str(substr($path, $p+1), $query);
        $path= substr($path, 0, $p-1);
      }
      
      return array($path, $query);
    }
    
    /**
     * Match the given URL and return values matched
     * 
     * @param string path The URL path
     * @return var[]
     */
    public function match($path) {
      list ($path, $query)= $this->splitParams($path);
      
      if (preg_match('#^'.$this->pattern.'$#', $path, $matches)) {
        array_shift($matches);
        
        $params= array();
        foreach ($this->names as $i => $name) {
          $params[$name]= rawurldecode($matches[$i]);
        }
        
        foreach ($this->query as $name => $param) {
          if (isset($query[$name])) $params[$param]= $query[$name];
        }
        
        return $params;
      }
      
      return FALSE;
    }
    
    /**
     * Return path
     * 
     * @return string
     */
    public function getPath() {
      return $this->path;
    }
    
    /**
     * Return parameter names
     * 
     */
    public function getParamNames() {
      return array_merge($this->names, array_values($this->query));
    }
    
    /**
     * Test if parameter exist
     * 
     * @param string name The name of parameter
     * @return bool
     */
    public function hasParam($name) {
      return in_array($name, $this->getParamNames());
    }
  }
?>
