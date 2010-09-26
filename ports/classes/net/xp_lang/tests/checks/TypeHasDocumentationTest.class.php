<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'xp.compiler.checks.TypeHasDocumentation',
    'xp.compiler.ast.InterfaceNode',
    'xp.compiler.ast.EnumNode',
    'xp.compiler.ast.ClassNode',
    'xp.compiler.types.CompilationUnitScope'
  );

  /**
   * TestCase
   *
   * @see      xp://xp.compiler.checks.TypeHasDocumentation
   */
  class TypeHasDocumentationTest extends TestCase {
    protected $fixture= NULL;
    protected $scope= NULL;
  
    /**
     * Sets up test case
     *
     */
    public function setUp() {
      $this->fixture= new TypeHasDocumentation();
      $this->scope= new CompilationUnitScope();
    }
    
    /**
     * Test interfaces
     *
     */
    #[@test]
    public function interfaceWithoutApidoc() {
      $this->assertEquals(
        array('D201', 'No api doc for type Runnable'), 
        $this->fixture->verify(new InterfaceNode(MODIFIER_PUBLIC, array(), new TypeName('Runnable')), $this->scope)
      );
    }

    /**
     * Test interfaces
     *
     */
    #[@test]
    public function interfaceWithApidoc() {
      $i= new InterfaceNode(MODIFIER_PUBLIC, array(), new TypeName('Runnable'));
      $i->comment= 'Comment';
      $this->assertNull($this->fixture->verify($i, $this->scope));
    }

    /**
     * Test classes
     *
     */
    #[@test]
    public function syntheticInterface() {
      $i= new InterfaceNode(MODIFIER_PUBLIC, array(), new TypeName('Runnable'));
      $i->synthetic= TRUE;
      $this->assertNull($this->fixture->verify($i, $this->scope));
    }

    /**
     * Test classes
     *
     */
    #[@test]
    public function classWithoutApidoc() {
      $this->assertEquals(
        array('D201', 'No api doc for type Runner'), 
        $this->fixture->verify(new ClassNode(MODIFIER_PUBLIC, array(), new TypeName('Runner')), $this->scope)
      );
    }

    /**
     * Test classes
     *
     */
    #[@test]
    public function classWithApidoc() {
      $c= new ClassNode(MODIFIER_PUBLIC, array(), new TypeName('Runner'));
      $c->comment= 'Comment';
      $this->assertNull($this->fixture->verify($c, $this->scope));
    }

    /**
     * Test classes
     *
     */
    #[@test]
    public function syntheticClass() {
      $c= new ClassNode(MODIFIER_PUBLIC, array(), new TypeName('Runner'));
      $c->synthetic= TRUE;
      $this->assertNull($this->fixture->verify($c, $this->scope));
    }

    /**
     * Test enums
     *
     */
    #[@test]
    public function enumWithoutApidoc() {
      $this->assertEquals(
        array('D201', 'No api doc for type Runners'), 
        $this->fixture->verify(new EnumNode(MODIFIER_PUBLIC, array(), new TypeName('Runners')), $this->scope)
      );
    }

    /**
     * Test classes
     *
     */
    #[@test]
    public function enumWithApidoc() {
      $e= new EnumNode(MODIFIER_PUBLIC, array(), new TypeName('Runners'));
      $e->comment= 'Comment';
      $this->assertNull($this->fixture->verify($e, $this->scope));
    }

    /**
     * Test classes
     *
     */
    #[@test]
    public function syntheticEnums() {
      $e= new EnumNode(MODIFIER_PUBLIC, array(), new TypeName('Runners'));
      $e->synthetic= TRUE;
      $this->assertNull($this->fixture->verify($e, $this->scope));
    }
  }
?>
