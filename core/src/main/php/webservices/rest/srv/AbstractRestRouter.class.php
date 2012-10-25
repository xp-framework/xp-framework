<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('webservices.rest.srv.RestRoute', 'webservices.rest.RestDeserializer', 'scriptlet.Preference');

  /**
   * Abstract base class
   *
   */
  class AbstractRestRouter extends Object {
    protected $routes= array();
    protected $input= array();
    protected $output= array();
    protected $convert= NULL;

    /**
     * Creates converter
     *
     */
    public function __construct() {
      $this->convert= newinstance('webservices.rest.RestDeserializer', array(), '{
        public function deserialize($in, $target) {
          throw new IllegalStateException("Unused");
        }
      }');
    }
    
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
     * Handle routing item
     *
     * @param  var target
     * @param  var[] args
     * @return webservices.rest.srv.Response
     * @throws scriptlet.HttpScriptletException
     */
    public function handle($target, $args) {
      $this->cat && $this->cat->debug('->', $target);

      // Instantiate the handler class and invoke method
      $instance= $target['target']->getDeclaringClass()->newInstance();
      try {
        $result= $target['target']->invoke($instance, $args);
        $this->cat && $this->cat->debug('<-', $result);
      } catch (TargetInvocationException $t) {
        $this->cat && $this->cat->warn('<-', $t);
        throw new HttpScriptletException($t->getCause()->getMessage(), HttpConstants::STATUS_BAD_REQUEST, $t);
      }

      // For "VOID" methods, set status to "no content". If a response is returned, 
      // use its status, headers and payload. For any other methods, set status to "OK".
      if (Type::$VOID->equals($target['target']->getReturnType())) {
        $res= Response::status(HttpConstants::STATUS_NO_CONTENT);
      } else if ($result instanceof Response) {
        $res= $result;
      } else {
        $res= Response::status(HttpConstants::STATUS_OK)->withPayload($result);
      }
      return $res;
    }

    public function process($target, $request, $format) {
      return $this->handle($target, $this->argumentsFor($target, $request, $format));
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