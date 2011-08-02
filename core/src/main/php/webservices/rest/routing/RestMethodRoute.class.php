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
    protected $method= NULL;
    
    /**
     * Constructor
     * 
     * @param lang.reflect.Method method The method to route to
     * @param mixed[] args The additional args to use when invoking the method (defaults to empty array)
     */
    public function __construct($method) {
      $this->method= $method;
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
     * @param mixed[] args The list of arguments to use
     * @return mixed[]
     */
    public function process($args= array()) {
      foreach ($this->method->getParameters() as $n => $arg) {
        if (!isset($args[$n]) && !$arg->isOptional()) {
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
