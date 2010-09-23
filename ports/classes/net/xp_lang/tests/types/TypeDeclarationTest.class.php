<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'xp.compiler.types.TypeDeclaration',
    'xp.compiler.ast.ClassConstantNode',
    'xp.compiler.ast.FieldNode',
    'xp.compiler.ast.MethodNode',
    'xp.compiler.ast.ConstructorNode',
    'xp.compiler.ast.IndexerNode',
    'xp.compiler.ast.PropertyNode',
    'xp.compiler.ast.StringNode',
    'xp.compiler.ast.IntegerNode'
  );

  /**
   * TestCase
   *
   * @see      xp://xp.compiler.types.TypeDeclaration
   */
  class TypeDeclarationTest extends TestCase {
  
    /**
     * Test name() method
     *
     */
    #[@test]
    public function nameWithoutPackage() {
      $decl= new TypeDeclaration(new ParseTree(NULL, array(), new ClassNode(
        MODIFIER_PUBLIC, 
        NULL,
        new TypeName('TestCase')
      )));
      $this->assertEquals('TestCase', $decl->name());
    }

    /**
     * Test name() method
     *
     */
    #[@test]
    public function nameWithPackage() {
      $decl= new TypeDeclaration(new ParseTree(new TypeName('unittest.web'), array(), new ClassNode(
        MODIFIER_PUBLIC, 
        NULL,
        new TypeName('WebTestCase')
      )));
      $this->assertEquals('unittest.web.WebTestCase', $decl->name());
    }

    /**
     * Test literal() method
     *
     */
    #[@test]
    public function literalWithoutPackage() {
      $decl= new TypeDeclaration(new ParseTree(NULL, array(), new ClassNode(
        MODIFIER_PUBLIC, 
        NULL,
        new TypeName('TestCase')
      )));
      $this->assertEquals('TestCase', $decl->literal());
    }

    /**
     * Test literal() method
     *
     */
    #[@test]
    public function literalWithPackage() {
      $decl= new TypeDeclaration(new ParseTree(new TypeName('unittest.web'), array(), new ClassNode(
        MODIFIER_PUBLIC, 
        NULL,
        new TypeName('WebTestCase')
      )));
      $this->assertEquals('WebTestCase', $decl->literal());
    }

    /**
     * Test kind() method
     *
     */
    #[@test]
    public function classKind() {
      $decl= new TypeDeclaration(new ParseTree(NULL, array(), new ClassNode(
        MODIFIER_PUBLIC, 
        NULL,
        new TypeName('TestCase')
      )));
      $this->assertEquals(Types::CLASS_KIND, $decl->kind());
    }

    /**
     * Test kind() method
     *
     */
    #[@test]
    public function interfaceKind() {
      $decl= new TypeDeclaration(new ParseTree(NULL, array(), new InterfaceNode(array(
        'name' => new TypeName('Resolveable')
      ))));
      $this->assertEquals(Types::INTERFACE_KIND, $decl->kind());
    }

    /**
     * Test kind() method
     *
     */
    #[@test]
    public function enumKind() {
      $decl= new TypeDeclaration(new ParseTree(NULL, array(), new EnumNode(array(
        'name' => new TypeName('Operation')
      ))));
      $this->assertEquals(Types::ENUM_KIND, $decl->kind());
    }
    

    /**
     * Returns a type declaration for the string class
     *
     * @return  xp.compiler.emit.TypeDeclaration
     */
    protected function stringClass() {
      return new TypeDeclaration(
        new ParseTree(new TypeName('lang.types'), array(), new ClassNode(
          MODIFIER_PUBLIC, 
          NULL,
          new TypeName('String'),
          new TypeName('lang.Object'),
          NULL,
          array(
            new ClassConstantNode('ENCODING', new TypeName('string'), new StringNode('utf-8')),
            new ConstructorNode(array(
            )),
            new MethodNode(array(
              'name'        => 'substring',
              'returns'     => new TypeName('lang.types.String'),
              'modifiers'   => MODIFIER_PUBLIC,
              'parameters'  => array(
                array(
                  'name'  => 'start',
                  'type'  => new TypeName('int'),
                  'check' => TRUE
                ), 
                array(
                  'name'  => 'end',
                  'type'  => new TypeName('int'),
                  'check' => TRUE
                )
              )
            )),
            new FieldNode(array(
              'name' => 'length'
            )),
            new IndexerNode(array(
              'type'       => new TypeName('string'),
              'parameter'  => array(
                'name'  => 'offset',
                'type'  => new TypeName('int'),
                'check' => TRUE
              )
            ))
          )
        )),
        $this->objectClass()
      );
    }

    /**
     * Returns a type declaration for the coin enum
     *
     * @return  xp.compiler.emit.TypeDeclaration
     */
    protected function coinEnum() {
      return new TypeDeclaration(
        new ParseTree(new TypeName('util.money'), array(), new ClassNode(
          MODIFIER_PUBLIC, 
          NULL,
          new TypeName('Coin'),
          new TypeName('lang.Enum'),
          NULL,
          array(
            new EnumMemberNode(array('name' => 'penny', 'value' => new IntegerNode('1'), 'body' => NULL)),
            new EnumMemberNode(array('name' => 'nickel', 'value' => new IntegerNode('2'), 'body' => NULL)),
            new EnumMemberNode(array('name' => 'dime', 'value' => new IntegerNode('10'), 'body' => NULL)),
            new EnumMemberNode(array('name' => 'quarter', 'value' => new IntegerNode('25'), 'body' => NULL)),
          )
        )),
        $this->objectClass()
      );
    }

    /**
     * Returns a type declaration for the object class
     *
     * @return  xp.compiler.emit.TypeDeclaration
     */
    protected function objectClass() {
      return new TypeDeclaration(
        new ParseTree(new TypeName('lang'), array(), new ClassNode(
          MODIFIER_PUBLIC, 
          NULL,
          new TypeName('Object'),
          NULL,
          NULL,
          array(
            new MethodNode(array(
              'name' => 'equals'
            ))
          )
        ))
      );
    }

    /**
     * Test hasConstructor() method
     *
     */
    #[@test]
    public function objectClassHasNoConstructor() {
      $decl= $this->objectClass();
      $this->assertFalse($decl->hasConstructor());
    }

    /**
     * Test getConstructor() method
     *
     */
    #[@test]
    public function objectClassNoConstructor() {
      $decl= $this->objectClass();
      $this->assertNull($decl->getConstructor());
    }

    /**
     * Test hasConstructor() method
     *
     */
    #[@test]
    public function stringClassHasConstructor() {
      $decl= $this->stringClass();
      $this->assertTrue($decl->hasConstructor());
    }

    /**
     * Test getConstructor() method
     *
     */
    #[@test]
    public function stringClassConstructor() {
      $decl= $this->stringClass();
      $this->assertInstanceOf('xp.compiler.types.Constructor', $decl->getConstructor());
    }

    /**
     * Test hasMethod() method
     *
     */
    #[@test]
    public function objectClassHasMethod() {
      $decl= $this->objectClass();
      $this->assertTrue($decl->hasMethod('equals'), 'equals');
      $this->assertFalse($decl->hasMethod('getName'), 'getName');
    }

    /**
     * Test hasMethod() method for inherited methods
     *
     */
    #[@test]
    public function stringClassHasEqualsMethod() {
      $decl= $this->stringClass();
      $this->assertTrue($decl->hasMethod('equals'));
    }

    /**
     * Test hasMethod() method for instance methods
     *
     */
    #[@test]
    public function stringClassHasSubstringMethod() {
      $decl= $this->stringClass();
      $this->assertTrue($decl->hasMethod('substring'));
    }

    /**
     * Test hasMethod() method for nonexistant methods
     *
     */
    #[@test]
    public function stringClassDoesNotHaveGetNameMethod() {
      $decl= $this->stringClass();
      $this->assertFalse($decl->hasMethod('getName'));
    }

    /**
     * Test getMethod()
     *
     */
    #[@test]
    public function stringClassSubstringMethod() {
      $method= $this->stringClass()->getMethod('substring');
      $this->assertEquals(new TypeName('lang.types.String'), $method->returns);
      $this->assertEquals('substring', $method->name);
      $this->assertEquals(array(new TypeName('int'), new TypeName('int')), $method->parameters);
      $this->assertEquals(MODIFIER_PUBLIC, $method->modifiers);
    }

    /**
     * Test hasField() method for instance fields
     *
     */
    #[@test]
    public function stringClassHasLengthField() {
      $decl= $this->stringClass();
      $this->assertTrue($decl->hasField('length'));
    }

    /**
     * Test hasField() method for nonexistant fields
     *
     */
    #[@test]
    public function stringClassDoesNotHaveCharsField() {
      $decl= $this->stringClass();
      $this->assertFalse($decl->hasField('chars'));
    }

    /**
     * Test hasIndexer() method
     *
     */
    #[@test]
    public function stringClassHasIndexer() {
      $decl= $this->stringClass();
      $this->assertTrue($decl->hasIndexer());
    }

    /**
     * Test hasIndexer() method
     *
     */
    #[@test]
    public function objectClassDoesNotHaveIndexer() {
      $decl= $this->objectClass();
      $this->assertFalse($decl->hasIndexer());
    }

    /**
     * Test getIndexer() method
     *
     */
    #[@test]
    public function stringClassIndexer() {
      $indexer= $this->stringClass()->getIndexer();
      $this->assertEquals(new TypeName('string'), $indexer->type);
      $this->assertEquals(new TypeName('int'), $indexer->parameter);
    }

    /**
     * Test isEnumerable() method
     *
     */
    #[@test]
    public function objectClassIsNotEnumerable() {
      $decl= $this->objectClass();
      $this->assertFalse($decl->isEnumerable());
    }

    /**
     * Test hasConstant() method
     *
     */
    #[@test]
    public function objectClassDoesNotHaveConstant() {
      $decl= $this->objectClass();
      $this->assertFalse($decl->hasConstant('STATUS_OK'));
    }

    /**
     * Test hasConstant() method
     *
     */
    #[@test]
    public function stringClassHasConstant() {
      $decl= $this->stringClass();
      $this->assertTrue($decl->hasConstant('ENCODING'));
    }

    /**
     * Test getConstant() method
     *
     */
    #[@test]
    public function stringClassConstant() {
      $const= $this->stringClass()->getConstant('ENCODING');
      $this->assertEquals(new TypeName('string'), $const->type);
      $this->assertEquals('utf-8', $const->value);
    }

    /**
     * Test isSubclassOf() method
     *
     */
    #[@test]
    public function stringClassSubclassOfObject() {
      $this->assertTrue($this->stringClass()->isSubclassOf($this->objectClass()));
    }

    /**
     * Test isSubclassOf() method
     *
     */
    #[@test]
    public function extendedStringClassSubclassOfObject() {
      $decl= new TypeDeclaration(
        new ParseTree(new TypeName('lang.types'), array(), new ClassNode(
          MODIFIER_PUBLIC, 
          NULL,
          new TypeName('ExtendedString'),
          new TypeName('lang.types.String'),
          NULL,
          array()
        )),
        $this->stringClass()
      );
      $this->assertTrue($decl->isSubclassOf($this->objectClass()));
    }

    /**
     * Test hasField() method
     *
     */
    #[@test]
    public function coinEnumHasMemberField() {
      $this->assertTrue($this->coinEnum()->hasField('penny'));
    }
  }
?>
