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
      foreach ($args as $name => $type) {
        is_numeric($name) ? $this->addArgument($type) : $this->addArgument($name, $type);
      }
      
      $this->injects= $injects;
    }
    
    /**
     * Add argument
     * 
     * @param string name The name of argument
     * @param lang.reflect.Type type The type of argument
     */
    public function addArgument($name, Type $type= NULL) {
      $this->args[$name]= $type !== NULL ? $type : Type::$VAR;
    }
    
    /**
     * Return list of arguments
     * 
     * @return string
     */
    public function getArguments() {
      return array_keys($this->args);
    }
    
    /**
     * Return type for given argument
     * 
     * @return lang.Type
     */
    public function getArgumentType($name) {
      return $this->args[$name];
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
