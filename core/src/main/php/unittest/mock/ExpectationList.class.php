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
    $list= null,
    $called= null,
    $unexpected= null;
    
    /**
     * Constructor      
     */
    public function __construct() {
      $this->list= new Vector();
      $this->called= new Vector();
      $this->unexpected= new Vector();
    }

    /**
     * Adds an expectation.
     * 
     * @param unittest.mock.Expectation expectation    
     */
    public function add($expectation) {
      if (!($expectation instanceof Expectation)) {
        throw new IllegalArgumentException("Expectation expected.");
      }
      
      $this->list->add($expectation);
    }

    /**
     * Returns the next expectation or null if no expectations left.
     *
     * @param mixed[] args
     * @return unittest.mock.Expectation  
     */
    public function getNext($args) {
      $expectation= $this->getMatching($args);
      if (!$expectation) {
        return NULL;
      }
      
      $expectation->incActualCalls(); //increase call counter

      if(!$this->called->contains($expectation)) {
          $this->called->add($expectation);
      }
      
      if (!$expectation->canRepeat()) { // no more repetitions left
          $idx= $this->list->indexOf($expectation); //find it
          $this->list->remove($idx); //remove it
      }

      return $expectation;
    }

    /**
     * Returns the expectation at position $idx
     *
     * @param int idx
     * @return unittest.mock.Expectation  
     */
    public function getExpectation($idx) {
      return $this->list[$idx];
    }
    
    /**
     * Returns the size of the list
     *
     * @return int  
     */
    public function size() {
      return $this->list->size();
    }
    
    /**
     * Searches for a (valid) expectation that matches the parameters
     *
     * @param  mixed[] args
     * @return unittest.mock.Expectation
     */
    private function getMatching($args) {
      foreach ($this->list as $exp) {
        if ($exp->isInPropertyBehavior() || $exp->doesMatchArgs($args)) {
          return $exp;
        }
      }

      return NULL;
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

    /**
     * Returns the expectation list
     *
     * @return util.collections.Vector
     */
    public function getExpectations() {
      return $this->list;
    }

    /**
     * Returns expectations that have been "called"
     *
     * @return util.collections.Vector
     */
    public function getCalled() {
      return $this->called;
    }
  }
?>