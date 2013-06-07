<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'lang.IllegalArgumentException', 
    'unittest.mock.Expectation',
    'util.collections.Vector'
  );

  /**
   * A stateful list for expectations.
   *
   */
  class ExpectationList extends Object {
    private
      $list       = NULL,
      $called     = NULL,
      $unexpected = NULL;
    
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
     * @param   unittest.mock.Expectation expectation    
     */
    public function add(Expectation $expectation) {
      $this->list->add($expectation);
    }

    /**
     * Returns the next expectation or null if no expectations left.
     *
     * @param   var[] args
     * @return  unittest.mock.Expectation  
     */
    public function getNext($args) {
      $expectation= $this->getMatching($args);
      if (NULL === $expectation) return NULL;
      
      $expectation->incActualCalls();     // increase call counter

      if (!$this->called->contains($expectation)) {
        $this->called->add($expectation);
      }
      
      if (!$expectation->canRepeat()) {   // no more repetitions left
        $idx= $this->list->indexOf($expectation);
        $this->list->remove($idx);
      }

      return $expectation;
    }

    /**
     * Returns the expectation at position $idx
     *
     * @param   int idx
     * @return  unittest.mock.Expectation  
     */
    public function getExpectation($idx) {
      return $this->list[$idx];
    }
    
    /**
     * Returns the size of the list
     *
     * @return  int  
     */
    public function size() {
      return $this->list->size();
    }
    
    /**
     * Searches for a (valid) expectation that matches the parameters
     *
     * @param   var[] args
     * @return  unittest.mock.Expectation
     */
    private function getMatching($args) {
      foreach ($this->list as $exp) {
        if ($exp->isInPropertyBehavior() || $exp->doesMatchArgs($args)) return $exp;
      }

      return NULL;
    }

    /**
     * Stores a call in the $unexpected list.
     *
     * @param   string method
     * @param   var[] args
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

    /**
     * Cerates a string representation
     *
     * @return string
     */
    public function toString() {
      return $this->getClassName().'@'.xp::stringOf($this->list->elements());
    }
  }
?>
