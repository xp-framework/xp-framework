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
    protected static $objectType;
    
    static function __static() {
      self::$objectType= new TypeReflection(XPClass::forName('lang.Object'));
    }
    
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
    
    /**
     * Returns fixture with a given parent class
     *
     * @param   xp.compiler.types.Types parent
     * @return  xp.compiler.types.Types fixture
     */
    protected function fixtureWithParent($parent) {
      $this->fixture->parent= $parent;
      return $this->fixture;
    }

    /**
     * Test hasMethod() returning parent class' method
     *
     */
    #[@test]
    public function hasParentMethod() {
      $this->assertTrue($this->fixtureWithParent(self::$objectType)->hasMethod('getClassName'));
    }

    /**
     * Test getMethod() returning parent class' method
     *
     */
    #[@test]
    public function getParentMethod() {
      $m= $this->fixtureWithParent(self::$objectType)->getMethod('getClassName');
      $this->assertInstanceOf('xp.compiler.types.Method', $m);
      $this->assertEquals($this->fixture->parent(), $m->holder);
    }

    /**
     * Test hasMethod() returning this class' method
     *
     */
    #[@test]
    public function hasOverwrittenMethod() {
      $m= new xp·compiler·types·Method('getClassName');
      $m->returns= new TypeName('string');
      $this->fixtureWithParent(self::$objectType)->addMethod($m);
      $this->assertTrue($this->fixture->hasMethod('getClassName'));
    }

    /**
     * Test getMethod() returning parent class' method
     *
     */
    #[@test]
    public function getOverwrittenMethod() {
      $m= new xp·compiler·types·Method('getClassName');
      $m->returns= new TypeName('string');
      $this->fixtureWithParent(self::$objectType)->addMethod($m);
      $m= $this->fixture->getMethod('getClassName');
      $this->assertInstanceOf('xp.compiler.types.Method', $m);
      $this->assertEquals($this->fixture, $m->holder);
    }

    /**
     * Test hasMethod() when no parent is set
     *
     */
    #[@test]
    public function noParentHasMethod() {
      $this->assertFalse($this->fixtureWithParent(NULL)->hasMethod('getClassName'));
    }

    /**
     * Test getMethod() when no parent is set
     *
     */
    #[@test]
    public function noParentParentMethod() {
      $this->assertNull($this->fixtureWithParent(NULL)->getMethod('getClassName'));
    }
  }
?>
