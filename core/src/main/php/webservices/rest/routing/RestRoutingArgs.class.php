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
    protected $injects= array();
    
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
      
      $args= $this->getArguments();
      foreach ($injects as $name => $inject) {
        $this->addInjection($inject, is_numeric($name) ? $args[$name] : $name);
      }
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
     * Test if argument exists
     * 
     * @param string name The name of argument
     * @return bool
     */
    public function hasArgument($name) {
      return isset($this->args[$name]);
    }
    
    /**
     * Return list of arguments
     * 
     * @return string[]
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
     * @param string ref The optional reference id
     */
    public function addInjection($name, $ref= NULL) {
      $this->injects[$ref !== NULL ? $ref : sizeof($this->injects)]= $name;
    }
    
    /**
     * Return injections
     * 
     * @return string[]
     */
    public function getInjections() {
      return array_values($this->injects);
    }
    
    /**
     * Return injection name for given parameter
     * 
     * @param string name The parameter name
     * @return string
     */
    public function getInjection($name) {
      if (array_key_exists($name, $this->injects)) {  // Referenced injection
        return $this->injects[$name];
      } else {
        $names= $this->getArguments();
        
        if (FALSE !== ($p= array_key_exists($name, array_keys($names)))) {
          return $this->injects[$names[$name]];
        }
      }
      
      return NULL;
    }
    
    /**
     * Get injection reference
     * 
     * @param int idx The injection index
     * @return string
     */
    public function getInjectionRef($idx) {
      $keys= array_keys($this->injects);
      
      return $keys[$idx];
    }
    
    /**
     * Test if given parameter name is an injection parameter
     * 
     * @param string name The name of parameter
     * @return bool
     */
    public function isInjected($name) {
      return $this->getInjection($name) !== NULL;
    }
  }
?>
