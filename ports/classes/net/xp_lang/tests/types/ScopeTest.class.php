<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'xp.compiler.ast.VariableNode',
    'xp.compiler.emit.source.Emitter',
    'xp.compiler.types.TypeReflection',
    'xp.compiler.types.ArrayTypeOf',
    'xp.compiler.types.TaskScope',
    'xp.compiler.diagnostic.NullDiagnosticListener',
    'xp.compiler.io.FileManager',
    'xp.compiler.task.CompilationTask'
  );

  /**
   * TestCase
   *
   * @see      xp://xp.compiler.types.Scope
   */
  class ScopeTest extends TestCase {
    protected $fixture= NULL;
    
    /**
     * Sets up this testcase
     *
     */
    public function setUp() {
      $this->fixture= new TaskScope(new CompilationTask(
        new FileSource(new File(__FILE__), Syntax::forName('xp')),
        new NullDiagnosticListener(),
        new FileManager(),
        new xp·compiler·emit·source·Emitter()
      ));
    }
    
    /**
     * Test typeOf() method
     *
     */
    #[@test]
    public function arrayType() {
      $this->assertEquals(new TypeName('var[]'), $this->fixture->typeOf(new ArrayNode()));
    }

    /**
     * Test typeOf() method
     *
     */
    #[@test]
    public function typedArrayType() {
      $this->assertEquals(new TypeName('string[]'), $this->fixture->typeOf(new ArrayNode(array(
        'values'        => NULL,
        'type'          => new TypeName('string[]'),
      ))));
    }
    
    /**
     * Test typeOf() method
     *
     */
    #[@test]
    public function mapType() {
      $this->assertEquals(new TypeName('[:var]'), $this->fixture->typeOf(new MapNode()));
    }

    /**
     * Test typeOf() method
     *
     */
    #[@test]
    public function typedMapType() {
      $this->assertEquals(new TypeName('[:string]'), $this->fixture->typeOf(new MapNode(array(
        'elements'      => NULL,
        'type'          => new TypeName('[:string]'),
      ))));
    }
    
    /**
     * Test typeOf() method
     *
     */
    #[@test]
    public function stringType() {
      $this->assertEquals(new TypeName('string'), $this->fixture->typeOf(new StringNode('')));
    }
    
    /**
     * Test typeOf() method
     *
     */
    #[@test]
    public function intType() {
      $this->assertEquals(new TypeName('int'), $this->fixture->typeOf(new IntegerNode('')));
    }
    
    /**
     * Test typeOf() method
     *
     */
    #[@test]
    public function hexType() {
      $this->assertEquals(new TypeName('int'), $this->fixture->typeOf(new HexNode('')));
    }
    
    /**
     * Test typeOf() method
     *
     */
    #[@test]
    public function decimalType() {
      $this->assertEquals(new TypeName('double'), $this->fixture->typeOf(new DecimalNode('')));
    }
    
    /**
     * Test typeOf() method
     *
     */
    #[@test]
    public function nullType() {
      $this->assertEquals(new TypeName('lang.Object'), $this->fixture->typeOf(new NullNode()));
    }
    
    /**
     * Test typeOf() method
     *
     */
    #[@test]
    public function boolType() {
      $this->assertEquals(new TypeName('bool'), $this->fixture->typeOf(new BooleanNode(TRUE)));
    }
    
    /**
     * Test typeOf() method
     *
     */
    #[@test]
    public function typeOfAComparison() {
      $this->assertEquals(new TypeName('bool'), $this->fixture->typeOf(new ComparisonNode()));
    }

    /**
     * Test typeOf() method
     *
     */
    #[@test]
    public function typeOfBracedExpressionNode() {
      $this->assertEquals(new TypeName('bool'), $this->fixture->typeOf(new BracedExpressionNode(new BooleanNode(TRUE))));
      $this->assertEquals(new TypeName('string'), $this->fixture->typeOf(new BracedExpressionNode(new StringNode('Hello'))));
    }

    /**
     * Test setType() and typeOf() methods
     *
     */
    #[@test]
    public function registeredType() {
      with ($v= new VariableNode('h'), $t= new TypeName('util.collections.HashTable')); {
        $this->fixture->setType($v, $t);
        $this->assertEquals($t, $this->fixture->typeOf($v));
      }
    }

    /**
     * Test typeOf() method
     *
     */
    #[@test]
    public function unknownType() {
      $this->assertEquals(TypeName::$VAR, $this->fixture->typeOf(new VariableNode('v')));
    }

    /**
     * Test extension method API
     *
     */
    #[@test]
    public function objectExtension() {
      with (
        $objectType= new TypeReflection(XPClass::forName('lang.Object')), 
        $classNameMethod= new xp·compiler·types·Method('getClassName')
      ); {
        $this->fixture->addExtension($objectType, $classNameMethod);
        $this->assertTrue($this->fixture->hasExtension($objectType, $classNameMethod->name));
        $this->assertEquals(
          $classNameMethod,
          $this->fixture->getExtension($objectType, $classNameMethod->name)
        );
      }
    }

    /**
     * Test extension method API
     *
     */
    #[@test]
    public function arrayExtension() {
      with (
        $objectsType= new ArrayTypeOf(new TypeReflection(XPClass::forName('lang.Object'))), 
        $sortedMethod= new xp·compiler·types·Method('sorted')
      ); {
        $this->fixture->addExtension($objectsType, $sortedMethod);
        $this->assertTrue($this->fixture->hasExtension($objectsType, $sortedMethod->name));
        $this->assertEquals(
          $sortedMethod,
          $this->fixture->getExtension($objectsType, $sortedMethod->name)
        );
      }
    }

    /**
     * Test extension method API
     *
     */
    #[@test]
    public function mapExtension() {
      with (
        $mapType= new MapTypeOf(new TypeReference(new TypeName('string'), Types::PRIMITIVE_KIND), new TypeReflection(XPClass::forName('lang.Object'))), 
        $keyMethod= new xp·compiler·types·Method('key')
      ); {
        $this->fixture->addExtension($mapType, $keyMethod);
        $this->assertTrue($this->fixture->hasExtension($mapType, $keyMethod->name));
        $this->assertEquals(
          $keyMethod,
          $this->fixture->getExtension($mapType, $keyMethod->name)
        );
      }
    }

    /**
     * Test extension method API
     *
     */
    #[@test]
    public function objectExtensionInherited() {
      with (
        $objectType= new TypeReflection(XPClass::forName('lang.Object')), 
        $dateType= new TypeReflection(XPClass::forName('util.Date')),
        $classNameMethod= new xp·compiler·types·Method('getClassName')
      ); {
        $this->fixture->addExtension($objectType, $classNameMethod);
        $this->assertTrue($this->fixture->hasExtension($dateType, $classNameMethod->name));
        $this->assertEquals(
          $classNameMethod,
          $this->fixture->getExtension($dateType, $classNameMethod->name)
        );
      }
    }

    /**
     * Test addTypeImport()
     *
     */
    #[@test, @expect('xp.compiler.types.ResolveException')]
    public function importNonExistantType() {
      $this->fixture->addTypeImport('util.cmd.@@NON_EXISTANT@@');
    }

    /**
     * Test addPackageImport()
     *
     */
    #[@test, @expect('xp.compiler.types.ResolveException')]
    public function importNonExistantPackage() {
      $this->fixture->addPackageImport('util.cmd.@@NON_EXISTANT@@');
    }

    /**
     * Test resolve()
     *
     */
    #[@test]
    public function resolveFullyQualified() {
      $this->assertEquals(
        new TypeReflection(XPClass::forName('util.cmd.Command')), 
        $this->fixture->resolveType(new TypeName('util.cmd.Command'))
      );
    }

    /**
     * Test resolve()
     *
     */
    #[@test]
    public function resolveUnqualified() {
      $this->fixture->addTypeImport('util.cmd.Command');
      $this->assertEquals(
        new TypeReflection(XPClass::forName('util.cmd.Command')), 
        $this->fixture->resolveType(new TypeName('Command'))
      );
    }

    /**
     * Test resolve()
     *
     */
    #[@test]
    public function resolveUnqualifiedByPackageImport() {
      $this->fixture->addPackageImport('util.cmd');
      $this->assertEquals(
        new TypeReflection(XPClass::forName('util.cmd.Command')), 
        $this->fixture->resolveType(new TypeName('Command'))
      );
    }

    /**
     * Test resolve()
     *
     */
    #[@test]
    public function resolveArrayType() {
      $this->assertEquals(
        new TypeReference(new TypeName('util.cmd.Command[]'), Types::CLASS_KIND), 
        $this->fixture->resolveType(new TypeName('util.cmd.Command[]'))
      );
    }

    /**
     * Test resolve()
     *
     */
    #[@test]
    public function resolveUnqualifiedArrayType() {
      $this->fixture->addPackageImport('util.cmd');
      $this->assertEquals(
        new TypeReference(new TypeName('util.cmd.Command[]'), Types::CLASS_KIND), 
        $this->fixture->resolveType(new TypeName('Command[]'))
      );
    }

    /**
     * Test resolve()
     *
     */
    #[@test]
    public function resolveStringType() {
      $this->assertEquals(
        new TypeReference(new TypeName('string'), Types::PRIMITIVE_KIND), 
        $this->fixture->resolveType(new TypeName('string'))
      );
    }

    /**
     * Test resolve()
     *
     */
    #[@test]
    public function resolveStringArrayType() {
      $this->assertEquals(
        new TypeReference(new TypeName('string[]'), Types::PRIMITIVE_KIND), 
        $this->fixture->resolveType(new TypeName('string[]'))
      );
    }

    /**
     * Test resolving a generic type
     *
     */
    #[@test]
    public function resolveGenericType() {
      $components= array(new TypeName('string'), new TypeName('lang.Object'));
      $this->assertEquals(
        new GenericType(new TypeReflection(XPClass::forName('util.collections.HashTable')), $components),
        $this->fixture->resolveType(new TypeName('util.collections.HashTable', $components))
      );
    }

    /**
     * Test used list
     *
     */
    #[@test]
    public function usedAfterPackageImport() {
      $this->fixture->addPackageImport('util.cmd');
      
      $this->assertEquals(array(), $this->fixture->used);
    }

    /**
     * Test used list
     *
     */
    #[@test]
    public function usedAfterPackageAndTypeImport() {
      $this->fixture->addPackageImport('util.cmd');
      $this->fixture->resolveType(new TypeName('Command'));
      
      $this->assertEquals(array(new TypeName('util.cmd.Command')), $this->fixture->used);
    }

    /**
     * Test used list
     *
     */
    #[@test]
    public function usedAfterPackageAndMultipleTypeImport() {
      $this->fixture->addPackageImport('util.cmd');
      $this->fixture->resolveType(new TypeName('Command'));
      $this->fixture->resolveType(new TypeName('Command'));
      
      $this->assertEquals(array(new TypeName('util.cmd.Command')), $this->fixture->used);
    }

    /**
     * Test used list
     *
     */
    #[@test]
    public function usedAfterTypeImport() {
      $this->fixture->addTypeImport('util.cmd.Command');
      
      $this->assertEquals(array(new TypeName('util.cmd.Command')), $this->fixture->used);
    }

    /**
     * Test used list
     *
     */
    #[@test]
    public function usedAfterMultipleTypeImport() {
      $this->fixture->addTypeImport('util.cmd.Command');
      $this->fixture->addTypeImport('util.cmd.Command');
      
      $this->assertEquals(array(new TypeName('util.cmd.Command')), $this->fixture->used);
    }
  }
?>
