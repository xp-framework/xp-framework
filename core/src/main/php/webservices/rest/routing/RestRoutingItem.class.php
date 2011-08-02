<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'webservices.rest.routing.RestPath',
    'webservices.rest.routing.RestRoute',
    'webservices.rest.routing.RestRoutingArgs'
  );
  
  /**
   * One item in routing table
   *
   * @test    xp://net.xp_framework.unittest.rest.RestRoutingItemTest
   * @purpose Routing item
   */
  class RestRoutingItem extends Object {
    protected $method= NULL;
    protected $path= NULL;
    protected $target= NULL;
    
    /**
     * Constructor
     * 
     * @param string method The method
     * @param webservices.rest.routing.RestPath path The path
     * @param webservices.rest.routing.RestRoute target The target route to apply
     */
    public function __construct($method, RestPath $path, RestRoute $target, $args= NULL) {
      $this->method= $method;
      $this->path= $path;
      $this->target= $target;
      $this->args= $args !== NULL ? $args : new RestRoutingArgs();
    }
    
    /**
     * Return method
     * 
     * @return string
     */
    public function getMethod() {
      return $this->method;
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
     * Return target
     * 
     * @return webservices.rest.routing.RestRoute
     */
    public function getTarget() {
      return $this->target;
    }
    
    /**
     * Return arguments
     * 
     * @return webserices.rest.RestRoutingArgs
     */
    public function getArgs() {
      return $this->args;
    }
    
    /**
     * Test if method and path applies to this routing item
     * 
     * @param string method The method
     * @param string path The path
     * @return bool
     */
    public function appliesTo($method, $path) {
      return (
        ($this->method == $method) &&
        (FALSE !== $this->path->match($path))
      );
    }
  }
?>
