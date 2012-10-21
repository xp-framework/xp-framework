<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('webservices.rest.server.routing.AbstractRestRouter');
  
  /**
   * REST router based on class and method annotations
   *
   * Example of web service class
   * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~
   * <code>
   *   #[@webservice]
   *   class HelloWorldHandler extends Object {
   *
   *     #[@webmethod(verb= 'GET', path= '/hello')]
   *     public function helloWorld() {
   *       return 'Hello, World!';
   *     }
   *   }
   * </code>
   *
   * @test  xp://net.xp_framework.unittest.rest.server.RestDefaultRouterTest
   */
  class RestDefaultRouter extends AbstractRestRouter {

    /**
     * Configure router
     * 
     * @param  string setup
     * @param  string base The base URI
     */
    public function configure($setup, $base= '') {
      static $search= '/\{([\w]*)\}/';
      static $replace= '(?P<$1>[%\w:\+\-\.]*)';

      $package= Package::forName($setup);
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
          $this->addRoute(new RestRoute(
            $webmethod['verb'],
            $pattern,
            $method,
            isset($webmethod['accepts']) ? (array)$webmethod['accepts'] : NULL,
            isset($webmethod['returns']) ? (array)$webmethod['returns'] : NULL
          ));
        }
      }
    }
  }
?>
