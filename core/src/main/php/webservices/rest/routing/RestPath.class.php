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
      $pattern= '',
      $params= array();
    
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
          $this->setParam($name, $matches[$i]);
        }
        
        return TRUE;
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
     * Set parameter
     * 
     * @param name The parameter name
     * @param string value The parameter value
     */
    public function setParam($name, $value) {
      if (!in_array($name, $this->names)) throw new IllegalArgumentException(
        'Parameter '.$name.' does not exist in path: '.$this->path
      );
      
      $this->params[$name]= $value;
    }
    
    /**
     * Return path parameter
     * 
     * @param string name The parameter name
     */
    public function getParam($name) {
      return isset($this->params[$name]) ? $this->params[$name] : NULL;
    }
  }
?>
