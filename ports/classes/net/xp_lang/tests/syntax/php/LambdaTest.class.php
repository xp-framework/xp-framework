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
  class net·xp_lang·tests·syntax·php·LambdaTest extends net·xp_lang·tests·syntax·php·ParserTestCase {

    /**
     * Test simple lambda
     *
     */
    #[@test]
    public function noParameters() {
      $this->assertEquals(
        array(new LambdaNode(
          array(),
          array(new ReturnNode(new BooleanNode(TRUE)))
        )), 
        $this->parse('function() { return TRUE; };')
      );
    }
  
    /**
     * Test simple lambda
     *
     */
    #[@test]
    public function oneParameter() {
      $this->assertEquals(
        array(new LambdaNode(
          array(new VariableNode('a')),
          array(new ReturnNode(new BinaryOpNode(array(
            'lhs' => new VariableNode('a'),
            'rhs' => new IntegerNode('1'),
            'op'  => '+'
          ))))
        )), 
        $this->parse('function($a) { return $a + 1; };')
      );
    }

    /**
     * Test simple lambda
     *
     */
    #[@test]
    public function twoParameters() {
      $this->assertEquals(
        array(new LambdaNode(
          array(new VariableNode('a'), new VariableNode('b')),
          array(new ReturnNode(new BinaryOpNode(array(
            'lhs' => new VariableNode('a'),
            'rhs' => new VariableNode('b'),
            'op'  => '+'
          ))))
        )), 
        $this->parse('function($a, $b) { return $a + $b; };')
      );
    }
  }
?>
