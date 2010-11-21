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
    'xp.compiler.ast.VariableNode',
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
      $declaration= new TypeDeclaration(new ParseTree('', array(), $class));
      
      // Declare scope and inject resolved types
      $this->scope= new MethodScope();
      $this->scope->declarations[0]= $class;
      $this->scope->setType(new VariableNode('this'), $class->name);
      $this->scope->addResolved('self', $declaration);
    }
    
    /**
     * Wrapper around fixture's optimize() method
     *
     * @param   xp.compiler.ast.Node call
     * @param   xp.compiler.ast.MethodNode declaration
     * @return  xp.compiler.ast.Node
     */
    public function optimize($call, MethodNode $declaration) {
      $this->scope->declarations[0]->body= array($declaration);
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
        $this->optimize($call, new MethodNode(array(
          'modifiers'   => MODIFIER_INLINE,
          'name'        => 'inc',
          'parameters'  => array(array('name' => 'in')),
          'body'        => array(
            new ReturnNode(
              new UnaryOpNode(array('op' => '++', 'postfix' => FALSE, 'expression' => new VariableNode('in')))
            )
          )
        )))
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
        $this->optimize($call, new MethodNode(array(
          'modifiers'   => 0,
          'name'        => 'inc',
          'parameters'  => array(array('name' => 'in')),
          'body'        => array(
            new ReturnNode(
              new UnaryOpNode(array('op' => '++', 'postfix' => FALSE, 'expression' => new VariableNode('in')))
            )
          )
        )))
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
        $this->optimize($call, new MethodNode(array(
          'modifiers'   => MODIFIER_INLINE | MODIFIER_STATIC,
          'name'        => 'inc',
          'parameters'  => array(array('name' => 'in')),
          'body'        => array(
            new ReturnNode(
              new UnaryOpNode(array('op' => '++', 'postfix' => FALSE, 'expression' => new VariableNode('in')))
            )
          )
        )))
      );
    }

    /**
     * Test instance methods
     *
     */
    #[@test]
    public function noStaticMethodOptimizationWithoutInlineFlag() {
      $call= new StaticMethodCallNode(new TypeName('self'), 'inc', array(new VariableNode('a')));
      $this->assertEquals(
        $call, 
        $this->optimize($call, new MethodNode(array(
          'modifiers'   => MODIFIER_STATIC,
          'name'        => 'inc',
          'parameters'  => array(array('name' => 'in')),
          'body'        => array(
            new ReturnNode(
              new UnaryOpNode(array('op' => '++', 'postfix' => FALSE, 'expression' => new VariableNode('in')))
            )
          )
        )))
      );
    }
  }
?>
