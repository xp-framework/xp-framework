<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'scriptlet.HttpScriptlet',
    'webservices.rest.routing.RestRoutingProcessor',
    'webservices.rest.transport.HttpRequestAdapterFactory',
    'webservices.rest.transport.HttpResponseAdapterFactory'
  );
  
  /**
   * REST HTTP scriptlet
   *
   * @test    xp://net.xp_framework.unittest.scriptlet.HttpScriptletProcessTest
   */
  class RestHttpScriptlet extends HttpScriptlet {
    protected $router= NULL;
    protected $base= '';
    
    /**
     * Constructor
     * 
     * @param string package The package containing handler classes
     * @param string router The router to use
     * @param string base The base URL (will be stripped off from request url)
     */
    public function __construct($package, $router, $base= '') {
      $this->router= XPClass::forName($router)->newInstance();
      $this->router->configure($package, $base);
      $this->base= rtrim($base, '/');
    }
    
    /**
     * Do request processing
     * 
     * @param scriptlet.http.HttpScriptletRequest request The request
     * @param scriptlet.http.HttpScriptletResponse response The response
     */
    public function doProcess($request, $response) {
      $req= HttpRequestAdapterFactory::forRequest($request)->newInstance($request);
      $res= HttpResponseAdapterFactory::forRequest($request)->newInstance($response);
      
      $routings= $this->router->routesFor($req, $res);
      
      // Setup processor and bind data sources
      $processor= new RestRoutingProcessor();
      $processor->bind('webservices.rest.transport.HttpRequestAdapter', $req);
      $processor->bind('webservices.rest.transport.HttpResponseAdapter', $res);
      $processor->bind('payload', $req->getData());
      
      $routed= FALSE;
      $errors= array();
      for ($i= 0, $s= sizeof($routings); $i<$s && !$routed; $i++) {
        $routing= $routings[$i];
        
        try {
          $res->setData($processor->execute(
            $routing,
            $routing->getPath()->match(substr($req->getPath(), strlen($this->base)))
          ));
          $routed= TRUE;
        } catch (ClassCastException $e) {
          // When casting arguments, try next route and register error
          $errors[]= sprintf(
            '%s ~ %s (%s)',
            $routing->getTarget()->toString(),
            $e->getClassName(),
            $e->getMessage()
          );
        }
      }
      
      if (!$routed)  throw new IllegalStateException(
        'Can not route '.$req->getMethod().' request '.$req->getPath()." [\n  ".
        implode($errors, "\n  ").
        "\n]"
      );
    }
    
    /**
     * Calculate method to invoke
     *
     * @param   scriptlet.HttpScriptletRequest request 
     * @return  string
     */
    public function handleMethod($request) {
      
      // Setup request
      parent::handleMethod($request);
      
      // We want to handle all request at one place
      return 'doProcess';
    }
  }
?>
