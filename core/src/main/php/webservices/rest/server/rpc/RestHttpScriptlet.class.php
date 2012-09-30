<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'scriptlet.HttpScriptlet',
    'webservices.rest.server.routing.RestRoutingProcessor',
    'webservices.rest.server.transport.HttpRequestAdapterFactory',
    'webservices.rest.server.transport.HttpResponseAdapterFactory',
    'webservices.rest.server.RestErrorFormatter',
    'webservices.json.JsonDecoder'
  );
  
  /**
   * REST HTTP scriptlet
   *
   */
  class RestHttpScriptlet extends HttpScriptlet {
    protected 
      $router= NULL,
      $base= '',
      $errorFormatter= NULL;
    
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
     * Sets an error formatter.
     * 
     * @param type $errorFormatter 
     */
    public function setErrorFormatter(RestErrorFormatter $errorFormatter) {
      $this->errorFormatter= $errorFormatter;
    }
    
    /**
     * Generate API documentation
     * 
     * @param scriptlet.http.HttpScriptletRequest request The request
     * @param scriptlet.http.HttpScriptletResponse response The response
     */
    public function doDocumentation($request, $response) {
      $response->write('<html><head><title>REST @ '.$request->getURL()->getPath().'</title></head><body style="font-family: courier;">');

      $response->write('<h1>Methods</h1><ul>');
      foreach ($this->router->getRouting()->getItems() as $item) {
        $response->write('<li>'.$item->getVerb().' '.$item->getPath()->getPath().'<br/>');
        
        $args= $item->getArgs();
        foreach ($args->getArguments() as $name) {
          $response->write(sprintf('- %s : %s %s',
            $name, 
            $args->getArgumentType($name)->getName(),
            $args->isArgumentOptional($name) ? ' (optional)' : ''
          ));
          
          if ($args->isInjected($name)) $response->write(' (injected by '.$args->getInjection($name).')');
          
          $response->write('<br/>');
        }
        $response->write('<br/></li>');
      }
      
      $response->write('</ul></body></html>');
    }
    
    /**
     * Process request and handle errors
     * 
     * @param scriptlet.http.HttpScriptletRequest request The request
     * @param scriptlet.http.HttpScriptletResponse response The response
     */
    public function doProcess($request, $response) {
      $req= HttpRequestAdapterFactory::forRequest($request)->newInstance($request);
      $res= HttpResponseAdapterFactory::forRequest($request)->newInstance($response);
      
      try {
        $this->execute($req, $res);
      } catch (Throwable $e) {
        if(!$this->errorFormatter)
          throw $e;

        $formattedError= $this->errorFormatter->format($e);
        if (NULL === $formattedError)
          throw $e;

        if ($e instanceof ScriptletException) {
          $res->setStatus($e->getStatus());
        } else {
          $res->setStatus(HttpConstants::STATUS_INTERNAL_SERVER_ERROR);
        }
        
        $res->setData($formattedError);  
      }
    }
    
    /**
     * Do request processing
     * 
     * @param scriptlet.http.HttpScriptletRequest request The request
     * @param scriptlet.http.HttpScriptletResponse response The response
     */
    private function execute($req, $res) {
      $routings = $this->getRequestRoutes($req);
            
      // Setup processor and bind data sources
      $processor= new RestRoutingProcessor();
      $processor->bind('webservices.rest.server.transport.HttpRequestAdapter', $req);
      $processor->bind('webservices.rest.server.transport.HttpResponseAdapter', $res);
      
      try {
        $processor->bind('payload', $req->getData());
      } catch (FormatException $e) {
        throw new HttpScriptletException($e->getMessage(), HttpConstants::STATUS_BAD_REQUEST, $e->getCause());
      }

      // Use path and query params for maching routes
      // (after casting to required type, the params are passed to web-methods)
      $uri= sprintf('%s?%s', $req->getPath(), $req->getQueryString());

      $routed= FALSE;
      $errors= array();
      for ($i= 0, $s= sizeof($routings); $i<$s && !$routed; $i++) {
        $routing= $routings[$i];
        
        try {
          $res->setData($processor->execute(
            $routing,
            $routing->getPath()->match(substr($uri, strlen($this->base)))
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
     * Configure available routes for request
     * 
     * @param scriptlet.http.HttpScriptletRequest request The request
     * @param scriptlet.http.HttpScriptletResponse response The response
     */
    private function getRequestRoutes($req) {
      $path= substr($req->getPath(), strlen($this->base));
      
      if (!$this->router->resourceExists($path)) {
        throw new HttpScriptletException('Resource '.$path.' does not exist.', HttpConstants::STATUS_NOT_FOUND);
      }
      
      $routings= $this->router->routesFor($req, $res);     
      if (count($routings) === 0) {
        throw new HttpScriptletException('The method '.$req->getMethod().' is not allowed on the resource '.$path.'.', HttpConstants::STATUS_METHOD_NOT_ALLOWED);
      }
      
      return $routings;
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
      
      // Check Accept header for "text/html" which may indicate a direct
      // browser request - in this case just show the API doc
      if (FALSE !== strpos($request->getHeader('Accept'), 'text/html')) {
        return 'doDocumentation';
      }
      
      // We want to handle all request at one place
      return 'doProcess';
    }
  }
?>
