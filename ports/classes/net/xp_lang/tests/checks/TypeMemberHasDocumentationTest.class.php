<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'xp.compiler.checks.TypeMemberHasDocumentation',
    'xp.compiler.ast.MethodNode',
    'xp.compiler.ast.ClassNode',
    'xp.compiler.types.TypeDeclarationScope'
  );

  /**
   * TestCase
   *
   * @see      xp://xp.compiler.checks.TypeMemberHasDocumentation
   */
  class TypeMemberHasDocumentationTest extends TestCase {
    protected $fixture= NULL;
  
    /**
     * Sets up test case
     *
     */
    public function setUp() {
      $this->fixture= new TypeMemberHasDocumentation();
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
     * Test
     *
     */
    #[@test]
    public function methodWithoutApidoc() {
      $m= new MethodNode(array(
        'name'        => 'run',
        'modifiers'   => MODIFIER_ABSTRACT,
        'returns'     => TypeName::$VOID,
        'parameters'  => array(),
        'body'        => array()
      ));
      $this->assertEquals(
        array('D201', 'No api doc for member Runner::run'), 
        $this->verify($m, new ClassNode(MODIFIER_PUBLIC, array(), new TypeName('Runner')))
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function methodsInSyntheticClassesNotChecked() {
      $c= new ClassNode(MODIFIER_PUBLIC, array(), new TypeName('Lambda··4b70075bd9164'));
      $c->synthetic= TRUE;
      $m= new MethodNode(array(
        'name'        => 'run',
        'modifiers'   => MODIFIER_ABSTRACT,
        'returns'     => TypeName::$VOID,
        'parameters'  => array(),
        'body'        => array()
      ));
      $this->assertNull($this->verify($m, $c));
    }
  }
?>
