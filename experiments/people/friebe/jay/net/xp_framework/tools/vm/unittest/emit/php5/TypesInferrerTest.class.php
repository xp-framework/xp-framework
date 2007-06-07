<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'net.xp_framework.tools.vm.Parser',
    'net.xp_framework.tools.vm.Lexer',
    'net.xp_framework.tools.vm.emit.php5.Php5Emitter'
  );

  /**
   * Test types
   *
   * @purpose  Test Case
   */
  class TypesInferrerTest extends TestCase {
  
    /**
     * Setup test.
     *
     */
    public function setUp() {
      $this->emitter= new Php5Emitter();
      
      $parser= new Parser();
      $this->emitter->emitAll($parser->parse(new Lexer('
        import lang.Object;
        
        class Now {
          public string toString() {
            return date("r");
          }

          public string[] components() {
          }
        }
        
        class Test {
          public int $integer= 1;
          public Now $now= NULL;
          private php.ReflectionClass $r= NULL;
          
          public __construct() {
            $this->now= new Now();
          }
          
          public Now getNow() {
            return $this->now;
          }
          
          public string isSubclassOf(string $name) {
            $this->r= new php.ReflectionClass($this);
            return $this->r->isSubclassOf(new php.ReflectionClass($name));
          }
        }
        
        $c= new Now()->components();
        
        $i= 1;
        $chained_i= new Test()->integer;
        
        $s= "";
        $chained_s= new Test()->now->toString();
        
        $t= new Test();
        $chained_n= $t->getNow()->toString();
      ', __FILE__), '<setUp>'));
      if ($this->emitter->hasErrors()) {
        throw new PrerequisitesNotMetError('Fixture source contains errors '.xp::stringOf($this->emitter->getErrors()), 'parse');
      }
    }
    
    /**
     * Tests type of NewNode
     *
     * @see     xp://net.xp_framework.tools.vm.nodes.NewNode
     */
    #[@test]
    public function typeOfNew() {
      $this->assertEquals('lang·Object', $this->emitter->typeOf(new NewNode(new ClassReferenceNode('lang.Object'))));
    }

    /**
     * Tests type of NewNode
     *
     * @see     xp://net.xp_framework.tools.vm.nodes.NewNode
     */
    #[@test]
    public function typeOfUnqualifiedNew() {
      $this->assertEquals('lang·Object', $this->emitter->typeOf(new NewNode(new ClassReferenceNode('Object'))));
    }

    /**
     * Tests type of VariableNode
     *
     * @see     xp://net.xp_framework.tools.vm.nodes.VariableNode
     */
    #[@test]
    public function typeOfComponentsAssignment() {
      $this->assertEquals('string[]', $this->emitter->typeOf(new VariableNode('$c')));
    }

    /**
     * Tests type of VariableNode
     *
     * @see     xp://net.xp_framework.tools.vm.nodes.VariableNode
     */
    #[@test]
    public function typeOfVariableAssignedToConstant() {
      $this->assertEquals('int', $this->emitter->typeOf(new VariableNode('$i'))) &&
      $this->assertEquals('string', $this->emitter->typeOf(new VariableNode('$s'))) &&
      $this->assertEquals('main·Test', $this->emitter->typeOf(new VariableNode('$t')));
    }
  
    /**
     * Tests type of variables that have been assigned to chained expressions
     *
     * @see     xp://net.xp_framework.tools.vm.nodes.VariableNode
     */
    #[@test]
    public function typeOfVariableAssignedToChain() {
      $this->assertEquals('int', $this->emitter->typeOf(new VariableNode('$chained_i'))) &&
      $this->assertEquals('string', $this->emitter->typeOf(new VariableNode('$chained_s'))) &&
      $this->assertEquals('string', $this->emitter->typeOf(new VariableNode('$chained_n')));
    }
  }
?>
