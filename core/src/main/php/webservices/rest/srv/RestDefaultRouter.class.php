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
        if (!$handler->hasAnnotation('webservice')) continue;
        $hbase= $handler->hasAnnotation('webservice', 'path') 
          ? rtrim($handler->getAnnotation('webservice', 'path'), '/')
          : ''
        ;

        foreach ($handler->getMethods() as $method) {
          if (!$method->hasAnnotation('webmethod')) continue;

          $webmethod= $method->getAnnotation('webmethod');
          $this->addRoute(new RestRoute(
            $webmethod['verb'],
            $base.$hbase.rtrim($webmethod['path'], '/'),
            $method,
            isset($webmethod['accepts']) ? (array)$webmethod['accepts'] : NULL,
            isset($webmethod['returns']) ? (array)$webmethod['returns'] : NULL
          ));
        }
      }
    }

    public function argumentsFor($route, $request, $in) {
      $input= NULL;

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
              $arg= $parameter->getDefaultValue();
            } else {
              $arg= rawurldecode($route['segments'][$annotations[$param][1]]);
            }
            $args[]= $this->convert->convert($parameter->getType(), $arg);
            break;

          case 'param':
            if (!$request->hasParam($annotations[$param][1])) {
              $arg= $parameter->getDefaultValue();
            } else {
              $arg= $request->getParam($annotations[$param][1]); 
            }
            $args[]= $this->convert->convert($parameter->getType(), $arg);
            break;

          case NULL:
            if (NULL === $input) {
              $input= $in->read($request, $parameter->getType()); 
            }
            $args[]= $input;
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
      return $args;
    }
  }
?>
