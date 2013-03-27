<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'webservices.rest.srv.AbstractRestRouter',
    'webservices.rest.srv.RestParamSource'
  );
  
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
        if ($method->hasAnnotation('webmethod')) $this->addWebmethod($class, $method, $base);
      }
    }

    /**
     * Add a webmethod
     *
     * @param  lang.XPClass class
     * @param  lang.reflect.Method method
     * @param  string base
     * @throws lang.IllegalArgumentException
     */
    public function addWebmethod($class, $method, $base= '') {
      try {
        $webmethod= $method->getAnnotation('webmethod');
      } catch (ElementNotFoundException $e) {
        throw new IllegalArgumentException('Not a webmethod: '.$method->toString(), $e);
      }

      // Create route from @webmethod annotation
      $route= $this->addRoute(new RestRoute(
        $webmethod['verb'],
        $base.(isset($webmethod['path']) ? rtrim($webmethod['path'], '/') : ''),
        $class,
        $method,
        isset($webmethod['accepts']) ? (array)$webmethod['accepts'] : NULL,
        isset($webmethod['returns']) ? (array)$webmethod['returns'] : NULL
      ));

      // Add route parameters using parameter annotations
      foreach ($method->getParameters() as $parameter) {
        $param= $parameter->getName();
        foreach ($parameter->getAnnotations() as $source => $value) {
          $route->addParam($param, new RestParamSource(
            $value ? $value : $param,
            ParamReader::forName($source)
          ));
        }
      }
    }
  }
?>
