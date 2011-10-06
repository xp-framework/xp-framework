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
    protected $verb= NULL;
    protected $path= NULL;
    protected $target= NULL;
    
    /**
     * Constructor
     * 
     * @param string verb The verb
     * @param webservices.rest.routing.RestPath path The path
     * @param webservices.rest.routing.RestRoute target The target route to apply
     */
    public function __construct($verb, RestPath $path, RestRoute $target, $args= NULL) {
      $this->verb= $verb;
      $this->path= $path;
      $this->target= $target;
      $this->args= $args !== NULL ? $args : new RestRoutingArgs();
    }
    
    /**
     * Return verb
     * 
     * @return string
     */
    public function getVerb() {
      return $this->verb;
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
     * Test if verb and path applies to this routing item
     * 
     * @param string verb The verb
     * @param string path The path
     * @return bool
     */
    public function appliesTo($verb, $path) {
      return (
        ($this->verb == $verb) &&
        (FALSE !== $this->path->match($path))
      );
    }
  }
?>
