<?php

/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('unittest.mock.IMockState',
       'lang.IllegalArgumentException',
       'util.Hashmap',
       'util.collections.Vector');

  /**
   * Replaying state.
   *
   * @purpose Replay expectations 
   */
  class ReplayState extends Object implements IMockState {
    private
      $unexpectedCalls= NULL,
      $expectationMap= NULL;
        
    /**
     * Constructor
     *
     * @param Hashmap expectationsMap
     */
    public function  __construct($expectationMap) {
      if(!($expectationMap instanceof Hashmap))
        throw new IllegalArgumentException('Invalid expectation map passed.');
      
      $this->expectationMap= $expectationMap;
      $this->unexpectedCalls= new Vector();
    }
    /**
     * Handles calls to methods regarding the 
     *
     * @param   string method the method name
     * @param   var* args an array of arguments
     * @return  var
     */
    public function handleInvocation($method, $args) {
      if(!$this->expectationMap->containsKey($method))
        return NULL;

      $expectationList= $this->expectationMap->get($method);
      $nextExpectation= $expectationList->getNext($args);
      if(!$nextExpectation) {//no more expectations
        $expectationList->fileUnexpected($method, $args);
        return NULL;
      }

      if(NULL != $nextExpectation->getException())
        throw $nextExpectation->getException();
      
      return $nextExpectation->getReturn();      
    }
  }

?>