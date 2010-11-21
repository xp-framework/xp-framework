<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'xp.compiler.optimize.Optimizations',
    'xp.compiler.optimize.InlineMethodCalls',
    'xp.compiler.optimize.InlineStaticMethodCalls',
    'xp.compiler.ast.MethodCallNode',
    'xp.compiler.ast.StaticMethodCallNode',
    'xp.compiler.ast.MethodNode',
    'xp.compiler.ast.MemberAccessNode',
    'xp.compiler.ast.VariableNode',
    'xp.compiler.ast.UnaryOpNode',
    'xp.compiler.ast.BinaryOpNode',
    'xp.compiler.ast.ReturnNode',
    'xp.compiler.ast.ParseTree',
    'xp.compiler.types.TypeName',
    'xp.compiler.types.MethodScope',
    'xp.compiler.types.TypeDeclaration',
    'xp.compiler.syntax.xp.Lexer'
  );

  /**
   * TestCase for Inlining operations
   *
   * @see      xp://xp.compiler.optimize.InliningOptimization
   */
  class InliningOptimizationTest extends TestCase {
    protected $fixture = NULL;
    protected $scope = NULL;
  
    /**
     * Sets up test case
     *
     */
    public function setUp() {
      $this->fixture= new Optimizations();
      $this->fixture->add(new InlineMethodCalls());
      $this->fixture->add(new InlineStaticMethodCalls());
      
      // Declare class
      $class= new ClassNode(MODIFIER_PUBLIC, array(), new TypeName('Test'), NULL, array(), array());
      
      // Declare scope and inject resolved types
      $this->scope= new MethodScope();
      $this->scope->declarations[0]= $class;
      $this->scope->setType(new VariableNode('this'), $class->name);
      $this->scope->addResolved('self', new TypeDeclaration(new ParseTree('', array(), $class)));
    }
    
    /**
     * Wrapper around fixture's optimize() method
     *
     * @param   xp.compiler.ast.Node call
     * @param   xp.compiler.ast.MethodNode[] declarations
     * @return  xp.compiler.ast.Node
     */
    protected function optimize($call, $declarations) {
      $this->scope->declarations[0]->body= $declarations;
      return $this->fixture->optimize($call, $this->scope);
    }
    
    /**
     * Test instance methods
     *
     */
    #[@test]
    public function oneLineInstanceMethod() {
      $call= new MethodCallNode(new VariableNode('this'), 'inc', array(new VariableNode('a')));
      $this->assertEquals(
        new UnaryOpNode(array('op' => '++', 'postfix' => FALSE, 'expression' => new VariableNode('a'))), 
        $this->optimize($call, array(new MethodNode(array(
          'modifiers'   => MODIFIER_INLINE,
          'name'        => 'inc',
          'parameters'  => array(array('name' => 'in')),
          'body'        => array(
            new ReturnNode(
              new UnaryOpNode(array('op' => '++', 'postfix' => FALSE, 'expression' => new VariableNode('in')))
            )
          )
        ))))
      );
    }

    /**
     * Test instance methods
     *
     */
    #[@test]
    public function noInstanceMethodOptimizationWithoutInlineFlag() {
      $call= new MethodCallNode(new VariableNode('this'), 'inc', array(new VariableNode('a')));
      $this->assertEquals(
        $call, 
        $this->optimize($call, array(new MethodNode(array(
          'modifiers'   => 0,
          'name'        => 'inc',
          'parameters'  => array(array('name' => 'in')),
          'body'        => array(
            new ReturnNode(
              new UnaryOpNode(array('op' => '++', 'postfix' => FALSE, 'expression' => new VariableNode('in')))
            )
          )
        ))))
      );
    }

    /**
     * Test static methods
     *
     */
    #[@test]
    public function oneLineStaticMethod() {
      $call= new StaticMethodCallNode(new TypeName('self'), 'inc', array(new VariableNode('a')));
      $this->assertEquals(
        new UnaryOpNode(array('op' => '++', 'postfix' => FALSE, 'expression' => new VariableNode('a'))), 
        $this->optimize($call, array(new MethodNode(array(
          'modifiers'   => MODIFIER_INLINE | MODIFIER_STATIC,
          'name'        => 'inc',
          'parameters'  => array(array('name' => 'in')),
          'body'        => array(
            new ReturnNode(
              new UnaryOpNode(array('op' => '++', 'postfix' => FALSE, 'expression' => new VariableNode('in')))
            )
          )
        ))))
      );
    }

    /**
     * Test static methods
     *
     */
    #[@test]
    public function noStaticMethodOptimizationWithoutInlineFlag() {
      $call= new StaticMethodCallNode(new TypeName('self'), 'inc', array(new VariableNode('a')));
      $this->assertEquals(
        $call, 
        $this->optimize($call, array(new MethodNode(array(
          'modifiers'   => MODIFIER_STATIC,
          'name'        => 'inc',
          'parameters'  => array(array('name' => 'in')),
          'body'        => array(
            new ReturnNode(
              new UnaryOpNode(array('op' => '++', 'postfix' => FALSE, 'expression' => new VariableNode('in')))
            )
          )
        ))))
      );
    }

    /**
     * Test parameter rewriting with the following declaration:
     * <code>
     *   inline T add(T $x, T $y) { return $x + $y; }
     * </code>
     */
    #[@test]
    public function parameterRewritingWithTwoParameters() {
      $call= new MethodCallNode(new VariableNode('this'), 'add', array(new VariableNode('a'), new VariableNode('b')));
      $this->assertEquals(
        new BinaryOpNode(array('lhs' => new VariableNode('a'), 'rhs' => new VariableNode('b'), 'op' => '+')), 
        $this->optimize($call, array(new MethodNode(array(
          'modifiers'   => MODIFIER_INLINE,
          'name'        => 'add',
          'parameters'  => array(array('name' => 'x'), array('name' => 'y')),
          'body'        => array(
            new ReturnNode(
              new BinaryOpNode(array('lhs' => new VariableNode('x'), 'rhs' => new VariableNode('y'), 'op' => '+'))
            )
          )
        ))))
      );
    }

    /**
     * Test parameter rewriting with the following declaration:
     * <code>
     *   inline T inc(T $x) { return $x + $this.step; }
     * </code>
     */
    #[@test]
    public function parameterRewritingWithOneParameterAndOneMember() {
      $call= new MethodCallNode(new VariableNode('this'), 'inc', array(new VariableNode('a')));
      $this->assertEquals(
        new BinaryOpNode(array('lhs' => new VariableNode('a'), 'rhs' => new MemberAccessNode(new VariableNode('this'), 'step'), 'op' => '+')), 
        $this->optimize($call, array(new MethodNode(array(
          'modifiers'   => MODIFIER_INLINE,
          'name'        => 'inc',
          'parameters'  => array(array('name' => 'x')),
          'body'        => array(
            new ReturnNode(
              new BinaryOpNode(array('lhs' => new VariableNode('x'), 'rhs' => new MemberAccessNode(new VariableNode('this'), 'step'), 'op' => '+'))
            )
          )
        ))))
      );
    }
  }
?>
