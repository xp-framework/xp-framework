<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'util.Hashmap',
    'webservices.rest.routing.RestPath',
    'webservices.rest.routing.RestRouter',
    'webservices.rest.routing.RestMethodRoute'
  );
  
  /**
   * REST router based on class and method annotations
   *
   * @test    xp://net.xp_framework.unittest.rest.RestAnnotationRouterTest
   * @purpose Router
   */
  class RestAnnotationRouter extends Object implements RestRouter {
    protected $map= array();
    
    /**
     * Configure router
     * 
     * @param string setup The setup string
     */
    public function configure($setup) {
      $package= Package::forName($setup);
      
      foreach ($package->getClasses() as $handler) {
        if (!$handler->hasAnnotation('webservice')) continue;
        
        foreach ($handler->getMethods() as $method) {
          if (
            !$method->hasAnnotation('webmethod', 'method') ||
            !$method->hasAnnotation('webmethod', 'path')
          ) continue;
          
          $this->map[strtoupper($method->getAnnotation('webmethod', 'method'))][]= array(
            'path'   => new RestPath($method->getAnnotation('webmethod', 'path')),
            'method' => $method
          );
        }
      }
    }
    
    /**
     * Retrieve route for request
     * 
     * @param webservices.rest.transport.HttpRequestAdapter request The request
     */
    protected function route($method, $path) {
      $method= strtoupper($method);
      
      if (isset($this->map[$method])) foreach ($this->map[$method] as $map) {
        if ($map['path']->match($path)) return $map;
      }
      
      return NULL;
    }
    
    /**
     * Test if route exists
     * 
     */
    public function hasRoutesFor($method, $path) {
      return $this->route($method, $path) !== NULL;
    }
    
    /**
     * Return routes for given request and response
     * 
     * @param webservices.rest.transport.HttpRequestAdapter request The request
     * @param webservices.rest.transport.HttpResponseAdapter response The response
     * @return webservices.rest.RestRoute[]
     */
    public function routesFor($request, $response) {
      if ($route= $this->route($request)) return array(new RestMethodRoute(
        $route['path'],
        $route['method']
      ));
      
      return array();
    }
  }
?>
