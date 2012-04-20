<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'util.Hashmap',
    'util.NoSuchElementException',
    'webservices.rest.server.routing.RestRoutingItem'
  );
  
  /**
   * REST routing arguments
   *
   * @test    xp://net.xp_framework.unittest.rest.server.routing.RestRoutingArgsTest
   * @purpose Routing table
   */
  class RestRoutingArgs extends Object {
    protected $args= array();
    public $injects= array();
    
    /**
     * Constructor
     * 
     * @param string[] args The arguments (defaults to empty)
     * @param string[] injects The injected arguments (defaults to empty)
     */
    public function __construct($args= array(), $injects= array()) {
      foreach ($args as $arg) {
        $this->addArgument(
          is('lang.reflect.Parameter', $arg) ? $arg->getName() : $arg, 
          is('lang.reflect.Parameter', $arg) ? $arg : Type::$VAR
        );
      }
      
      foreach ($injects as $name => $inject) {
        $this->addInjection($inject, $name);
      }
    }
    
    /**
     * Add argument
     * 
     * @param string name The name of argument
     * @param lang.reflect.Parameter arg The argument
     */
    public function addArgument($name, $arg) {
      $this->args[$name]= $arg;
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
     * @param string name The name of argument
     * @return lang.Type
     */
    public function getArgumentType($name) {
      return is('lang.reflect.Parameter', $this->args[$name]) ? 
        $this->args[$name]->getType() : $this->args[$name];
    }
    
    /**
     * Check if the argument is optional or not
     * 
     * @param string name The name of argument
     * @return bool
     */
    public function isArgumentOptional($name) {
      return is('lang.reflect.Parameter', $this->args[$name]) ? 
        $this->args[$name]->isOptional() : FALSE;
    }
    
    /**
     * Get default value if argument is optional
     * 
     * @param string name The name of argument
     * @return bool
     */
    public function getArgumentDefaultValue($name) {
      return $this->isArgumentOptional($name) ? 
        $this->args[$name]->getDefaultValue() : NULL;
    }
    
    /**
     * Add injection
     * 
     * @param string name The injection name
     * @param string ref The optional reference id
     */
    public function addInjection($name, $ref= NULL) {
      if ($ref === NULL) {  // Append injection parameter
        $this->injects[sizeof($this->injects)]= $name;
        
      } else if (is_numeric($ref)) {  // Reference by index
        $args= $this->getArguments();
        $this->injects[$args[$ref]]= $name;
        
      } else {  // Referenced by name
        $this->injects[$ref]= $name;
      }
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
