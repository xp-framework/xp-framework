<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'util.log.context.NestedLogContext'
  );

  /**
   * Tests NestedLogContext class
   *
   */
  class NestedLogContextTest extends TestCase {
    protected $context= NULL;

    /**
     * Sets up test case
     *
     */
    public function setUp() {
      $this->context= new NestedLogContext();
    }

    /**
     * Tests NestedLogContext::getDepth()
     *
     */
    #[@test]
    public function getDepth() {
      $this->assertEquals(0, $this->context->getDepth());
      $this->context->push('val1');
      $this->assertEquals(1, $this->context->getDepth());

      $this->context->push('val2');
      $this->assertEquals(2, $this->context->getDepth());
    }

    /**
     * Tests NestedLogContext::pop()
     *
     */
    #[@test]
    public function pop() {
      $this->assertNull($this->context->pop());
      $this->context->push('val1');
      $this->context->push('val2');
      $this->assertEquals('val2', $this->context->pop());
      $this->assertEquals('val1', $this->context->pop());
    }

    /**
     * Tests NestedLogContext::peek()
     *
     */
    #[@test]
    public function peek() {
      $this->assertNull($this->context->peek());
      $this->context->push('val1');
      $this->context->push('val2');
      $this->assertEquals('val2', $this->context->peek());
      $this->assertEquals('val2', $this->context->peek());
    }

    /**
     * Tests NestedLogContext::clear()
     *
     */
    #[@test]
    public function clear() {
      $this->context->push('val1');
      $this->context->push('val2');
      $this->assertEquals(2, $this->context->getDepth());
      $this->context->clear();
      $this->assertEquals(0, $this->context->getDepth());
    }

    /**
     * Tests NestedLogContext::format()
     *
     */
    #[@test]
    public function format() {
      $this->assertEquals('', $this->context->format());
      $this->context->push('val1');
      $this->context->push('val2');
      $this->assertEquals('val1 val2', $this->context->format());
    }

    /**
     * Tests NestedLogContext::toString()
     *
     */
    #[@test]
    public function toStringTest() {
      $this->assertEquals('util.log.context.NestedLogContext{}', $this->context->toString());
      $this->context->push('val1');
      $this->context->push('val2');
      $this->assertEquals('util.log.context.NestedLogContext{val1 > val2}', $this->context->toString());
    }
  }
?>
