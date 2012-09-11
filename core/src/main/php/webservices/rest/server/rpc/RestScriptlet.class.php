<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'scriptlet.HttpScriptlet',
    'webservices.rest.server.RestFormat',
    'webservices.rest.server.routing.RestDefaultRouter',
    'util.log.Traceable'
  );
  
  /**
   * REST scriptlet
   *
   */
  class RestScriptlet extends HttpScriptlet implements Traceable {
    protected 
      $cat    = NULL,
      $router = NULL,
      $base   = '';
    
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
     * Get format for a given mediatype
     *
     * @param  string mediatype
     * @return webservices.rest.server.RestFormat
     */
    protected function formatFor($mediatype) {
      if ('application/x-www-form-urlencoded' === $mediatype) {
        return RestFormat::$FORM;
      } else if (preg_match('#[/\+]json$#', $mediatype)) {
        return RestFormat::$JSON;
      } else if (preg_match('#[/\+]xml$#', $mediatype)) {
        return RestFormat::$XML;
      } else {
        return RestFormat::$UNKNOWN;
      }
    }

    /**
     * Process request and handle errors
     * 
     * @param  scriptlet.http.HttpScriptletRequest request The request
     * @param  scriptlet.http.HttpScriptletResponse response The response
     */
    public function doProcess($request, $response) {
      $url= $request->getURL()->getURL();
      $this->cat && $this->cat->debug(
        $request->getMethod(),
        $request->getHeader('Content-Type', '(null)'),
        $url,
        $request->getHeader('Accept')
      );

      foreach ($this->router->routesFor($request, $response) as $route) {
        $this->cat && $this->cat->debug('->', $route);

        // Unserialize incoming payload if given
        if ($route['input']) {
          $input= $this->formatFor($route['input']);
        } else {
          $input= xp::null();
        }

        // Instantiate
        $instance= $route['target']->getDeclaringClass()->newInstance();

        // Parameter annotations parsing
        $annotations= array();
        foreach ($route['target']->getAnnotations() as $annotation => $value) {
          if (2 === sscanf($annotation, '$%[^:]: %s', $param, $source)) {
            $annotations[$param]= array($source, $value ? $value : $param);
          }
        }

        // Extract arguments according to definition
        $args= array();
        foreach ($route['target']->getParameters() as $parameter) {
          $param= $parameter->getName();
          switch ($annotations[$param][0]) {
            case 'path':
              if (!isset($route['segments'][$annotations[$param][1]])) {
                $args[]= $parameter->getDefaultValue();
              } else {
                $args[]= $route['segments'][$annotations[$param][1]];   
              }
              break;

            case 'param':
              if (!$request->hasParam($annotations[$param][1])) {
                $args[]= $parameter->getDefaultValue();
              } else {
                $args[]= $request->getParam($annotations[$param][1]); 
              }
              break;

            case NULL:
              $args[]= $input->read($request, $parameter->getType()); 
              break;

            default: 
              throw new HttpScriptletException(sprintf(
                'Malformed source %s for parameter %s of %s',
                $annotations[$param][0],
                $param,
                $route['target']->toString()
              ));
          }
        }

        // Invoke method
        try {
          $result= $route['target']->invoke($instance, $args);
        } catch (TargetInvocationException $t) {
          throw new HttpScriptletException($e->getCause()->getMessage(), HttpConstants::STATUS_BAD_REQUEST);
        }

        // For "VOID" methods, set status to "no content"
        if (Type::$VOID->equals($route['target']->getReturnType())) {
          $response->setStatus(HttpConstants::STATUS_NO_CONTENT);
          return;
        }

        // For any other methods, set status to "OK" and return
        $response->setStatus(HttpConstants::STATUS_OK);
        $response->setHeader('Content-Type', $route['output']);
        $this->formatFor($route['output'])->write($response, $result); 
        return;
      }
      
      throw new HttpScriptletException('Could not route request to '.$url, HttpConstants::STATUS_NOT_FOUND);
    }
  }
?>
