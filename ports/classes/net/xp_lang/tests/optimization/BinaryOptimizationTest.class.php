<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'xp.compiler.optimize.Optimizations',
    'xp.compiler.optimize.BinaryOptimization',
    'xp.compiler.ast.IntegerNode',
    'xp.compiler.ast.DecimalNode',
    'xp.compiler.ast.StringNode',
    'xp.compiler.types.MethodScope'
  );

  /**
   * TestCase for binary operations
   *
   * @see      xp://xp.compiler.optimize.BinaryOptimization
   */
  class BinaryOptimizationTest extends TestCase {
    protected $fixture = NULL;
  
    /**
     * Sets up test case
     *
     */
    public function setUp() {
      $this->fixture= new Optimizations();
      $this->fixture->add(new BinaryOptimization());
      $this->scope= new MethodScope();
    }
    
    /**
     * Test adding integers
     *
     */
    #[@test]
    public function addIntegers() {
      $this->assertEquals(new IntegerNode(1), $this->fixture->optimize(new BinaryOpNode(array(
        'lhs' => new IntegerNode('1'), 
        'rhs' => new IntegerNode('0'), 
        'op'  => '+'
      )), $this->scope));
    }

    /**
     * Test subtracting integers
     *
     */
    #[@test]
    public function subtractIntegers() {
      $this->assertEquals(new IntegerNode(-1), $this->fixture->optimize(new BinaryOpNode(array(
        'lhs' => new IntegerNode('1'), 
        'rhs' => new IntegerNode('2'), 
        'op'  => '-'
      )), $this->scope));
    }

    /**
     * Test multiplying integers
     *
     */
    #[@test]
    public function multiplyIntegers() {
      $this->assertEquals(new IntegerNode(2), $this->fixture->optimize(new BinaryOpNode(array(
        'lhs' => new IntegerNode('1'), 
        'rhs' => new IntegerNode('2'), 
        'op'  => '*'
      )), $this->scope));
    }

    /**
     * Test dividing integers
     *
     */
    #[@test]
    public function divideIntegers() {
      $this->assertEquals(new DecimalNode(2), $this->fixture->optimize(new BinaryOpNode(array(
        'lhs' => new IntegerNode('4'), 
        'rhs' => new IntegerNode('2'), 
        'op'  => '/'
      )), $this->scope));
    }

    /**
     * Test modulo division on integers
     *
     */
    #[@test]
    public function modIntegers() {
      $this->assertEquals(new IntegerNode(1), $this->fixture->optimize(new BinaryOpNode(array(
        'lhs' => new IntegerNode('4'), 
        'rhs' => new IntegerNode('3'), 
        'op'  => '%'
      )), $this->scope));
    }

    /**
     * Test shift right on integers
     *
     */
    #[@test]
    public function shrIntegers() {
      $this->assertEquals(new IntegerNode(2), $this->fixture->optimize(new BinaryOpNode(array(
        'lhs' => new IntegerNode('4'), 
        'rhs' => new IntegerNode('1'), 
        'op'  => '>>'
      )), $this->scope));
    }

    /**
     * Test shift left on integers
     *
     */
    #[@test]
    public function shlIntegers() {
      $this->assertEquals(new IntegerNode(32), $this->fixture->optimize(new BinaryOpNode(array(
        'lhs' => new IntegerNode('4'), 
        'rhs' => new IntegerNode('3'), 
        'op'  => '<<'
      )), $this->scope));
    }

    /**
     * Test "|" on integers
     *
     */
    #[@test]
    public function orIntegers() {
      $this->assertEquals(new IntegerNode(5), $this->fixture->optimize(new BinaryOpNode(array(
        'lhs' => new IntegerNode('4'), 
        'rhs' => new IntegerNode('1'), 
        'op'  => '|'
      )), $this->scope));
    }

    /**
     * Test "^" on integers
     *
     */
    #[@test]
    public function xorIntegers() {
      $this->assertEquals(new IntegerNode(5), $this->fixture->optimize(new BinaryOpNode(array(
        'lhs' => new IntegerNode('4'), 
        'rhs' => new IntegerNode('1'), 
        'op'  => '^'
      )), $this->scope));
    }

    /**
     * Test "&" on integers
     *
     */
    #[@test]
    public function andIntegers() {
      $this->assertEquals(new IntegerNode(0), $this->fixture->optimize(new BinaryOpNode(array(
        'lhs' => new IntegerNode('4'), 
        'rhs' => new IntegerNode('1'), 
        'op'  => '&'
      )), $this->scope));
    }

    /**
     * Test adding decimals
     *
     */
    #[@test]
    public function addDecimals() {
      $this->assertEquals(new DecimalNode(1.0), $this->fixture->optimize(new BinaryOpNode(array(
        'lhs' => new DecimalNode(1.0), 
        'rhs' => new DecimalNode(0.0), 
        'op'  => '+'
      )), $this->scope));
    }

    /**
     * Test subtracting decimals
     *
     */
    #[@test]
    public function subtractDecimals() {
      $this->assertEquals(new DecimalNode(-1.0), $this->fixture->optimize(new BinaryOpNode(array(
        'lhs' => new DecimalNode(1.0), 
        'rhs' => new DecimalNode(2.0), 
        'op'  => '-'
      )), $this->scope));
    }

    /**
     * Test multiplying decimals
     *
     */
    #[@test]
    public function multiplyDecimals() {
      $this->assertEquals(new DecimalNode(2.0), $this->fixture->optimize(new BinaryOpNode(array(
        'lhs' => new DecimalNode(1.0), 
        'rhs' => new DecimalNode(2.0), 
        'op'  => '*'
      )), $this->scope));
    }

    /**
     * Test dividing decimals
     *
     */
    #[@test]
    public function divideDecimals() {
      $this->assertEquals(new DecimalNode(2.0), $this->fixture->optimize(new BinaryOpNode(array(
        'lhs' => new DecimalNode(4.0), 
        'rhs' => new DecimalNode(2.0), 
        'op'  => '/'
      )), $this->scope));
    }

    /**
     * Test adding integers and decimals
     *
     */
    #[@test]
    public function addIntegerAndDecimal() {
      $this->assertEquals(new DecimalNode(1.0), $this->fixture->optimize(new BinaryOpNode(array(
        'lhs' => new IntegerNode('1'), 
        'rhs' => new DecimalNode(0.0), 
        'op'  => '+'
      )), $this->scope));
    }

    /**
     * Test subtracting integers and decimals
     *
     */
    #[@test]
    public function subtractIntegerAndDecimal() {
      $this->assertEquals(new DecimalNode(-1.0), $this->fixture->optimize(new BinaryOpNode(array(
        'lhs' => new IntegerNode('1'), 
        'rhs' => new DecimalNode(2.0), 
        'op'  => '-'
      )), $this->scope));
    }

    /**
     * Test multiplying integers and decimals
     *
     */
    #[@test]
    public function multiplyIntegerAndDecimal() {
      $this->assertEquals(new DecimalNode(2.0), $this->fixture->optimize(new BinaryOpNode(array(
        'lhs' => new IntegerNode('1'), 
        'rhs' => new DecimalNode(2.0), 
        'op'  => '*'
      )), $this->scope));
    }

    /**
     * Test dividing integers and decimals
     *
     */
    #[@test]
    public function divideIntegerAndDecimal() {
      $this->assertEquals(new DecimalNode(2.0), $this->fixture->optimize(new BinaryOpNode(array(
        'lhs' => new IntegerNode(4), 
        'rhs' => new DecimalNode(2.0), 
        'op'  => '/'
      )), $this->scope));
    }

    /**
     * Test adding strings
     *
     */
    #[@test]
    public function addStringsNotOptimized() {
      $o= new BinaryOpNode(array(
        'lhs' => new StringNode('a'), 
        'rhs' => new StringNode('b'), 
        'op'  => '+'
      ));
      $this->assertEquals($o, $this->fixture->optimize($o, $this->scope));
    }

    /**
     * Test subtracting strings
     *
     */
    #[@test]
    public function subtractStringsNotOptimized() {
      $o= new BinaryOpNode(array(
        'lhs' => new StringNode('a'), 
        'rhs' => new StringNode('b'), 
        'op'  => '-'
      ));
      $this->assertEquals($o, $this->fixture->optimize($o, $this->scope));
    }

    /**
     * Test multiplying strings
     *
     */
    #[@test]
    public function multiplyStringsNotOptimized() {
      $o= new BinaryOpNode(array(
        'lhs' => new StringNode('a'), 
        'rhs' => new StringNode('b'), 
        'op'  => '*'
      ));
      $this->assertEquals($o, $this->fixture->optimize($o, $this->scope));
    }

    /**
     * Test dividing strings
     *
     */
    #[@test]
    public function divideStringsNotOptimized() {
      $o= new BinaryOpNode(array(
        'lhs' => new StringNode('a'), 
        'rhs' => new StringNode('b'), 
        'op'  => '/'
      ));
      $this->assertEquals($o, $this->fixture->optimize($o, $this->scope));
    }

    /**
     * Test concatenating strings
     *
     */
    #[@test]
    public function concatenatingStrings() {
      $this->assertEquals(new StringNode('Hello World'), $this->fixture->optimize(new BinaryOpNode(array(
        'lhs' => new StringNode('Hello'), 
        'rhs' => new StringNode(' World'), 
        'op'  => '~'
      )), $this->scope));
    }

    /**
     * Test optimizing a more complex binary operation (1 + 2 * 3)
     *
     */
    #[@test]
    public function optimizeComplex() {
      $this->assertEquals(new IntegerNode(7), $this->fixture->optimize(new BinaryOpNode(array(
        'lhs' => new IntegerNode('1'), 
        'rhs' => new BinaryOpNode(array(
          'lhs' => new IntegerNode('2'), 
          'rhs' => new IntegerNode('3'), 
          'op'  => '*'
        )),
        'op'  => '+'
      )), $this->scope));
    }

    /**
     * Test optimizing a more complex binary operation ((1 + 2) * 3)
     *
     */
    #[@test]
    public function optimizeBraced() {
      $this->assertEquals(new IntegerNode(9), $this->fixture->optimize(new BinaryOpNode(array(
        'lhs' => new BracedExpressionNode(new BinaryOpNode(array(
          'lhs' => new IntegerNode('1'), 
          'rhs' => new IntegerNode('2'), 
          'op'  => '+'
        ))),
        'rhs' => new IntegerNode('3'), 
        'op'  => '*'
      )), $this->scope));
    }
  }
?>
