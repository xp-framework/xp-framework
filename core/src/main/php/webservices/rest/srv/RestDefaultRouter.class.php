<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('webservices.rest.srv.AbstractRestRouter');
  
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
   * @test  xp://net.xp_framework.unittest.webservices.rest.srv.RestDefaultRouterTest
   */
  class RestDefaultRouter extends AbstractRestRouter {

    /**
     * Configure router
     * 
     * @param  string setup
     * @param  string base The base URI
     */
    public function configure($setup, $base= '') {
      $package= Package::forName($setup);
      foreach ($package->getClasses() as $handler) {
        if ($handler->hasAnnotation('webservice')) $this->addWebservice($handler, $base);
      }
    }

    /**
     * Add a webservice
     *
     * @param  lang.XPClass class
     * @param  string base
     * @throws lang.IllegalArgumentException
     */
    public function addWebservice($class, $base= '') {
      try {
        $webservice= $class->getAnnotation('webservice');
      } catch (ElementNotFoundException $e) {
        throw new IllegalArgumentException('Not a webservice: '.$class->toString(), $e);
      }

      isset($webservice['path']) && $base.= rtrim($webservice['path'], '/');
      foreach ($class->getMethods() as $method) {
        if ($method->hasAnnotation('webmethod')) $this->addWebmethod($method, $base);
      }
    }

    /**
     * Add a webmethod
     *
     * @param  lang.reflect.Method method
     * @param  string base
     * @throws lang.IllegalArgumentException
     */
    public function addWebmethod($method, $base= '') {
      try {
        $webmethod= $method->getAnnotation('webmethod');
      } catch (ElementNotFoundException $e) {
        throw new IllegalArgumentException('Not a webmethod: '.$method->toString(), $e);
      }

      // Create route from @webmethod annotation
      $route= $this->addRoute(new RestRoute(
        $webmethod['verb'],
        $base.rtrim($webmethod['path'], '/'),
        $method,
        isset($webmethod['accepts']) ? (array)$webmethod['accepts'] : NULL,
        isset($webmethod['returns']) ? (array)$webmethod['returns'] : NULL
      ));

      // Add route parameters using parameter annotations
      foreach ($method->getAnnotations() as $annotation => $value) {
        if (2 === sscanf($annotation, '$%[^:]: %s', $param, $source)) {
          $route->addParam($param, new RestParamSource(
            $value ? $value : $param,
            ParamReader::forName($source)
          ));
        }
      }
    }
  }
?>
