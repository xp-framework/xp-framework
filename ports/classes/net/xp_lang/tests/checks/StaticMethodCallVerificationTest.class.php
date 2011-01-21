<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'xp.compiler.checks.StaticMethodCallVerification',
    'xp.compiler.ast.StaticMethodCallNode',
    'xp.compiler.ast.VariableNode',
    'xp.compiler.ast.InstanceCreationNode',
    'xp.compiler.ast.CastNode',
    'xp.compiler.types.TypeDeclarationScope'
  );

  /**
   * TestCase
   *
   * @see      xp://xp.compiler.checks.StaticMethodCallVerification
   */
  class StaticMethodCallVerificationTest extends TestCase {
    protected $fixture= NULL;
  
    /**
     * Sets up test case
     *
     */
    public function setUp() {
      $this->fixture= new StaticMethodCallVerification();
    }
    
    /**
     * Wrapper around verify
     *
     * @param   xp.compiler.ast.StaticMethodCallNode call
     * @param   xp.compiler.types.TypeName parent
     * @return  var
     */
    protected function verify(StaticMethodCallNode $call, $parent= NULL) {
      $scope= new TypeDeclarationScope();
      $scope->declarations[0]= new ClassNode(
        MODIFIER_PUBLIC, 
        NULL,
        new TypeName('Fixture'),
        $parent ? $parent : new TypeName('lang.Object'),
        NULL,
        array(
          new MethodNode(array(
            'name'      => 'forName',
            'modifiers' => MODIFIER_STATIC | MODIFIER_PUBLIC
          )),
          new MethodNode(array(
            'name'      => 'getInstance',
            'modifiers' => MODIFIER_STATIC | MODIFIER_PROTECTED
          )),
          new MethodNode(array(
            'name'      => 'asIntern',
            'modifiers' => MODIFIER_STATIC | MODIFIER_PRIVATE
          )),
        )
      );
      $scope->addResolved('parent', $ptr= $scope->resolveType($scope->declarations[0]->parent));
      $scope->addResolved('self', new TypeDeclaration(new ParseTree(NULL, array(), $scope->declarations[0]), $ptr));
      return $this->fixture->verify($call, $scope);
    }
    
    /**
     * Helper method
     *
     * @param   string type
     * @return  xp.compiler.ast.InstanceCreationNode
     */
    protected function newInstance($type) {
      return new InstanceCreationNode(array('type' => new TypeName($type)));
    }

    /**
     * Test method call to a public method on this class
     *
     */
    #[@test]
    public function nonExistantMethodCall() {
      $this->assertEquals(
        array('T404', 'No such method nonExistant() in Fixture'),
        $this->verify(new StaticMethodCallNode(new TypeName('self'), 'nonExistant'))
      );
    }
    
    /**
     * Test method call to a public method on this class
     *
     */
    #[@test]
    public function thisPublicMethodCall() {
      $this->assertNull(
        $this->verify(new StaticMethodCallNode(new TypeName('self'), 'forName'))
      );
    }

    /**
     * Test method call to a protected method on this class
     *
     */
    #[@test]
    public function thisProtectedMethodCall() {
      $this->assertNull(
        $this->verify(new StaticMethodCallNode(new TypeName('self'), 'getInstance'))
      );
    }

    /**
     * Test method call to a private method on this class
     *
     */
    #[@test]
    public function thisPrivateMethodCall() {
      $this->assertNull(
        $this->verify(new StaticMethodCallNode(new TypeName('self'), 'asIntern'))
      );
    }

    /**
     * Test method call to public static XPClass::forName()
     *
     */
    #[@test]
    public function classPublicMethodCall() {
      $this->assertNull(
        $this->verify(new StaticMethodCallNode(new TypeName('lang.XPClass'), 'forName'))
      );
    }

    /**
     * Test method call to protected static Enum::membersOf()
     *
     */
    #[@test]
    public function enumProtectedMethodCall() {
      $this->assertEquals(
        array('T403', 'Invoking protected static lang.Enum::membersOf() from Fixture'),
        $this->verify(new StaticMethodCallNode(new TypeName('lang.Enum'), 'membersOf'))
      );
    }

    /**
     * Test method call to a protected method on the string sclass
     *
     */
    #[@test]
    public function enumProtectedMethodCallIfSubclass() {
      $this->assertNull(
        $this->verify(new StaticMethodCallNode(new TypeName('lang.Enum'), 'membersOf'), new TypeName('lang.Enum'))
      );
    }
  }
?>
