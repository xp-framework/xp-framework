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
  class net·xp_lang·tests·syntax·php·ComparisonTest extends net·xp_lang·tests·syntax·php·ParserTestCase {
  
    /**
     * Test equality comparison <tt>$i == 10</tt>
     *
     */
    #[@test]
    public function equality() {
      $this->assertEquals(array(new ComparisonNode(array(
        'lhs'           => new VariableNode('i'),
        'rhs'           => new IntegerNode('10'),
        'op'            => '=='
      ))), $this->parse('
        $i == 10;
      '));
    }

    /**
     * Test identity comparison <tt>$i === 10</tt>
     *
     */
    #[@test]
    public function identity() {
      $this->assertEquals(array(new ComparisonNode(array(
        'lhs'           => new VariableNode('i'),
        'rhs'           => new IntegerNode('10'),
        'op'            => '==='
      ))), $this->parse('
        $i === 10;
      '));
    }

    /**
     * Test negative identity comparison <tt>$i !== 10</tt>
     *
     */
    #[@test]
    public function notIdentity() {
      $this->assertEquals(array(new ComparisonNode(array(
        'lhs'           => new VariableNode('i'),
        'rhs'           => new IntegerNode('10'),
        'op'            => '!=='
      ))), $this->parse('
        $i !== 10;
      '));
    }

    /**
     * Test equality comparison <tt>-10 == $i</tt>
     *
     */
    #[@test]
    public function equalityToNegativeLhs() {
      $this->assertEquals(array(new ComparisonNode(array(
        'lhs'           => new UnaryOpNode(array(
          'expression'    => new IntegerNode('10'),
          'op'            => '-'
        )),
        'rhs'           => new VariableNode('i'),
        'op'            => '=='
      ))), $this->parse('
        -10 == $i;
      '));
    }

    /**
     * Test unequality comparison
     *
     */
    #[@test]
    public function unEquality() {
      $this->assertEquals(array(new ComparisonNode(array(
        'lhs'           => new VariableNode('i'),
        'rhs'           => new IntegerNode('10'),
        'op'            => '!='
      ))), $this->parse('
        $i != 10;
      '));
    }

    /**
     * Test smaller than comparison
     *
     */
    #[@test]
    public function smallerThan() {
      $this->assertEquals(array(new ComparisonNode(array(
        'lhs'           => new VariableNode('i'),
        'rhs'           => new IntegerNode('10'),
        'op'            => '<'
      ))), $this->parse('
        $i < 10;
      '));
    }

    /**
     * Test smaller than or equal to comparison
     *
     */
    #[@test]
    public function smallerThanOrEqualTo() {
      $this->assertEquals(array(new ComparisonNode(array(
        'lhs'           => new VariableNode('i'),
        'rhs'           => new IntegerNode('10'),
        'op'            => '<='
      ))), $this->parse('
        $i <= 10;
      '));
    }

    /**
     * Test greater than comparison
     *
     */
    #[@test]
    public function greaterThan() {
      $this->assertEquals(array(new ComparisonNode(array(
        'lhs'           => new VariableNode('i'),
        'rhs'           => new IntegerNode('10'),
        'op'            => '>'
      ))), $this->parse('
        $i > 10;
      '));
    }

    /**
     * Test greather than or equal to comparison
     *
     */
    #[@test]
    public function greaterThanOrEqualTo() {
      $this->assertEquals(array(new ComparisonNode(array(
        'lhs'           => new VariableNode('i'),
        'rhs'           => new IntegerNode('10'),
        'op'            => '>='
      ))), $this->parse('
        $i >= 10;
      '));
    }
  }
?>
