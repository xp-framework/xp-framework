<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'util.Hashmap',
    'util.NoSuchElementException',
    'webservices.rest.routing.RestRoutingItem'
  );
  
  /**
   * REST routing arguments
   *
   * @test    xp://net.xp_framework.unittest.rest.routing.RestRoutingArgsTest
   * @purpose Routing table
   */
  class RestRoutingArgs extends Object {
    protected $args= array();
    protected $injections= array();
    
    /**
     * Constructor
     * 
     * @param string[] args The arguments (defaults to empty)
     * @param string[] injects The injected arguments (defaults to empty)
     */
    public function __construct($args= array(), $injects= array()) {
      $this->args= $args;
      $this->injects= $injects;
    }
    
    /**
     * Add argument
     * 
     * @param string name The name of argument
     */
    public function addArgument($name) {
      $this->args[]= $name;
    }
    
    /**
     * Return list of arguments
     * 
     * @return string
     */
    public function getArguments() {
      return $this->args;
    }
    
    /**
     * Add injection
     * 
     * @param string name The injection name
     */
    public function addInjection($name) {
      $this->injects[]= $name;
    }
    
    /**
     * Return injections
     * 
     * @return string[]
     */
    public function getInjections() {
      return $this->injects;
    }
  }
?>
