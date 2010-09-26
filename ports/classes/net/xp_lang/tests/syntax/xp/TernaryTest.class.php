<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('net.xp_lang.tests.syntax.xp.ParserTestCase');

  /**
   * TestCase
   *
   */
  class TernaryTest extends ParserTestCase {
  
    /**
     * Test ternary - expr ? expr : expr
     *
     */
    #[@test]
    public function ternary() {
      $this->assertEquals(array(new TernaryNode(array(
        'condition'     => new VariableNode('i'),
        'expression'    => new IntegerNode('1'),
        'conditional'   => new IntegerNode('2'),
      ))), $this->parse('
        $i ? 1 : 2;
      '));
    }

    /**
     * Test ternary - expr ?: expr
     *
     */
    #[@test]
    public function assignment() {
      $this->assertEquals(array(new AssignmentNode(array(
        'variable'      => new VariableNode('a'),
        'expression'    => new TernaryNode(array(
          'condition'     => new VariableNode('argc'),
          'expression'    => new VariableNode('args0'),
          'conditional'   => new IntegerNode('1')
        )),
        'op'            => '='
      ))), $this->parse('
        $a= $argc ? $args0 : 1;
      '));
    }

    /**
     * Test ternary - expr ?: expr
     *
     */
    #[@test]
    public function withoutExpression() {
      $this->assertEquals(array(new TernaryNode(array(
        'condition'     => new VariableNode('i'),
        'expression'    => NULL,
        'conditional'   => new IntegerNode('2'),
      ))), $this->parse('
        $i ?: 2;
      '));
    }

    /**
     * Test ternary - expr ?: (expr ? expr : expr)
     *
     */
    #[@test]
    public function nested() {
      $this->assertEquals(array(new TernaryNode(array(
        'condition'     => new VariableNode('i'),
        'expression'    => NULL,
        'conditional'   => new BracedExpressionNode(new TernaryNode(array(
          'condition'     => new VariableNode('f'),
          'expression'    => new IntegerNode('1'),
          'conditional'   => new IntegerNode('2'),
        )))
      ))), $this->parse('
        $i ?: ($f ? 1 : 2);
      '));
    }
  }
?>
