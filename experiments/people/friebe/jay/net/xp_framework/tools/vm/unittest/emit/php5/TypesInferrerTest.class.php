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
     * @access  public
     */
    function setUp() {
      $this->emitter= &new Php5Emitter();
      
      $parser= &new Parser();
      $this->emitter->emitAll($parser->parse(new Lexer('
        import xp~lang~Object;
        
        class Now {
          public string toString() {
            return date("r");
          }
        }
        
        class Test {
          public $integer= 1;
          public self $now= NULL;
          
          public __construct() {
            $this->now= new Now();
          }
        }
        
        $i= 1;
        $chained_i= new Test()->integer;
        
        $s= "";
        $chained_s= new Test()->now->toString();
      '), '<setUp>'));
    }
    
    /**
     * Tests type of NewNode
     *
     * @see     xp://net.xp_framework.tools.vm.nodes.NewNode
     * @access  public
     */
    #[@test]
    function typeOfNew() {
      $this->assertEquals('xp·lang·Object', $this->emitter->typeOf(new NewNode(new ClassReferenceNode('xp~lang~Object'))));
    }

    /**
     * Tests type of NewNode
     *
     * @see     xp://net.xp_framework.tools.vm.nodes.NewNode
     * @access  public
     */
    #[@test]
    function typeOfUnqualifiedNew() {
      $this->assertEquals('xp·lang·Object', $this->emitter->typeOf(new NewNode(new ClassReferenceNode('Object'))));
    }

    /**
     * Tests type of VariableNode
     *
     * @see     xp://net.xp_framework.tools.vm.nodes.NewNode
     * @access  public
     */
    #[@test]
    function typeOfVariableAssignedToConstant() {
      $this->assertEquals('integer', $this->emitter->typeOf(new VariableNode('$i')));
      $this->assertEquals('string', $this->emitter->typeOf(new VariableNode('$s')));
    }
  
    /**
     * Tests type of variables that have been assigned to chained expressions
     *
     * @see     xp://net.xp_framework.tools.vm.nodes.NewNode
     * @access  public
     */
    #[@test, @ignore]
    function typeOfVariableAssignedToChain() {
      $this->assertEquals('integer', $this->emitter->typeOf(new VariableNode('$chained_i')));
      $this->assertEquals('string', $this->emitter->typeOf(new VariableNode('$chained_s')));
    }
  }
?>
