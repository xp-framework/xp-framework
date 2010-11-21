<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'xp.compiler.Syntax',
    'io.streams.MemoryInputStream',
    'xp.compiler.ast.Visitor'
  );

  /**
   * TestCase
   *
   * @see   xp://xp.compiler.ast.Visitor
   */
  class VisitorTest extends TestCase {
    protected static $visitor;
    
    static function __static() {
      self::$visitor= ClassLoader::defineClass('VisitorTest··Visitor', 'xp.compiler.ast.Visitor', array(), '{
        public $visited= array();
        public function visitOne($node) {
          $this->visited[]= $node;
          return parent::visitOne($node);
        }
      }');
    }
  
    /**
     * Assertion helper
     *
     * @param   xp.compiler.ast.Node[] nodes
     * @param   xp.compiler.ast.Node toVisit
     * @throws  unittest.AssertionFailedError
     */
    protected function assertVisited(array $nodes, xp·compiler·ast·Node $toVisit) {
      $visitor= self::$visitor->newInstance();
      $visitor->visitOne($toVisit);
      $this->assertEquals($nodes, $visitor->visited);
    }

    /**
     * Test visitAnnotation()
     *
     */
    #[@test]
    public function visitAnnotation() {
      $node= new AnnotationNode(array('type' => 'deprecated', 'parameters' => array()));
      $this->assertVisited(array($node), $node);
    }

    /**
     * Test visitAnnotation()
     *
     */
    #[@test]
    public function visitAnnotationWithParameters() {
      $node= new AnnotationNode(array('type' => 'deprecated', 'parameters' => array(
        new StringNode('Use other class instead')
      )));
      $this->assertVisited(array($node, $node->parameters[0]), $node);
    }

    /**
     * Test visitArrayAccess()
     *
     */
    #[@test]
    public function visitArrayAccess() {
      $node= new ArrayAccessNode(new VariableNode('a'), new IntegerNode(0));
      $this->assertVisited(array($node, $node->target, $node->offset), $node);
    }

    /**
     * Test visitArrayAccess()
     *
     */
    #[@test]
    public function visitArrayAccessWithoutOffset() {
      $node= new ArrayAccessNode(new VariableNode('a'), NULL);
      $this->assertVisited(array($node, $node->target), $node);
    }

    /**
     * Test visitArray()
     *
     */
    #[@test]
    public function visitArray() {
      $node= new ArrayNode(array('values' => array(new IntegerNode(0), new IntegerNode(1))));
      $this->assertVisited(array($node, $node->values[0], $node->values[1]), $node);
    }

    /**
     * Test visitArray()
     *
     */
    #[@test]
    public function visitEmptyArray() {
      $node= new ArrayNode(array('values' => array()));
      $this->assertVisited(array($node), $node);
    }

    /**
     * Test visitTernary()
     *
     */
    #[@test]
    public function visitTernary() {
      $node= new TernaryNode(array(
        'condition'   => new VariableNode('a'), 
        'expression'  => new VariableNode('a'), 
        'conditional' => new VariableNode('b')
      ));
      $this->assertVisited(array($node, $node->condition, $node->expression, $node->conditional), $node);
    }

    /**
     * Test visitAssignment()
     *
     */
    #[@test]
    public function visitAssignment() {
      $node= new AssignmentNode(array(
        'variable'   => new VariableNode('a'), 
        'expression' => new IntegerNode(0), 
        'op'         => '='
      ));
      $this->assertVisited(array($node, $node->variable, $node->expression), $node);
    }

    /**
     * Test visitBinaryOp()
     *
     */
    #[@test]
    public function visitBinaryOp() {
      $node= new BinaryOpNode(array(
        'lhs' => new VariableNode('a'), 
        'rhs' => new IntegerNode(0), 
        'op'  => '+'
      ));
      $this->assertVisited(array($node, $node->lhs, $node->rhs), $node);
    }

    /**
     * Test visitBoolean()
     *
     */
    #[@test]
    public function visitBoolean() {
      $node= new BooleanNode(TRUE);
      $this->assertVisited(array($node), $node);
    }

    /**
     * Test visitBooleanOp()
     *
     */
    #[@test]
    public function visitBooleanOp() {
      $node= new BooleanOpNode(array(
        'lhs' => new VariableNode('a'), 
        'rhs' => new IntegerNode(0), 
        'op'  => '&&'
      ));
      $this->assertVisited(array($node, $node->lhs, $node->rhs), $node);
    }

    /**
     * Test visitBreak()
     *
     */
    #[@test]
    public function visitBreak() {
      $node= new BreakNode();
      $this->assertVisited(array($node), $node);
    }

    /**
     * Test visitCase()
     *
     */
    #[@test]
    public function visitCase() {
      $node= new CaseNode(array(
        'expression' => new IntegerNode(0), 
        'statements' => array(new VariableNode('a'), new BreakNode())
      ));
      $this->assertVisited(array($node, $node->expression, $node->statements[0], $node->statements[1]), $node);
    }

    /**
     * Test visitTernary()
     *
     */
    #[@test]
    public function visitTernaryWithoutExpression() {
      $node= new TernaryNode(array(
        'condition'   => new VariableNode('a'), 
        'conditional' => new VariableNode('b')
      ));
      $this->assertVisited(array($node, $node->condition, $node->conditional), $node);
    }

    /**
     * Parse sourcecode
     *
     * @param   string source
     * @return  xp.compiler.ast.TypeDeclarationNode
     */
    protected function parse($source) {
      return Syntax::forName('xp')->parse(new MemoryInputStream($source))->declaration;
    }
  
    /**
     * Test finding variables
     *
     */
    #[@test]
    public function findVariables() {
      $visitor= newinstance('xp.compiler.ast.Visitor', array(), '{
        public $variables= array();
        protected function visitVariable(VariableNode $var) {
          $this->variables[$var->name]= TRUE;
          return $var;
        }
      }');
      $visitor->visitOne($this->parse('class Test {
        public int add(int $a, int $b) {
          return $a + $b;
        }

        public int subtract(int $a, int $b) {
          return $a - $b;
        }
      }'));
      
      $this->assertEquals(array('a', 'b'), array_keys($visitor->variables));
    }
  }
?>
