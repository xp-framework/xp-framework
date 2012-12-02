<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'webservices.rest.srv.RestContext',
    'webservices.rest.srv.RestRoute',
    'webservices.rest.srv.CookieParamSource',
    'webservices.rest.srv.HeaderParamSource',
    'webservices.rest.srv.QueryParamSource',
    'webservices.rest.srv.PathParamSource',
    'webservices.rest.srv.BodyParamSource',
    'webservices.rest.RestDeserializer',
    'scriptlet.Preference'
  );

  /**
   * Abstract base class
   *
   * @test  xp://net.xp_framework.unittest.webservices.rest.srv.AbstractRestRouterTest
   */
  class AbstractRestRouter extends Object {
    protected $cat= NULL;
    protected $routes= array();
    protected $input= array();
    protected $output= array();

    /**
     * Configure router. Template method - overwrite and implement in subclasses!
     * 
     * @param  string setup
     * @param  string base The base URL
     */
    public function configure($setup, $base= '') {
    }

    /**
     * Sets input formats
     *
     * @param  string[] supported order by preference
     */
    public function setInputFormats($supported) {
      $this->input= $supported;
    }

    /**
     * Gets input formats
     *
     * @return string[]
     */
    public function getInputFormats() {
      return $this->input;
    }

    /**
     * Sets output formats
     *
     * @param  string[] supported order by preference
     */
    public function setOutputFormats($supported) {
      $this->output= $supported;
    }

    /**
     * Gets output formats
     *
     * @return string[]
     */
    public function getOutputFormats() {
      return $this->output;
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
     * @return  var[]
     */
    public function targetsFor($verb, $path, $type, Preference $accept) {
      if (!isset($this->routes[$verb])) return array();   // Short-circuit

      // Figure out matching routes
      $path= rtrim($path, '/');
      $matching= $order= array();
      foreach ($this->routes[$verb] as $route) {
        if (!preg_match($route->getPattern(), $path, $segments)) continue;

        // Check input type if specified by client
        if (NULL !== $type) {
          $pref= new Preference($route->getAccepts($this->input));
          if (NULL === ($input= $pref->match(array($type)))) continue;
          $q= $pref->qualityOf($input, 6);
        } else {
          $input= NULL;
          $q= 0.0;
        }

        // Check output type
        if (NULL === ($output= $accept->match($route->getProduces($this->output)))) continue;

        // Found possible candidate
        $matching[]= array(
          'target'   => $route->getTarget(), 
          'params'   => $route->getParams(),
          'segments' => $segments,
          'input'    => $input,
          'output'   => $output
        );
        $order[sizeof($matching)- 1]= $q + $accept->qualityOf($output, 6);
      }

      // Sort by quality
      arsort($order, SORT_NUMERIC);
      $return= array();
      foreach ($order as $offset => $q) {
        $return[]= $matching[$offset];
      }
      return $return;
    }

    /**
     * Read arguments from request
     *
     * @param  [:var] target
     * @param  scriptlet.Request request
     * @return var[] args
     */
    public function argumentsFor($target, $request) {
      $args= array();
      foreach ($target['target']->getParameters() as $parameter) {
        $param= $parameter->getName();

        // Extract arguments according to definition. In case we don't have an explicit
        // source for an argument, look up according to the following rules:
        //
        // * If we have a segment named exactly like the parameter, use it
        // * If there is no incoming payload, check the parameters
        // * If there is an incoming payload, use that.
        //
        // Handle explicitely configured sources first.
        if (isset($target['params'][$param])) {
          $src= $target['params'][$param];
        } else if (isset($target['segments'][$param])) {
          $src= new PathParamSource($param);
        } else if (NULL === $target['input']) {
          $src= new QueryParamSource($param);
        } else {
          $src= new BodyParamSource();
        }

        if (NULL === ($arg= $src->read($parameter->getType(), $target, $request))) {
          if ($parameter->isOptional()) {
            $arg= $src->convert($parameter->getType(), $parameter->getDefaultValue());
          } else {
            throw new IllegalArgumentException('Parameter "'.$param.'" required but found in '.$src->toString());
          }
        }
        $args[]= $arg;
      }
      return $args;
    }

    /**
     * Process a target with a given request
     *
     * @param  [:var] target
     * @param  scriptlet.Request request
     * @param  webservices.rest.srv.Context context
     * @return webservices.rest.srv.Response
     */
    public function process($target, $request, $context) {
      return $context->handle(
        $context->handlerInstanceFor($target['target']->getDeclaringClass()), 
        $target['target'],
        $this->argumentsFor($target, $request)
      );
    }

    /**
     * Creates a string representation
     *
     * @return  string
     */
    public function toString() {
      return $this->getClassName().'@'.xp::stringOf($this->routes);
    }
  }
?>