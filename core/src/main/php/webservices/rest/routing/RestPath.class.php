<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'util.Hashmap',
    'webservices.rest.routing.RestRouter',
    'webservices.rest.routing.RestMethodRoute'
  );
  
  /**
   * Represent REST resource path
   *
   * @test xp://net.xp_framework.webservices.rest.routing.RestPathTest
   * @purpose Path
   */
  class RestPath extends Object {
    protected $names= array();
    protected $pattern= '';
    protected $params= array();
    protected $query= array();
    
    /**
     * Constructor
     * 
     * @param string path The path
     */
    public function __construct($path) {
      static $search= '/\{(\w*)\}/';
      static $replace= '(\w*)';
      
      $this->path= $path;
      
      // Check if placeholders are in path
      if (preg_match_all($search, $this->path, $names)) {
        $this->names= $names[1];
        $this->pattern= preg_replace($search, $replace, $this->path);
      } else {
        $this->pattern= $this->path;
      }
    }
    
    /**
     * Match the given URL
     * 
     * @param string path The URL path
     * @return bool
     */
    public function match($path) {
      $this->params= array();
      
      if (preg_match('#^'.$this->pattern.'#', $path, $matches)) {
        array_shift($matches);
        
        foreach ($this->names as $i => $name) {
          $this->params[$name]= $matches[$i];
        }
        
        return TRUE;
      }
      
      return FALSE;
    }
    
    /**
     * Return path parameter
     * 
     * @param string name The parameter name
     */
    public function getPathParam($name) {
      return isset($this->params[$name]) ? $this->params[$name] : NULL;
    }
    
    /**
     * Return query parameter
     * 
     * @param string name The parameter name
     */
    public function getQueryParam($name) {
      return isset($this->query[$name]) ? $this->query[$name] : NULL;
    }
  }
?>
