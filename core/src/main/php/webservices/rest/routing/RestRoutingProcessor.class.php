<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'webservices.rest.RestDataCaster',
    'webservices.rest.routing.RestRoutingItem'
  );
  
  /**
   * REST route processor
   *
   * @test    xp://net.xp_framework.unittest.rest.routing.RestRoutingProcessorTest
   * @purpose Router
   */
  class RestRoutingProcessor extends Object {
    protected
      $bindings= array();
    
    /**
     * Bind resource
     * 
     * @param string name The name of binding
     * @param lang.Object resource The resource to bind
     */
    public function bind($name, $resource) {
      $this->bindings[$name]= $resource;
    }
    
    /**
     * Retrieve binding
     * 
     * @param string name The name of binding
     * @return mixed
     */
    public function getBinding($name) {
      if (!isset($this->bindings[$name])) {
        throw new IllegalArgumentException('No binding to inject '.$name);
      }
      
      return $this->bindings[$name];
    }
    
    /**
     * Process
     * 
     * @param webservices.rest.routing.RestRoutingItem routing The routing item to process
     * @param mixed[] values The argument values
     * @return mixed
     */
    public function execute(RestRoutingItem $routing, $values) {
      $args= array();
      
      // Build list of injected arguments
      foreach ($routing->getArgs()->getInjections() as $name) {
        $args[]= $this->getBinding($name);
      }

      // Add named parameters
      foreach ($routing->getArgs()->getArguments() as $i => $name) {
        if ($i < sizeof($routing->getArgs()->getInjections())) continue;  // Skip injection arguments

        $args[]= RestDataCaster::complex(
          RestDataCaster::simple($values[$name]),
          $routing->getArgs()->getArgumentType($name)
        );
      }
      
      return $routing->getTarget()->process($args);
    }
  }
?>
