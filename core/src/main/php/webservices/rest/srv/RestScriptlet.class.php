<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'scriptlet.HttpScriptlet',
    'scriptlet.Preference',
    'webservices.rest.srv.RestFormat',
    'webservices.rest.srv.RestContext',
    'webservices.rest.srv.Response',
    'webservices.rest.srv.RestDefaultRouter',
    'util.log.Traceable'
  );
  
  /**
   * REST scriptlet
   *
   */
  class RestScriptlet extends HttpScriptlet implements Traceable {
    protected 
      $cat     = NULL,
      $router  = NULL,
      $context = NULL,
      $base    = '';

    /**
     * Constructor
     * 
     * @param  string package The package containing handler classes
     * @param  string base The base URL (will be stripped off from request url)
     * @param  string context The context class to use
     * @param  string router The router class to use
     */
    public function __construct($package, $base= '', $context= '', $router= '') {
      $this->base= rtrim($base, '/');

      // Context class
      $this->context= XPClass::forName('' === (string)$context ? 'webservices.rest.srv.RestContext' : $context); 

      // Create router
      if ('' === (string)$router) {
        $this->router= new RestDefaultRouter();
      } else {
        $this->router= XPClass::forName($name)->newInstance();
      }
      $this->router->configure($package, $this->base);
      $this->router->setInputFormats(array('*json', '*xml', 'application/x-www-form-urlencoded'));
      $this->router->setOutputFormats(array('application/json', 'text/json', 'text/xml', 'application/xml'));
    }

    /**
     * Set a log category for tracing
     *
     * @param  util.log.LogCategory cat
     */
    public function setTrace($cat) {
      $this->cat= $cat;
    }

    /**
     * Calculate method to invoke
     *
     * @param   scriptlet.HttpScriptletRequest request 
     * @return  string
     */
    public function handleMethod($request) {
      parent::handleMethod($request);
      return 'doProcess';
    }

    /**
     * Process request and handle errors
     * 
     * @param  scriptlet.HttpScriptletRequest request The request
     * @param  scriptlet.HttpScriptletResponse response The response
     */
    public function doProcess($request, $response) {
      $url= $request->getURL();
      $accept= new Preference($request->getHeader('Accept', '*/*'));
      $this->cat && $this->cat->info(
        $request->getMethod(),
        $request->getHeader('Content-Type', '(null)'),
        $url->getURL(),
        $accept
      );

      // Iterate over all applicable routes
      foreach ($this->router->targetsFor(
        $request->getMethod(), 
        $url->getPath(), 
        $request->getHeader('Content-Type', NULL), 
        $accept
      ) as $target) {
        $context= $this->context->newInstance();
        $context->setTrace($this->cat);
        if ($context->process($target, $request, $response)) return;
      }

      // No route
      $response->setStatus(HttpConstants::STATUS_NOT_FOUND);
      RestFormat::forMediaType($accept->match($this->router->getOutputFormats()))->write($response, new Payload(
        array('message' => 'Could not route request to '.$url->getURL()), array('name' => 'error')
      ));
    }
  }
?>
