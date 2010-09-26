<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'xp.compiler.types.CompiledType',
    'xp.compiler.types.TypeReflection'
  );

  /**
   * TestCase
   *
   * @see      xp://xp.compiler.types.CompiledType
   */
  class CompiledTypeTest extends TestCase {
    protected $fixture= NULL;
    
    /**
     * Set up test case - creates fixture
     *
     */
    public function setUp() {
      $this->fixture= new CompiledType(ucfirst($this->name));
    }
  
    /**
     * Test adding a method
     *
     */
    #[@test]
    public function method() {
      $m= new xp·compiler·types·Method('hello');
      $m->returns= new TypeName('string');
      $this->fixture->addMethod($m);
      $this->assertEquals($m, $this->fixture->getMethod($m->name));
    }

    /**
     * Test adding a field
     *
     */
    #[@test]
    public function field() {
      $f= new xp·compiler·types·Field('name');
      $f->type= new TypeName('string');
      $this->fixture->addField($f);
      $this->assertEquals($f, $this->fixture->getField($f->name));
    }

    /**
     * Test adding a constant
     *
     */
    #[@test]
    public function constant() {
      $f= new xp·compiler·types·Constant('name');
      $f->type= new TypeName('string');
      $this->fixture->addConstant($f);
      $this->assertEquals($f, $this->fixture->getConstant($f->name));
    }

    /**
     * Test adding a property
     *
     */
    #[@test]
    public function property() {
      $f= new xp·compiler·types·Property('name');
      $f->type= new TypeName('string');
      $this->fixture->addProperty($f);
      $this->assertEquals($f, $this->fixture->getProperty($f->name));
    }

    /**
     * Test adding a operator
     *
     */
    #[@test]
    public function operator() {
      $o= new xp·compiler·types·Operator('+');
      $o->returns= new TypeName('string');
      $this->fixture->addOperator($o);
      $this->assertEquals($o, $this->fixture->getOperator($o->symbol));
    }

    /**
     * Test isSubclassOf()
     *
     */
    #[@test]
    public function isNotSubclassOfSelf() {
      $this->fixture->parent= new TypeReflection(XPClass::forName('unittest.TestCase'));
      $this->assertFalse($this->fixture->isSubclassOf($this->fixture));
    }

    /**
     * Test isSubclassOf()
     *
     */
    #[@test]
    public function isSubclassOfParent() {
      $this->fixture->parent= new TypeReflection(XPClass::forName('unittest.TestCase'));
      $this->assertTrue($this->fixture->isSubclassOf($this->fixture->parent));
    }

    /**
     * Test isSubclassOf()
     *
     */
    #[@test]
    public function isSubclassOfParentsParent() {
      $this->fixture->parent= new TypeReflection(XPClass::forName('unittest.TestCase'));
      $this->assertTrue($this->fixture->isSubclassOf(new TypeReflection(XPClass::forName('lang.Object'))));
    }
  }
?>
