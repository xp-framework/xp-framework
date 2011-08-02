<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'util.Hashmap',
    'util.NoSuchElementException',
    'webservices.rest.routing.RestRoutingItem'
  );
  
  /**
   * REST routing table
   *
   * @test    xp://net.xp_framework.unittest.rest.routing.RestRoutingTest
   * @purpose Routing table
   */
  class RestRouting extends Object {
    protected $routings= array();
    
    /**
     * Add new route
     * 
     * @param string method The method to add route for
     * @param string path The path to add route for
     * @param webservices.rest.routing.RestRoute route The target route to apply
     * @param webservices.rest.routing.RestRoutingArgs args The arguments
     */
    public function addRoute($method, $path, RestRoute $target, RestRoutingArgs $args= NULL) {
      $method= strtoupper($method);
      
      $this->routings[]= new RestRoutingItem($method, new RestPath($path), $target, $args);
    }
    
    /**
     * Check if route exists for given method and path
     * 
     * @param string method The method
     * @param string path The path
     * @return bool
     */
    public function hasRouting($method, $path) {
      $method= strtoupper($method);
      
      foreach ($this->routings as $item) {
        if ($item->appliesTo($method, $path)) return TRUE;
      }
      
      return FALSE;
    }
    
    /**
     * Return route for given method and path
     * 
     * @param string method The method
     * @param string path The path
     * @return webservices.rest.routing.RestRoutingItem
     */
    public function getRouting($method, $path) {
      $method= strtoupper($method);
      
      foreach ($this->routings as $item) {
        if ($item->appliesTo($method, $path)) return $item;
      }
      
      throw new NoSuchElementException('No route found for '.$path);
    }
  }
?>
