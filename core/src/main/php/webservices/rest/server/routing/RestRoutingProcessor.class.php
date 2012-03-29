<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'webservices.rest.server.RestDataCaster',
    'webservices.rest.server.routing.RestRoutingItem'
  );
  
  /**
   * REST route processor
   *
   * @test    xp://net.xp_framework.unittest.rest.server.routing.RestRoutingProcessorTest
   */
  class RestRoutingProcessor extends Object {
    private
      $bindings= array(),
      $dataCaster= NULL;

    /**
     * Constructor
     * 
     * @param     webservices.rest.server.RestDataCaster
     */
    public function __construct(RestDataCaster $dataCaster= NULL) {
      if (NULL === $dataCaster) {
        $this->dataCaster= new RestDataCaster();
      } else {
        $this->dataCaster= $dataCaster;
      }
    }
    
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
     * Helper function for parsing binding name
     * 
     * @param string name The name
     * @return string[]
     */
    protected function parseName($name) {
      if (!preg_match('/^([^\[]*)(\[(.*)\])?$/', $name, $matches)) {
        throw new IllegalArgumentException('Can not parse binding: '.$name);
      }
      
      return array(
        $matches[1],
        isset($matches[3]) ? $matches[3] : NULL
      );
    }
    
    /**
     * Retrieve binding
     * 
     * @param string token The token name of binding
     * @return var
     */
    public function getBinding($token) {
      list($name, $key)= $this->parseName($token);
      
      if (!array_key_exists($name, $this->bindings)) {
        throw new IllegalArgumentException('No binding to inject '.$token);
      }
      
      if ($key !== NULL) {
        $binding= (array)$this->bindings[$name];
        
        if (!array_key_exists($key, $binding)) {
          throw new IllegalArgumentException('Binding '.$name.' has no key '.$key);
        }
        
        return $binding[$key];
        
      } else {
        return $this->bindings[$name];
      }
    }
    
    /**
     * Process
     * 
     * @param webservices.rest.server.routing.RestRoutingItem routing The routing item to process
     * @param mixed[] values The argument values
     * @return var
     */
    public function execute(RestRoutingItem $routing, $values) {
      $args= array();
      $names= $routing->getArgs()->getArguments();
      
      // Build list of injected arguments
      foreach ($routing->getArgs()->getInjections() as $i => $name) {
        $ref= $routing->getArgs()->getInjectionRef($i);
        
        // Test if injection references a named argument
        if (!is_numeric($ref)) {
          $ref= array_search($ref, $names);
        }
        
        // Do not un-serialize object instances
        $binding= $this->getBinding($name);
        if ($binding instanceof Generic) {
          $args[(int)$ref]= $binding;
          continue;
        }
        
        // Try to convert binding to requested argument type
        try {
          $args[(int)$ref]= $this->dataCaster->complex(
            $this->dataCaster->simple($binding),
            $routing->getArgs()->getArgumentType($names[$ref])
          );
        } catch (XPException $e) {
          throw new ClassCastException('Can not convert argument #'.$ref.' from '.$name.': '.$e->getMessage(), $e);
        }
      }

      // Add named parameters
      foreach ($names as $i => $name) {
        if (isset($args[$i])) continue;  // Skip injection arguments

        // Try to convert parameters to requested argument type
        try {
          $args[$i]= $this->dataCaster->complex(
            $this->dataCaster->simple($values[$name]),
            $routing->getArgs()->getArgumentType($name)
          );
        } catch (XPException $e) {
          throw new ClassCastException('Can not convert object field '.$name.': '.$e->getMessage(), $e);
        }
      }
      ksort($args);
      
      return $this->dataCaster->simple($routing->getTarget()->process($args));
    }
  }
?>
