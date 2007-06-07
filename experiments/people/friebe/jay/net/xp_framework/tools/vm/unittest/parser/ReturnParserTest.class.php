<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('net.xp_framework.tools.vm.unittest.ParserTest');

  /**
   * TestCase for return nodes
   *
   * @purpose  Unittest
   */
  class ReturnParserTest extends ParserTest {
    
    /**
     * Tests returning nothing
     *
     */
    #[@test]
    public function returnNothing() {
      $nodes= $this->parse('return;');
      $this->assertEquals(1, sizeof($nodes));
      $this->assertNode('Return', $nodes[0]);
      $this->assertNull($nodes[0]->value);
    }

    /**
     * Tests returning a constant expression
     *
     */
    #[@test]
    public function constantExpression() {
      $nodes= $this->parse('return 1;');
      $this->assertEquals(1, sizeof($nodes));
      $this->assertNode('Return', $nodes[0]);
      $this->assertNode('LongNumber', $nodes[0]->value);
    }

    /**
     * Tests returning the result of a function call
     *
     */
    #[@test]
    public function functionCall() {
      $nodes= $this->parse('return str_replace("a", "b", "ab");');
      $this->assertEquals(1, sizeof($nodes));
      $this->assertNode('Return', $nodes[0]);
      $this->assertNode('FunctionCall', $nodes[0]->value);
    }

    /**
     * Tests returning the result of a method call
     *
     */
    #[@test]
    public function methodCall() {
      $nodes= $this->parse('return $a->method($arg);');
      $this->assertEquals(1, sizeof($nodes));
      $this->assertNode('Return', $nodes[0]);
      $this->assertNode('MethodCall', $nodes[0]->value);
    }

    /**
     * Tests returning the result of a chained method call
     *
     */
    #[@test]
    public function chainedMethodCall() {
      $nodes= $this->parse('return $a->reflect->method($arg);');
      $this->assertEquals(1, sizeof($nodes));
      $this->assertNode('Return', $nodes[0]);
      $this->assertNode('ObjectReference', $nodes[0]->value);
      $this->assertNode('Variable', $nodes[0]->value->class);
      $this->assertNode('Member', $nodes[0]->value->member);
      $this->assertNode('MethodCall', $nodes[0]->value->chain[0]);
    }
  }
?>
