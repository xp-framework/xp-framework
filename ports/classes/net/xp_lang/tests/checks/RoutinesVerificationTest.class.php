<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'xp.compiler.checks.RoutinesVerification',
    'xp.compiler.ast.MethodNode',
    'xp.compiler.ast.InterfaceNode',
    'xp.compiler.ast.ClassNode',
    'xp.compiler.types.TypeDeclarationScope'
  );

  /**
   * TestCase
   *
   * @see      xp://xp.compiler.checks.RoutinesVerification
   */
  class RoutinesVerificationTest extends TestCase {
    protected $fixture= NULL;
  
    /**
     * Sets up test case
     *
     */
    public function setUp() {
      $this->fixture= new RoutinesVerification();
    }
    
    /**
     * Wrapper around verify
     *
     * @param   xp.compiler.ast.RoutineNode routine
     * @param   xp.compiler.ast.TypeDeclarationNode type
     * @return  var
     */
    protected function verify(RoutineNode $routine, TypeDeclarationNode $type) {
      $scope= new TypeDeclarationScope();
      $scope->declarations[0]= $type;
      return $this->fixture->verify($routine, $scope);
    }
    
    /**
     * Test interface methods
     *
     */
    #[@test]
    public function interfaceMethodsMayNotHaveBodies() {
      $m= new MethodNode(array(
        'name'        => 'run',
        'modifiers'   => MODIFIER_PUBLIC,
        'returns'     => TypeName::$VOID,
        'parameters'  => array(),
        'body'        => array()
      ));
      $this->assertEquals(
        array('R403', 'Interface methods may not have a body Runnable::run'), 
        $this->verify($m, new InterfaceNode(MODIFIER_PUBLIC, array(), new TypeName('Runnable')))
      );
    }

    /**
     * Test interface methods
     *
     */
    #[@test]
    public function interfaceMethodsMayNotBePrivate() {
      $m= new MethodNode(array(
        'name'        => 'run',
        'modifiers'   => MODIFIER_PRIVATE,
        'returns'     => TypeName::$VOID,
        'parameters'  => array(),
        'body'        => NULL
      ));
      $this->assertEquals(
        array('R401', 'Interface methods may only be public Runnable::run'), 
        $this->verify($m, new InterfaceNode(MODIFIER_PUBLIC, array(), new TypeName('Runnable')))
      );
    }

    /**
     * Test interface methods
     *
     */
    #[@test]
    public function interfaceMethodsMayNotBeProtected() {
      $m= new MethodNode(array(
        'name'        => 'run',
        'modifiers'   => MODIFIER_PROTECTED,
        'returns'     => TypeName::$VOID,
        'parameters'  => array(),
        'body'        => NULL
      ));
      $this->assertEquals(
        array('R401', 'Interface methods may only be public Runnable::run'), 
        $this->verify($m, new InterfaceNode(MODIFIER_PUBLIC, array(), new TypeName('Runnable')))
      );
    }

    /**
     * Test interface methods
     *
     */
    #[@test]
    public function interfaceMethodsMayNotBeAbstract() {
      $m= new MethodNode(array(
        'name'        => 'run',
        'modifiers'   => MODIFIER_ABSTRACT,
        'returns'     => TypeName::$VOID,
        'parameters'  => array(),
        'body'        => NULL
      ));
      $this->assertEquals(
        array('R401', 'Interface methods may only be public Runnable::run'), 
        $this->verify($m, new InterfaceNode(MODIFIER_PUBLIC, array(), new TypeName('Runnable')))
      );
    }

    /**
     * Test interface methods
     *
     */
    #[@test]
    public function interfaceMethodsMayNotBeFinal() {
      $m= new MethodNode(array(
        'name'        => 'run',
        'modifiers'   => MODIFIER_FINAL,
        'returns'     => TypeName::$VOID,
        'parameters'  => array(),
        'body'        => NULL
      ));
      $this->assertEquals(
        array('R401', 'Interface methods may only be public Runnable::run'), 
        $this->verify($m, new InterfaceNode(MODIFIER_PUBLIC, array(), new TypeName('Runnable')))
      );
    }

    /**
     * Test class methods
     *
     */
    #[@test]
    public function abstractMethodsMayNotHaveBodies() {
      $m= new MethodNode(array(
        'name'        => 'run',
        'modifiers'   => MODIFIER_ABSTRACT,
        'returns'     => TypeName::$VOID,
        'parameters'  => array(),
        'body'        => array()
      ));
      $this->assertEquals(
        array('R403', 'Abstract methods may not have a body Runner::run'), 
        $this->verify($m, new ClassNode(MODIFIER_PUBLIC, array(), new TypeName('Runner')))
      );
    }

    /**
     * Test class methods
     *
     */
    #[@test]
    public function nonAbstractMethodsMustHaveBodies() {
      $m= new MethodNode(array(
        'name'        => 'run',
        'modifiers'   => MODIFIER_PUBLIC,
        'returns'     => TypeName::$VOID,
        'parameters'  => array(),
        'body'        => NULL
      ));
      $this->assertEquals(
        array('R401', 'Non-abstract methods must have a body Runner::run'), 
        $this->verify($m, new ClassNode(MODIFIER_PUBLIC, array(), new TypeName('Runner')))
      );
    }

    /**
     * Test extension methods
     *
     */
    #[@test]
    public function extensionMethodsMustBeStatic() {
      $m= new MethodNode(array(
        'name'        => 'equal',
        'modifiers'   => MODIFIER_PUBLIC,
        'returns'     => TypeName::$VOID,
        'parameters'  => array(
          array('name' => 'in', 'type' => new TypeName('string')),
          array('name' => 'cmp', 'type' => new TypeName('string')),
        ),
        'extension'   => TRUE,
        'body'        => array()
      ));
      $this->assertEquals(
        array('E403', 'Extension methods must be static Runner::equal'), 
        $this->verify($m, new ClassNode(MODIFIER_PUBLIC, array(), new TypeName('Runner')))
      );
    }

    /**
     * Test extension methods
     *
     */
    #[@test]
    public function extensionMethods() {
      $m= new MethodNode(array(
        'name'        => 'equal',
        'modifiers'   => MODIFIER_PUBLIC | MODIFIER_STATIC,
        'returns'     => TypeName::$VOID,
        'parameters'  => array(
          array('name' => 'in', 'type' => new TypeName('string')),
          array('name' => 'cmp', 'type' => new TypeName('string')),
        ),
        'extension'   => TRUE,
        'body'        => array()
      ));
      $this->assertNull(
        $this->verify($m, new ClassNode(MODIFIER_PUBLIC, array(), new TypeName('Runner')))
      );
    }
  }
?>
