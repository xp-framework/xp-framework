<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'webservices.rest.server.routing.RestPath',
    'webservices.rest.server.routing.RestRouter',
    'webservices.rest.server.routing.RestRouting',
    'webservices.rest.server.routing.RestMethodRoute'
  );
  
  /**
   * REST router based on class and method annotations
   *
   * @test    xp://net.xp_framework.unittest.rest.server.RestDefaultRouterTest
   */
  class RestDefaultRouter extends Object implements RestRouter {
    protected $routes= array();

    /**
     * Configure router
     * 
     * @param  lang.reflect.Package package
     * @param  string base The base URI
     */
    public function configure($package, $base= '') {
      static $search= '/\{([\w]*)\}/';
      static $replace= '(?P<$1>[%\w:\+\-\.]*)';

      foreach ($package->getClasses() as $handler) {
        if (!$handler->hasAnnotation('webservice')) continue;
        $hbase= $handler->hasAnnotation('webservice', 'path') 
          ? rtrim($handler->getAnnotation('webservice', 'path'), '/')
          : ''
        ;

        foreach ($handler->getMethods() as $method) {
          if (!$method->hasAnnotation('webmethod')) continue;

          $webmethod= $method->getAnnotation('webmethod');
          $pattern= '#^'.$base.$hbase.preg_replace($search, $replace, rtrim($webmethod['path'], '/')).'$#';
          $this->routes[$webmethod['verb']][]= array(
            'path'    => $pattern,
            'target'  => $method,
            'accepts' => isset($webmethod['accepts']) ? (array)$webmethod['accepts'] : NULL,
            'returns' => isset($webmethod['returns']) ? $webmethod['returns'] : NULL
          );
        }
      }
    }
    
    /**
     * Get routing table
     * 
     * @return  webservices.rest.server.routing.RestRouting
     */
    public function getRouting() {
      return xp::null();
    }

    /**
     * Return routes for given request and response
     * 
     * @param  scriptlet.http.HttpScriptletRequest request The request
     * @return webservices.rest.server.RestRoute[]
     */
    public function routesFor($request, $_= NULL) {
      $verb= $request->getMethod();
      if (!isset($this->routes[$verb])) return FALSE;

      // Figure out matching routes
      $path= rtrim($request->getURL()->getPath(), '/');
      $matching= array();
      foreach ($this->routes[$verb] as $route) {
        if (!preg_match($route['path'], $path, $segments)) continue;
        $matching[]= array(
          'target'   => $route['target'], 
          'segments' => $segments,
          'input'    => $route['accepts'],
          'output'   => $route['returns']
        );
      }
      return $matching;
    }
    
    /**
     * Return whether a specified resource exists
     * 
     * @param string resourcePath The resource path
     * @return bool
     */
    public function resourceExists($resourcePath) {
      return FALSE;
    }
  }
?>
