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
  abstract class AbstractRestRouter extends Object {
    protected $routes= array();
    
    /**
     * Configure router
     * 
     * @param  string setup
     * @param  string base The base URL
     */
    public abstract function configure($setup, $base= '');

    /**
     * Add a route
     *
     * @param  string verb one of the HTTP verbs
     * @param  webservices.rest.server.routing.RestRoute route
     */
    public function addRoute($verb, RestRoute $route) {
      if (!isset($this->routes[$verb])) $this->routes[$verb]= array();
      $this->routes[$verb][]= $route;
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
          'output'   => $route->getReturns()
        );
      }
      return $matching;
    }
  }
?>
