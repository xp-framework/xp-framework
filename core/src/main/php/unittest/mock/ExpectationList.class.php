<?php

  /* This class is part of the XP framework
   *
   * $Id$
   */
  uses('lang.IllegalArgumentException');

  /**
   * A stateful list for expectations.
   *
   * @purpose
   */
  class ExpectationList extends Object {
    private
    $list = null,
    $called = null,
    $unexpected= null;
    
    /**
     * Constructor      
     */
    public function __construct() {
      $this->list = new Vector();
      $this->called = new Vector();
      $this->unexpected = new Vector();
    }

    /**
     * Adds an expectation.
     * 
     * @param unittest.mock.Expectation expectation    
     */
    public function add($expectation) {
      if (!($expectation instanceof Expectation))
        throw new IllegalArgumentException("Expectation expected.");

      $this->list->add($expectation);
    }

    /**
     * Returns the next expectation or null if no expectations left.
     *
     * @param mixed[] args
     * @return unittest.mock.Expectation  
     */
    public function getNext($args) {
      $expectation = $this->getMatching($args);
      if(!$expectation)
        return null;
      
      $expectation->incActualCalls(); //increase call counter

      if (!$expectation->canRepeat()) { // no more repetitions left
        $this->moveToCalledList($expectation);
      }

      return $expectation;
    }

    /**
     * Searches for a (valid) expectation that matches the parameters
     *
     * @param  mixed[] args
     * @return unittest.mock.Expectation
     */
    private function getMatching($args) {
      foreach ($this->list as $exp) {
        if ($exp->doesMatchArgs($args))
          return $exp;
      }

      return null;
    }

    /**
     * Removes the passed expectation from the expectation list to the called
     * list, so that expectation is 'invalidated' by this method and won't be
     * returned by getNext anymore.
     * 
     * @param  $expectation 
     */
    private function moveToCalledList($expectation) {
      $idx = $this->list->indexOf($expectation); //find it
      $this->list->remove($idx); //remove it
      $this->called->add($expectation); 
    }

    /**
     * Stores a call in the $unexpected list.
     *
     * @param string method
     * @param mixed[] args
     */
    public function fileUnexpected($method, $args) {
      $this->unexpected->add(array($method, $args));
    }

    public function getExpectations() {
      return $this->list;
    }

    public function getCalled() {
      return $this->called;
    }
  }
?>