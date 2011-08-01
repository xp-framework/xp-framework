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
    protected
      $path= '',
      $names= array(),
      $pattern= '';
    
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
     * Match the given URL and return values matched
     * 
     * @param string path The URL path
     * @return mixed[]
     */
    public function match($path) {
      $params= array();
      
      if (preg_match('#^'.$this->pattern.'#', $path, $matches)) {
        array_shift($matches);
        
        foreach ($this->names as $i => $name) {
          $params[$name]= $matches[$i];
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
      return $this->names;
    }
    
    /**
     * Test if parameter exist
     * 
     * @param string name The name of parameter
     * @return bool
     */
    public function hasParam($name) {
      return in_array($name, $this->names);
    }
  }
?>
