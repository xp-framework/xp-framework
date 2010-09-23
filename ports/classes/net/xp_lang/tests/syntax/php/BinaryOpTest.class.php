<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  $package= 'net.xp_lang.tests.syntax.php';

  uses('net.xp_lang.tests.syntax.php.ParserTestCase');

  /**
   * TestCase
   *
   */
  class net·xp_lang·tests·syntax·php·BinaryOpTest extends net·xp_lang·tests·syntax·php·ParserTestCase {
  
    /**
     * Test addition operator
     *
     */
    #[@test]
    public function addition() {
      $this->assertEquals(array(new BinaryOpNode(array(
        'lhs'           => new VariableNode('i'),
        'rhs'           => new IntegerNode('10'),
        'op'            => '+'
      ))), $this->parse('$i + 10;'));
    }

    /**
     * Test subtraction operator
     *
     */
    #[@test]
    public function subtraction() {
      $this->assertEquals(array(new BinaryOpNode(array(
        'lhs'           => new VariableNode('i'),
        'rhs'           => new IntegerNode('10'),
        'op'            => '-'
      ))), $this->parse('$i - 10;'));
    }

    /**
     * Test multiplication operator
     *
     */
    #[@test]
    public function multiplication() {
      $this->assertEquals(array(new BinaryOpNode(array(
        'lhs'           => new VariableNode('i'),
        'rhs'           => new IntegerNode('10'),
        'op'            => '*'
      ))), $this->parse('$i * 10;'));
    }

    /**
     * Test division operator
     *
     */
    #[@test]
    public function division() {
      $this->assertEquals(array(new BinaryOpNode(array(
        'lhs'           => new VariableNode('i'),
        'rhs'           => new IntegerNode('10'),
        'op'            => '/'
      ))), $this->parse('$i / 10;'));
    }

    /**
     * Test modulo operator
     *
     */
    #[@test]
    public function modulo() {
      $this->assertEquals(array(new BinaryOpNode(array(
        'lhs'           => new VariableNode('i'),
        'rhs'           => new IntegerNode('10'),
        'op'            => '%'
      ))), $this->parse('$i % 10;'));
    }

    /**
     * Test brackets used for precedence
     *
     */
    #[@test]
    public function bracketsUsedForPrecedence() {
      $this->assertEquals(
        array(new BinaryOpNode(array(
          'lhs' => new BracedExpressionNode(new BinaryOpNode(array(
            'lhs'    => new IntegerNode('5'),
            'rhs'    => new IntegerNode('6'),
            'op'     => '+'
          ))),
          'rhs' => new IntegerNode('3'),
          'op'  => '*'
        ))), 
        $this->parse('(5 + 6) * 3;')
      );
    }

    /**
     * Test brackets used for precedence
     *
     */
    #[@test]
    public function bracketsUsedForPrecedenceWithVariable() {
      $this->assertEquals(
        array(new BinaryOpNode(array(
          'lhs' => new BracedExpressionNode(new BinaryOpNode(array(
            'lhs'    => new VariableNode('i'),
            'rhs'    => new IntegerNode('6'),
            'op'     => '+'
          ))),
          'rhs' => new IntegerNode('3'),
          'op'  => '*'
        ))), 
        $this->parse('($i + 6) * 3;')
      );
    }

    /**
     * Test concatenation
     *
     */
    #[@test]
    public function concatenation() {
      $this->assertEquals(
        array(new BinaryOpNode(array(
          'lhs' => new StringNode('Hello'),
          'rhs' => new StringNode('World'),
          'op'  => '~'
        ))), 
        $this->parse('"Hello"."World";')
      );
    }

    /**
     * Test concatenation
     *
     */
    #[@test]
    public function bracketsInConcatenation() {
      $this->assertEquals(
        array(new BinaryOpNode(array(
          'lhs' => new StringNode('Hello #'),
          'rhs' => new BracedExpressionNode(new BinaryOpNode(array(
            'lhs' => new VariableNode('i'),
            'rhs' => new IntegerNode('1'),
            'op'  => '+'
          ))),
          'op'  => '~'
        ))), 
        $this->parse('"Hello #".($i + 1);')
      );
    }
  }
?>
