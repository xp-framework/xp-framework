<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.mock.IMockState',
    'lang.IllegalArgumentException',
    'util.Hashmap',
    'util.collections.Vector'
  );

  /**
   * Replaying state.
   *
   * @test  xp://net.xp_framework.unittest.tests.mock.ReplayStateTest
   */
  class ReplayState extends Object implements IMockState {
    private
      $unexpectedCalls = NULL,
      $expectationMap  = NULL,
      $properties      = NULL;
        
    /**
     * Constructor
     *
     * @param   util.Hashmap expectationsMap
     * @param   util.Hashmap properties
     */
    public function  __construct($expectationMap, $properties) {
      if (!($expectationMap instanceof Hashmap)) {
        throw new IllegalArgumentException('Invalid expectation map passed.');
      }
      if (!($properties instanceof Hashmap)) {
        throw new IllegalArgumentException('Invalid properties passed.');
      }
      
      $this->expectationMap= $expectationMap;
      $this->properties= $properties;
      $this->unexpectedCalls= new Hashmap();
      $this->buildUpProperties();
    }
    
    /**
     * Build properties
     *
     */
    private function buildUpProperties() {
      foreach($this->expectationMap->keys() as $method) {
        $expList= $this->expectationMap->get($method);
        
        if(!$this->checkForBehaviorMode($expList)) {
          continue;
        }
        
        $expectation= $expList->getExpectation(0);
        
        if(!$expectation || !$expectation->isInPropertyBehavior()) {
          continue;
        }
        
        $propertyName= substr($method, 3);
        $this->properties->put($propertyName, $expectation->getReturn());        
      }
    }
    
    /**
     * Checks for behavior mode
     *
     * @param   var list
     * @return  bool
     */
    private function checkForBehaviorMode($list) {
      $seenBehaviorMode= FALSE;
      for($i= 0; $i < $list->size(); ++$i) {
        $exp= $list->getExpectation($i);
        
        if ($seenBehaviorMode) {
          throw new IllegalStateException('Invalid expectations definition '.$exp->toString().'. Property behavior has been applied.');
        }
        
        if ($exp->isInPropertyBehavior() && $i>0) {
          throw new IllegalStateException('Invalid expectations definition '.$exp->toString().'. Cannot switch to property behavior as expecations have been defined already.');
        }
        
        if ($exp->isInPropertyBehavior()) {
          $seenBehaviorMode= TRUE;
        }
      }

      return $seenBehaviorMode;
    }
    /**
     * Handles calls to methods regarding the 
     *
     * @param   string method the method name
     * @param   var* args an array of arguments
     * @return  var
     */
    public function handleInvocation($method, $args) {
      if($this->isPropertyMethod($method)) {
        $prefix= substr($method, 0, 3);
        $suffix= substr($method, 3);
             
         if($prefix == 'set') {
           $this->properties->put($suffix, $args[0]);
           return;
         } else {
           return $this->properties->get($suffix);
         }
      }
      
      if(!$this->expectationMap->get($method)) {
        return NULL;
      }
            
      $expectationList= $this->expectationMap->get($method);
      $nextExpectation= $expectationList->getNext($args);
      if(!$nextExpectation) {//no more expectations
        $expectationList->fileUnexpected($method, $args);
        return NULL;
      }

      if(NULL != $nextExpectation->getException()) {
        throw $nextExpectation->getException();
      }
      
      return $nextExpectation->getReturn();      
    }
    
    /**
     * Checks whether method is a property accessor, which is the case
     * when its name...
     * <ul>
     *   <li>...starts with either "get" or "set"</li>
     *   <li>...is named like any of the properties known to this state</li>
     * </ul>
     *
     * @param   string method
     * @return  bool
     */
    private function isPropertyMethod($method) {
      $prefix= substr($method, 0, 3);
      $suffix= substr($method, 3);
      
      return ($prefix == 'get' || $prefix == 'set') && in_array($suffix, $this->properties->keys());
    }
  }

?>
