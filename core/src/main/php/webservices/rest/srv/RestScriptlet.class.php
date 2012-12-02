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
      $base    = '';

    /**
     * Constructor
     * 
     * @param  string package The package containing handler classes
     * @param  string base The base URL (will be stripped off from request url)
     * @param  string router The router class to use
     */

    public function __construct($package, $base= '', $router= '') {
      if ('' === (string)$router) {
        $this->router= new RestDefaultRouter();
      } else if (strstr($router, '.')) {
        $this->router= cast(
          XPClass::forName($router)->newInstance(),
          'webservices.rest.server.routing.RestRouter'
        );
      } else {
        $this->router= Package::forName('webservices.rest.server.routing')
          ->loadClass('Rest'.ucfirst($router).'Router')
          ->newInstance()
        ;
      }
      
      $this->base= rtrim($base, '/');
      $this->router->configure($package, $this->base);
      $this->router->setInputFormats(array('*json', '*xml', 'application/x-www-form-urlencoded'));
      $this->router->setOutputFormats(array('application/json', 'text/json', 'text/xml', 'application/xml'));
    }

    /**
     * Set a log category fot tracing
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
        $context= new RestContext();
        try {
          $result= $this->router->process($target, $request, $context, RestFormat::forMediaType($target['input']));
        } catch (HttpScriptletException $e) {
          $result= $context->mapException($e);
        }

        // Have a result
        $response->setStatus($result->status);
        $response->setContentType($target['output']);
        foreach ($result->headers as $name => $value) {
          if ('Location' === $name) {
            $url= clone $request->getURL();
            $response->setHeader($name, $url->setPath($value)->getURL());
          } else {
            $response->setHeader($name, $value);
          }
        }
        foreach ($result->cookies as $cookie) {
          $response->setCookie($cookie);
        }
        RestFormat::forMediaType($target['output'])->write($response, $result->payload);
        return;
      }

      // No route
      $response->setStatus(HttpConstants::STATUS_NOT_FOUND);
      RestFormat::forMediaType($accept->match($this->router->getOutputFormats()))->write($response, new Payload(
        array('message' => 'Could not route request to '.$url->getURL()), array('name' => 'error')
      ));
    }
  }
?>
