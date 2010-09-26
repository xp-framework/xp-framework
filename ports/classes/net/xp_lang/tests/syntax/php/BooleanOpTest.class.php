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
  class net·xp_lang·tests·syntax·php·BooleanOpTest extends net·xp_lang·tests·syntax·php·ParserTestCase {
  
    /**
     * Test boolean "or" operator (||)
     *
     */
    #[@test]
    public function booleanOr() {
      $this->assertEquals(array(new BooleanOpNode(array(
        'lhs'           => new VariableNode('a'),
        'rhs'           => new VariableNode('b'),
        'op'            => '||'
      ))), $this->parse('$a || $b;'));
    }

    /**
     * Test boolean "and" operator (&&)
     *
     */
    #[@test]
    public function booleanAnd() {
      $this->assertEquals(array(new BooleanOpNode(array(
        'lhs'           => new VariableNode('a'),
        'rhs'           => new VariableNode('b'),
        'op'            => '&&'
      ))), $this->parse('$a && $b;'));
    }

    /**
     * Test the following code:
     *
     * <code>
     *   $a && $b+= 1;
     * </code>
     */
    #[@test]
    public function conditionalAssignment() {
      $this->assertEquals(array(new BooleanOpNode(array(
        'lhs'           => new VariableNode('a'),
        'rhs'           => new AssignmentNode(array(
          'variable'      => new VariableNode('b'),
          'expression'    => new IntegerNode('1'),
          'op'            => '+='
        )),
        'op'            => '&&'
      ))), $this->parse('$a && $b+= 1;'));
    }
  }
?>
