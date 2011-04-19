<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * ArrayList iterator
   *
   * @test  xp://net.xp_framework.unittest.core.types.ArrayListTest
   * @see   php://language.oop5.iterations
   */
  class ArrayListIterator extends Object implements Iterator {
    protected $iterator = 0;
    protected $list = NULL;

    /**
     * Creates a new ArrayList iterator
     *
     * @param   lang.types.ArrayList list
     */
    public function __construct(ArrayList $list) {
      $this->list= $list;
    }
      
    /**
     * Returns current value of iteration
     *
     * @return  var
     */
    public function current() { 
      return $this->list->values[$this->iterator]; 
    }

    /**
     * Returns current offset of iteration
     *
     * @return  int
     */
    public function key() { 
      return $this->iterator; 
    }

    /**
     * Forwards iteration
     *
     */
    public function next() { 
      $this->iterator++; 
    }

    /**
     * Rewinds iteration
     *
     */
    public function rewind() { 
      $this->iterator= 0; 
    }
    
    /**
     * Checks whether iteration should continue
     *
     * @return  bool
     */
    public function valid() { 
      return $this->iterator < $this->list->length; 
    }
  }
?>
