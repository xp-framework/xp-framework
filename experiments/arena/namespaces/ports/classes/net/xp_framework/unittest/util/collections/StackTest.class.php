<?php
/* This class is part of the XP framework
 *
 * $Id: StackTest.class.php 10175 2007-04-29 17:13:09Z friebe $
 */

  namespace net::xp_framework::unittest::util::collections;
 
  ::uses(
    'unittest.TestCase',
    'util.collections.Stack',
    'lang.types.String'
  );

  /**
   * Test Stack class
   *
   * @see      xp://util.collections.Stack
   * @purpose  Unit Test
   */
  class StackTest extends unittest::TestCase {
    public
      $stack= NULL;
    
    /**
     * Setup method. Creates the Stack member
     *
     */
    public function setUp() {
      $this->stack= new util::collections::Stack();
    }
        
    /**
     * Tests the Stack is initially empty
     *
     */
    #[@test]
    public function initiallyEmpty() {
      $this->assertTrue($this->stack->isEmpty());
    }

    /**
     * Tests Stack equals its clone
     *
     */
    #[@test]
    public function equalsClone() {
      $this->stack->push(new lang::types::String('green'));
      $this->assertTrue($this->stack->equals(clone($this->stack)));
    }

    /**
     * Tests push()
     *
     */
    #[@test]
    public function push() {
      $this->stack->push(new lang::types::String('green'));
      $this->assertFalse($this->stack->isEmpty());
      $this->assertEquals(1, $this->stack->size());
    }

    /**
     * Tests pop()
     *
     */
    #[@test]
    public function pop() {
      $color= new lang::types::String('green');
      $this->stack->push($color);
      $this->assertEquals($color, $this->stack->pop());
      $this->assertTrue($this->stack->isEmpty());
    }

    /**
     * Tests peek()
     *
     */
    #[@test]
    public function peek() {
      $color= new lang::types::String('green');
      $this->stack->push($color);
      $this->assertEquals($color, $this->stack->peek());
      $this->assertFalse($this->stack->isEmpty());
    }

    /**
     * Tests search()
     *
     */
    #[@test]
    public function search() {
      $color= new lang::types::String('green');
      $this->stack->push($color);
      $this->assertEquals(0, $this->stack->search($color));
      $this->assertEquals(-1, $this->stack->search(new lang::types::String('non-existant')));
    }

    /**
     * Tests elementAt()
     *
     */
    #[@test]
    public function elementAt() {
      $this->stack->push(new lang::types::String('red'));
      $this->stack->push(new lang::types::String('green'));
      $this->stack->push(new lang::types::String('blue'));

      $this->assertEquals(new lang::types::String('blue'), $this->stack->elementAt(0));
      $this->assertEquals(new lang::types::String('green'), $this->stack->elementAt(1));
      $this->assertEquals(new lang::types::String('red'), $this->stack->elementAt(2));
    }

    /**
     * Tests elementAt() when given an illegal offset
     *
     */
    #[@test, @expect('lang.IndexOutOfBoundsException')]
    public function elementAtIllegalOffset() {
      $this->stack->elementAt(-1);
    }
  }
?>
