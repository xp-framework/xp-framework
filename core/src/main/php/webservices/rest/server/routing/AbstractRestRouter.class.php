<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('webservices.rest.server.routing.RestRoute', 'scriptlet.Preference');

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
     * @param   string verb
     * @param   string path
     * @param   string type The Content-Type, or NULL
     * @param   scriptlet.Preference accept the "Accept" header's contents
     * @param   string[] supported
     * @return  [:var]
     */
    public function routesFor($verb, $path, $type, Preference $accept, array $supported= array()) {
      if (!isset($this->routes[$verb])) return array();   // Short-circuit

      // Figure out matching routes
      $path= rtrim($path, '/');
      $matching= array();
      foreach ($this->routes[$verb] as $route) {
        if (!preg_match($route->getPath(), $path, $segments)) continue;

        // Check input type if specified by client
        if (NULL !== $type) {
          $before= $route->getAccepts() !== NULL;
          if (NULL === ($input= create(new Preference($route->getAccepts('*/*')))->match(array($type)))) continue;
        } else {
          $before= FALSE;
          $input= NULL;
        }

        // Check output type
        if (NULL === ($output= $accept->match($route->getProduces($supported)))) continue;

        // Found possible candidate
        $candidate= array(
          'target'   => $route->getTarget(), 
          'segments' => $segments,
          'input'    => $input,
          'output'   => $output
        );
        $before ? array_unshift($matching, $candidate) : $matching[]= $candidate;
      }
      return $matching;
    }
  }
?>
