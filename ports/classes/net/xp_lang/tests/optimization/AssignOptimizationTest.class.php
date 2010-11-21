<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'xp.compiler.optimize.Optimizations',
    'xp.compiler.optimize.AssignOptimization',
    'xp.compiler.ast.AssignmentNode',
    'xp.compiler.ast.VariableNode',
    'xp.compiler.ast.MemberAccessNode',
    'xp.compiler.ast.StaticMemberAccessNode',
    'xp.compiler.ast.BinaryOpNode',
    'xp.compiler.ast.UnaryOpNode',
    'xp.compiler.types.MethodScope'
  );

  /**
   * TestCase for binary operations
   *
   * @see      xp://xp.compiler.optimize.AssignOptimization
   */
  class AssignOptimizationTest extends TestCase {
    protected $fixture = NULL;
  
    /**
     * Sets up test case
     *
     */
    public function setUp() {
      $this->fixture= new Optimizations();
      $this->fixture->add(new AssignOptimization());
    }
    
    /**
     * Wrapper around fixture's optimize() method
     *
     * @param   xp.compiler.ast.AssignmentNode
     * @return  xp.compiler.ast.Node
     */
    protected function optimize($assignment) {
      return $this->fixture->optimize($assignment, new MethodScope());
    }
    
    /**
     * Test optimizing $a= $a + $b; to $a+= $b;
     *
     */
    #[@test]
    public function addition() {
      $this->assertEquals(
        new AssignmentNode(array('variable' => new VariableNode('a'), 'op' => '+=', 'expression' => new VariableNode('b'))),
        $this->optimize(new AssignmentNode(array(
          'variable'   => new VariableNode('a'), 
          'op'         => '=', 
          'expression' => new BinaryOpNode(array('lhs' => new VariableNode('a'), 'op' => '+', 'rhs' => new VariableNode('b')))
        )))
      );
    }

    /**
     * Test optimizing $a= $a - $b; to $a-= $b;
     *
     */
    #[@test]
    public function subtraction() {
      $this->assertEquals(
        new AssignmentNode(array('variable' => new VariableNode('a'), 'op' => '-=', 'expression' => new VariableNode('b'))),
        $this->optimize(new AssignmentNode(array(
          'variable'   => new VariableNode('a'), 
          'op'         => '=', 
          'expression' => new BinaryOpNode(array('lhs' => new VariableNode('a'), 'op' => '-', 'rhs' => new VariableNode('b')))
        )))
      );
    }

    /**
     * Test optimizing $a= $a * $b; to $a*= $b;
     *
     */
    #[@test]
    public function multiplication() {
      $this->assertEquals(
        new AssignmentNode(array('variable' => new VariableNode('a'), 'op' => '*=', 'expression' => new VariableNode('b'))),
        $this->optimize(new AssignmentNode(array(
          'variable'   => new VariableNode('a'), 
          'op'         => '=', 
          'expression' => new BinaryOpNode(array('lhs' => new VariableNode('a'), 'op' => '*', 'rhs' => new VariableNode('b')))
        )))
      );
    }

    /**
     * Test optimizing $a= $a / $b; to $a/= $b;
     *
     */
    #[@test]
    public function division() {
      $this->assertEquals(
        new AssignmentNode(array('variable' => new VariableNode('a'), 'op' => '/=', 'expression' => new VariableNode('b'))),
        $this->optimize(new AssignmentNode(array(
          'variable'   => new VariableNode('a'), 
          'op'         => '=', 
          'expression' => new BinaryOpNode(array('lhs' => new VariableNode('a'), 'op' => '/', 'rhs' => new VariableNode('b')))
        )))
      );
    }

    /**
     * Test optimizing $a= $a % $b; to $a%= $b;
     *
     */
    #[@test]
    public function modulo() {
      $this->assertEquals(
        new AssignmentNode(array('variable' => new VariableNode('a'), 'op' => '%=', 'expression' => new VariableNode('b'))),
        $this->optimize(new AssignmentNode(array(
          'variable'   => new VariableNode('a'), 
          'op'         => '=', 
          'expression' => new BinaryOpNode(array('lhs' => new VariableNode('a'), 'op' => '%', 'rhs' => new VariableNode('b')))
        )))
      );
    }

    /**
     * Test optimizing $a= $a ~ $b; to $a~= $b;
     *
     */
    #[@test]
    public function concat() {
      $this->assertEquals(
        new AssignmentNode(array('variable' => new VariableNode('a'), 'op' => '~=', 'expression' => new VariableNode('b'))),
        $this->optimize(new AssignmentNode(array(
          'variable'   => new VariableNode('a'), 
          'op'         => '=', 
          'expression' => new BinaryOpNode(array('lhs' => new VariableNode('a'), 'op' => '~', 'rhs' => new VariableNode('b')))
        )))
      );
    }

    /**
     * Test optimizing $a= $a >> $b; to $a>>= $b;
     *
     */
    #[@test]
    public function shiftRight() {
      $this->assertEquals(
        new AssignmentNode(array('variable' => new VariableNode('a'), 'op' => '>>=', 'expression' => new VariableNode('b'))),
        $this->optimize(new AssignmentNode(array(
          'variable'   => new VariableNode('a'), 
          'op'         => '=', 
          'expression' => new BinaryOpNode(array('lhs' => new VariableNode('a'), 'op' => '>>', 'rhs' => new VariableNode('b')))
        )))
      );
    }

    /**
     * Test optimizing $a= $a << $b; to $a<<= $b;
     *
     */
    #[@test]
    public function shiftLeft() {
      $this->assertEquals(
        new AssignmentNode(array('variable' => new VariableNode('a'), 'op' => '<<=', 'expression' => new VariableNode('b'))),
        $this->optimize(new AssignmentNode(array(
          'variable'   => new VariableNode('a'), 
          'op'         => '=', 
          'expression' => new BinaryOpNode(array('lhs' => new VariableNode('a'), 'op' => '<<', 'rhs' => new VariableNode('b')))
        )))
      );
    }

    /**
     * Test optimizing $a= $a & $b; to $a&= $b;
     *
     */
    #[@test]
    public function logicalAnd() {
      $this->assertEquals(
        new AssignmentNode(array('variable' => new VariableNode('a'), 'op' => '&=', 'expression' => new VariableNode('b'))),
        $this->optimize(new AssignmentNode(array(
          'variable'   => new VariableNode('a'), 
          'op'         => '=', 
          'expression' => new BinaryOpNode(array('lhs' => new VariableNode('a'), 'op' => '&', 'rhs' => new VariableNode('b')))
        )))
      );
    }

    /**
     * Test optimizing $a= $a | $b; to $a|= $b;
     *
     */
    #[@test]
    public function logicalOr() {
      $this->assertEquals(
        new AssignmentNode(array('variable' => new VariableNode('a'), 'op' => '|=', 'expression' => new VariableNode('b'))),
        $this->optimize(new AssignmentNode(array(
          'variable'   => new VariableNode('a'), 
          'op'         => '=', 
          'expression' => new BinaryOpNode(array('lhs' => new VariableNode('a'), 'op' => '|', 'rhs' => new VariableNode('b')))
        )))
      );
    }

    /**
     * Test optimizing $a= $a ^ $b; to $a^= $b;
     *
     */
    #[@test]
    public function logicalXor() {
      $this->assertEquals(
        new AssignmentNode(array('variable' => new VariableNode('a'), 'op' => '^=', 'expression' => new VariableNode('b'))),
        $this->optimize(new AssignmentNode(array(
          'variable'   => new VariableNode('a'), 
          'op'         => '=', 
          'expression' => new BinaryOpNode(array('lhs' => new VariableNode('a'), 'op' => '^', 'rhs' => new VariableNode('b')))
        )))
      );
    }

    /**
     * Test $this.a= $this.a + $b; is optimized
     *
     */
    #[@test]
    public function instanceMemberVariables() {
      $this->assertEquals(
        new AssignmentNode(array('variable' => new MemberAccessNode(new VariableNode('this'), 'a'), 'op' => '+=', 'expression' => new VariableNode('b'))),
        $this->optimize(new AssignmentNode(array(
          'variable'   => new MemberAccessNode(new VariableNode('this'), 'a'), 
          'op'         => '=', 
          'expression' => new BinaryOpNode(array('lhs' => new MemberAccessNode(new VariableNode('this'), 'a'), 'op' => '+', 'rhs' => new VariableNode('b')))
        )))
      );
    }

    /**
     * Test self::a= self::a + $b; is optimized
     *
     */
    #[@test]
    public function staticMemberVariables() {
      $this->assertEquals(
        new AssignmentNode(array('variable' => new StaticMemberAccessNode(new TypeName('self'), 'a'), 'op' => '+=', 'expression' => new VariableNode('b'))),
        $this->optimize(new AssignmentNode(array(
          'variable'   => new StaticMemberAccessNode(new TypeName('self'), 'a'), 
          'op'         => '=', 
          'expression' => new BinaryOpNode(array('lhs' => new StaticMemberAccessNode(new TypeName('self'), 'a'), 'op' => '+', 'rhs' => new VariableNode('b')))
        )))
      );
    }

    /**
     * Test $this.a= $a + $b; is not optimized
     *
     */
    #[@test]
    public function notOptimizedIfInstanceMemberAssignToLocal() {
      $assignment= new AssignmentNode(array(
        'variable'   => new MemberAccessNode(new VariableNode('this'), 'a'), 
        'op'         => '=', 
        'expression' => new BinaryOpNode(array('lhs' => new VariableNode('a'), 'op' => '+', 'rhs' => new VariableNode('b')))
      ));
      $this->assertEquals($assignment, $this->optimize($assignment));
    }

    /**
     * Test $a= $this.a + $b; is not optimized
     *
     */
    #[@test]
    public function notOptimizedIfLocalAssignToInstanceMember() {
      $assignment= new AssignmentNode(array(
        'variable'   => new VariableNode('a'), 
        'op'         => '=', 
        'expression' => new BinaryOpNode(array('lhs' => new MemberAccessNode(new VariableNode('this'), 'a'), 'op' => '+', 'rhs' => new VariableNode('b')))
      ));
      $this->assertEquals($assignment, $this->optimize($assignment));
    }

    /**
     * Test self::a= $a + $b; is not optimized
     *
     */
    #[@test]
    public function notOptimizedIfStaticMemberAssignToLocal() {
      $assignment= new AssignmentNode(array(
        'variable'   => new StaticMemberAccessNode(new TypeName('self'), 'a'), 
        'op'         => '=', 
        'expression' => new BinaryOpNode(array('lhs' => new VariableNode('a'), 'op' => '+', 'rhs' => new VariableNode('b')))
      ));
      $this->assertEquals($assignment, $this->optimize($assignment));
    }

    /**
     * Test $a= self::a + $b; is not optimized
     *
     */
    #[@test]
    public function notOptimizedIfLocalAssignToStaticMember() {
      $assignment= new AssignmentNode(array(
        'variable'   => new VariableNode('a'), 
        'op'         => '=', 
        'expression' => new BinaryOpNode(array('lhs' => new StaticMemberAccessNode(new TypeName('self'), 'a'), 'op' => '+', 'rhs' => new VariableNode('b')))
      ));
      $this->assertEquals($assignment, $this->optimize($assignment));
    }

    /**
     * Test $a= $b + $c is not optimized
     *
     */
    #[@test]
    public function notOptimizedIfNotLHS() {
      $assignment= new AssignmentNode(array(
        'variable'   => new VariableNode('a'), 
        'op'         => '=', 
        'expression' => new BinaryOpNode(array('lhs' => new VariableNode('b'), 'op' => '+', 'rhs' => new VariableNode('c')))
      ));
      $this->assertEquals($assignment, $this->optimize($assignment));
    }

    /**
     * Test $a= $b + $a is not optimized
     *
     */
    #[@test]
    public function notOptimizedIfRHS() {
      $assignment= new AssignmentNode(array(
        'variable'   => new VariableNode('a'), 
        'op'         => '=', 
        'expression' => new BinaryOpNode(array('lhs' => new VariableNode('b'), 'op' => '+', 'rhs' => new VariableNode('a')))
      ));
      $this->assertEquals($assignment, $this->optimize($assignment));
    }

    /**
     * Test $a-= -$b; is optimized to $a+= $b;
     *
     */
    #[@test]
    public function minusAssignAndUnaryMinus() {
      $this->assertEquals(
        new AssignmentNode(array(
          'variable'   => new VariableNode('a'), 
          'op'         => '+=', 
          'expression' => new VariableNode('b')
        )),
        $this->optimize(new AssignmentNode(array(
          'variable'   => new VariableNode('a'), 
          'op'         => '-=', 
          'expression' => new UnaryOpNode(array('op' => '-', 'postfix' => FALSE, 'expression' => new VariableNode('b')))
        )))
      );
    }

    /**
     * Test $a+= -$b; is optimized to $a-= $b;
     *
     */
    #[@test]
    public function plusAssignAndUnaryMinus() {
      $this->assertEquals(
        new AssignmentNode(array(
          'variable'   => new VariableNode('a'), 
          'op'         => '-=', 
          'expression' => new VariableNode('b')
        )),
        $this->optimize(new AssignmentNode(array(
          'variable'   => new VariableNode('a'), 
          'op'         => '+=', 
          'expression' => new UnaryOpNode(array('op' => '-', 'postfix' => FALSE, 'expression' => new VariableNode('b')))
        )))
      );
    }

    /**
     * Test $a+= -$b; is optimized to $a-= $b;
     *
     */
    #[@test]
    public function timesAssignAndUnaryMinusNotOptimized() {
      $assignment= new AssignmentNode(array(
        'variable'   => new VariableNode('a'), 
        'op'         => '*=', 
        'expression' => new UnaryOpNode(array('op' => '-', 'postfix' => FALSE, 'expression' => new VariableNode('b')))
      ));
      $this->assertEquals($assignment, $this->optimize($assignment));
    }
  }
?>
