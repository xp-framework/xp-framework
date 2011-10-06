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
     * Return items
     * 
     * @return webservices.rest.routing.RestRoutingItem
     */
    public function getItems() {
      return $this->routings;
    }
    
    /**
     * Add new route
     * 
     * @param string verb The verb to add route for
     * @param string path The path to add route for
     * @param webservices.rest.routing.RestRoute route The target route to apply
     * @param webservices.rest.routing.RestRoutingArgs args The arguments
     */
    public function addRoute($verb, $path, RestRoute $target, RestRoutingArgs $args= NULL) {
      $verb= strtoupper($verb);
      
      $this->routings[]= new RestRoutingItem($verb, new RestPath($path), $target, $args);
    }
    
    /**
     * Check if route exists for given method and path
     * 
     * @param string verb The verb
     * @param string path The path
     * @return bool
     */
    public function hasRoutings($verb, $path) {
      return sizeof($this->getRoutings($verb, $path)) > 0;
    }
    
    /**
     * Return route for given verb and path
     * 
     * @param string verb The verb
     * @param string path The path
     * @return webservices.rest.routing.RestRoutingItem[]
     */
    public function getRoutings($verb, $path) {
      $verb= strtoupper($verb);
      
      $routings= array();
      foreach ($this->routings as $item) {
        if ($item->appliesTo($verb, $path)) $routings[]= $item;
      }
      
      return $routings;
    }
  }
?>
