<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'webservices.rest.server.routing.RestPath',
    'webservices.rest.server.routing.RestRoute',
    'webservices.rest.server.routing.RestRoutingArgs'
  );
  
  /**
   * One item in routing table
   *
   * @test    xp://net.xp_framework.unittest.rest.server.RestRoutingItemTest
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
     * @param webservices.rest.server.routing.RestPath path The path
     * @param webservices.rest.server.routing.RestRoute target The target route to apply
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
     * @return webservices.rest.server.routing.RestPath
     */
    public function getPath() {
      return $this->path;
    }
    
    /**
     * Return target
     * 
     * @return webservices.rest.server.routing.RestRoute
     */
    public function getTarget() {
      return $this->target;
    }
    
    /**
     * Return arguments
     * 
     * @return webserices.rest.server.RestRoutingArgs
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

    /**
     * Creates a string representation
     *
     * @return string
     */
    public function toString() {
      return $this->getClassName().'('.$this->verb.' '.$this->path->getUri().' -> '.$this->target->toString().')';
    }
  }
?>
