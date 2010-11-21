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
      $class= new ClassNode(MODIFIER_PUBLIC, array(), new TypeName('Test'), NULL, array(), array(
      
        // inline T tinc(T $in) { return ++$in; }
        new MethodNode(array(
          'modifiers'   => MODIFIER_INLINE,
          'name'        => 'tinc',
          'parameters'  => array(array('name' => 'in')),
          'body'        => array(
            new ReturnNode(
              new UnaryOpNode(array('op' => '++', 'postfix' => FALSE, 'expression' => new VariableNode('in')))
            )
          )
        )),

        // static inline T sinc(T $in) { return ++$in; }
        new MethodNode(array(
          'modifiers'   => MODIFIER_INLINE | MODIFIER_STATIC,
          'name'        => 'sinc',
          'parameters'  => array(array('name' => 'in')),
          'body'        => array(
            new ReturnNode(
              new UnaryOpNode(array('op' => '++', 'postfix' => FALSE, 'expression' => new VariableNode('in')))
            )
          )
        ))
      ));
      $declaration= new TypeDeclaration(new ParseTree('', array(), $class));
      
      $this->scope= new MethodScope();
      $this->scope->declarations[0]= $class;
      $this->scope->setType(new VariableNode('this'), $class->name);
      $this->scope->addResolved('self', $declaration);
    }
    
    /**
     * Test 
     *
     */
    #[@test]
    public function oneLineInstanceMethod() {
      $call= new MethodCallNode(new VariableNode('this'), 'tinc', array(new VariableNode('a')));
      $this->assertEquals(
        new UnaryOpNode(array('op' => '++', 'postfix' => FALSE, 'expression' => new VariableNode('a'))), 
        $this->fixture->optimize($call, $this->scope)
      );
    }

    /**
     * Test 
     *
     */
    #[@test]
    public function oneLineStaticMethod() {
      $call= new StaticMethodCallNode(new TypeName('self'), 'sinc', array(new VariableNode('a')));
      $this->assertEquals(
        new UnaryOpNode(array('op' => '++', 'postfix' => FALSE, 'expression' => new VariableNode('a'))), 
        $this->fixture->optimize($call, $this->scope)
      );
    }
  }
?>
