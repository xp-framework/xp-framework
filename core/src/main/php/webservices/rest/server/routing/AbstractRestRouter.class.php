<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('webservices.rest.server.routing.RestRoute');

  /**
   * Abstract base class
   *
   */
  class AbstractRestRouter extends Object {
    protected $routes= array();
    
    /**
     * Configure router. Template method - overwrite and implement in subclasses!
     * 
     * @param  string setup
     * @param  string base The base URL
     */
    public function configure($setup, $base= '') {
    }

    /**
     * Add a route
     *
     * @param   webservices.rest.server.routing.RestRoute route
     * @return  webservices.rest.server.routing.RestRoute The added route
     */
    public function addRoute(RestRoute $route) {
      $verb= $route->getVerb();
      if (!isset($this->routes[$verb])) $this->routes[$verb]= array();
      $this->routes[$verb][]= $route;
      return $route;
    }

    /**
     * Returns all routes
     *
     * @return  webservices.rest.server.routing.RestRoute[]
     */
    public function allRoutes() {
      $r= array();
      foreach ($this->routes as $verb => $routes) {
        $r= array_merge($r, $routes);
      }
      return $r;
    }

    /**
     * Return routes for given request and response
     * 
     * @param   scriptlet.http.HttpScriptletRequest request The request
     * @return  [:var]
     */
    public function routesFor($request) {
      $verb= $request->getMethod();
      if (!isset($this->routes[$verb])) return FALSE;

      // Figure out matching routes
      $path= rtrim($request->getURL()->getPath(), '/');
      $matching= array();
      foreach ($this->routes[$verb] as $route) {
        if (!preg_match($route->getPath(), $path, $segments)) continue;
        $matching[]= array(
          'target'   => $route->getTarget(), 
          'segments' => $segments,
          'input'    => $route->getAccepts(),
          'output'   => $route->getProduces()
        );
      }
      return $matching;
    }
  }
?>
