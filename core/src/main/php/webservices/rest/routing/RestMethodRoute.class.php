<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('webservices.rest.routing.RestRoute');
  
  /**
   * Routing to method of class
   *
   * @test xp://net.xp_framework.unittest.rest.routing.RestMethodRouteTest
   * @purpose Route
   */
  class RestMethodRoute extends Object implements RestRoute {
    protected $path= NULL;
    protected $method= NULL;
    protected $args= array();
    
    /**
     * Constructor
     * 
     * @param webservices.rest.routing.RestPath path The path
     * @param lang.reflect.Method method The method to route to
     * @param mixed[] args The additional args to use when invoking the method (defaults to empty array)
     */
    public function __construct($path, $method, $args= array()) {
      $this->path= $path;
      $this->method= $method;
      $this->args= $args;
    }
    
    /**
     * Return path
     * 
     * @return webservices.rest.routing.RestPath
     */
    public function getPath() {
      return $this->path;
    }
    
    /**
     * Return method
     * 
     * @return lang.reflect.Method
     */
    public function getMethod() {
      return $this->method;
    }
    
    /**
     * Return injected arguments
     * 
     * @return mixed[]
     */
    public function getArguments() {
      return $this->args;
    }
    
    /**
     * Handle route 
     * 
     * @param webservices.rest.transport.HttpRequestAdapter request The request
     * @param webservices.rest.transport.HttpResponseAdapter response The response
     * @return mixed[]
     */
    public function route($request, $response) {
      $args= array();
      
      // Copy inject parameters
      $i= 0;
      while (isset($this->args[$i])) $args[]= $this->args[$i++];
      
      // Append named parameters
      foreach ($this->method->getParameters() as $n => $arg) {
        if ($n < $i) continue;  // Skip injected args
        
        if (isset($this->args[$arg->getName()])) {
          $args[]= $this->args[$arg->getName()];
        } else if ($arg->isOptional() === FALSE) {
          throw new IllegalArgumentException('Argument '.$arg->getName().' missing');
        }
      }
      
      return $this->method->invoke(
        $this->method->getDeclaringClass()->newInstance(),
        $args
      );
    }
  }
?>
