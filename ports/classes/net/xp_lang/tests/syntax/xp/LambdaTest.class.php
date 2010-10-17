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
  class LambdaTest extends ParserTestCase {

    /**
     * Test expression lambda
     *
     */
    #[@test]
    public function expression() {
      $this->assertEquals(
        array(new LambdaNode(
          array(new VariableNode('a')),
          array(new ReturnNode(new BinaryOpNode(array(
            'lhs' => new VariableNode('a'),
            'rhs' => new IntegerNode('1'),
            'op'  => '+'
          ))))
        )), 
        $this->parse('#{ $a -> $a + 1 };')
      );
    }

    /**
     * Test statement lambda
     *
     */
    #[@test]
    public function statement() {
      $this->assertEquals(
        array(new LambdaNode(
          array(new VariableNode('a')),
          array(new ReturnNode(new BinaryOpNode(array(
            'lhs' => new VariableNode('a'),
            'rhs' => new IntegerNode('1'),
            'op'  => '+'
          ))))
        )), 
        $this->parse('#{ $a -> { return $a + 1; } };')
      );
    }

    /**
     * Test statement lambda
     *
     */
    #[@test]
    public function multipleStatements() {
      $this->assertEquals(
        array(new LambdaNode(
          array(new VariableNode('a')),
          array(
            new AssignmentNode(array(
              'variable'    => new VariableNode('a'),
              'expression'  => new IntegerNode('1'),
              'op'          => '+='
            )),
            new ReturnNode(new VariableNode('a'))
          )
        )), 
        $this->parse('#{ $a -> { $a+= 1; return $a; } };')
      );
    }

    /**
     * Test statement lambda
     *
     */
    #[@test]
    public function noStatements() {
      $this->assertEquals(
        array(new LambdaNode(
          array(new VariableNode('a')),
          array()
        )), 
        $this->parse('#{ $a -> { } };')
      );
    }

    /**
     * Test parameter enclosed with brackets
     *
     */
    #[@test]
    public function typedParameterWithBrackets() {
      $this->assertEquals(
        array(new LambdaNode(
          array(new VariableNode('a')),
          array(new ReturnNode(new BinaryOpNode(array(
            'lhs' => new VariableNode('a'),
            'rhs' => new IntegerNode('1'),
            'op'  => '+'
          ))))
        )), 
        $this->parse('#{ int $a -> { return $a + 1; } };')
      );
    }

    /**
     * Test parameters enclosed with brackets
     *
     */
    #[@test]
    public function parametersWithBrackets() {
      $this->assertEquals(
        array(new LambdaNode(
          array(new VariableNode('a'), new VariableNode('b')),
          array(new ReturnNode(new BinaryOpNode(array(
            'lhs' => new VariableNode('a'),
            'rhs' => new VariableNode('b'),
            'op'  => '+'
          ))))
        )), 
        $this->parse('#{ $a, $b -> { return $a + $b; } };')
      );
    }

    /**
     * Test parameters enclosed with brackets
     *
     */
    #[@test]
    public function typedParametersWithBrackets() {
      $this->assertEquals(
        array(new LambdaNode(
          array(new VariableNode('a'), new VariableNode('b')),
          array(new ReturnNode(new BinaryOpNode(array(
            'lhs' => new VariableNode('a'),
            'rhs' => new VariableNode('b'),
            'op'  => '+'
          ))))
        )), 
        $this->parse('#{ int $a, int $b -> { return $a + $b; } };')
      );
    }

    /**
     * Test parameters
     *
     */
    #[@test]
    public function emptyParameters() {
      $this->assertEquals(
        array(new LambdaNode(
          array(),
          array(new ReturnNode(new StaticMethodCallNode(
            new TypeName('Console'),
            'write', 
            array(new StringNode('Hello'))
          )))
        )), 
        $this->parse('#{ -> Console::write("Hello") };')
      );
    }
  }
?>
