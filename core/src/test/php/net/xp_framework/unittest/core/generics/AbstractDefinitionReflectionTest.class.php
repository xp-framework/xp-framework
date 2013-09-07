<?php namespace net\xp_framework\unittest\core\generics;

use unittest\TestCase;
use lang\types\String;


/**
 * TestCase for definition reflection
 *
 * @see   xp://net.xp_framework.unittest.core.generics.Lookup
 */
abstract class AbstractDefinitionReflectionTest extends TestCase {
  protected $fixture= NULL;

  /**
   * Creates fixture, a Lookup class
   *
   * @return  lang.XPClass
   */  
  protected abstract function fixtureClass();

  /**
   * Creates fixture instance
   *
   * @return  var
   */
  protected abstract function fixtureInstance();

  /**
   * Creates fixture, a Lookup class
   *
   */  
  public function setUp() {
    $this->fixture= $this->fixtureClass();
  }

  /**
   * Test isGenericDefinition()
   *
   */
  #[@test]
    public function isAGenericDefinition() {
    $this->assertTrue($this->fixture->isGenericDefinition());
  }

  /**
   * Test isGenericDefinition()
   *
   */
  #[@test]
    public function isNotAGeneric() {
    $this->assertFalse($this->fixture->isGeneric());
  }

  /**
   * Test genericComponents()
   *
   */
  #[@test]
    public function components() {
    $this->assertEquals(array('K', 'V'), $this->fixture->genericComponents());
  }

  /**
   * Test newGenericType()
   *
   */
  #[@test]
    public function newGenericTypeIsGeneric() {
    $t= $this->fixture->newGenericType(array(
      \lang\XPClass::forName('lang.types.String'), 
      \lang\XPClass::forName('unittest.TestCase')
    ));
    $this->assertTrue($t->isGeneric());
  }

  /**
   * Test newGenericType()
   *
   */
  #[@test]
    public function newLookupWithStringAndTestCase() {
    $arguments= array(
      \lang\XPClass::forName('lang.types.String'), 
      \lang\XPClass::forName('unittest.TestCase')
    );
    $this->assertEquals(
      $arguments, 
      $this->fixture->newGenericType($arguments)->genericArguments()
    );
  }

  /**
   * Test newGenericType()
   *
   */
  #[@test]
    public function newLookupWithStringAndObject() {
    $arguments= array(
      \lang\XPClass::forName('lang.types.String'), 
      \lang\XPClass::forName('lang.Object')
    );
    $this->assertEquals(
      $arguments, 
      $this->fixture->newGenericType($arguments)->genericArguments()
    );
  }

  /**
   * Test newGenericType()
   *
   */
  #[@test]
    public function newLookupWithPrimitiveStringAndObject() {
    $arguments= array(
      \lang\Primitive::$STRING,
      \lang\XPClass::forName('lang.Object')
    );
    $this->assertEquals(
      $arguments, 
      $this->fixture->newGenericType($arguments)->genericArguments()
    );
  }

  /**
   * Test classes created via newGenericType() and from an instance
   * instantiated via create() are equal.
   *
   */
  #[@test]
    public function classesFromReflectionAndCreateAreEqual() {
    $this->assertEquals(
      $this->fixtureInstance()->getClass(),
      $this->fixture->newGenericType(array(
        \lang\XPClass::forName('lang.types.String'), 
        \lang\XPClass::forName('unittest.TestCase')
      ))
    );
  }

  /**
   * Test newGenericType()
   *
   */
  #[@test]
    public function classesCreatedWithDifferentTypesAreNotEqual() {
    $this->assertNotEquals(
      $this->fixture->newGenericType(array(
        \lang\XPClass::forName('lang.types.String'), 
        \lang\XPClass::forName('lang.Object')
      )),
      $this->fixture->newGenericType(array(
        \lang\XPClass::forName('lang.types.String'), 
        \lang\XPClass::forName('unittest.TestCase')
      ))
    );
  }

  /**
   * Test newGenericType()
   *
   */
  #[@test, @expect('lang.IllegalArgumentException')]
    public function missingArguments() {
    $this->fixture->newGenericType(array());
  }

  /**
   * Test newGenericType()
   *
   */
  #[@test, @expect('lang.IllegalArgumentException')]
    public function missingArgument() {
    $this->fixture->newGenericType(array($this->getClass()));
  }

  /**
   * Test newGenericType()
   *
   */
  #[@test, @expect('lang.IllegalArgumentException')]
    public function tooManyArguments() {
    $c= $this->getClass();
    $this->fixture->newGenericType(array($c, $c, $c));
  }


  /**
   * Test newGenericType()
   *
   */
  #[@test]
    public function abstractMethod() {
    $abstractMethod= \lang\XPClass::forName('net.xp_framework.unittest.core.generics.ArrayFilter')
      ->newGenericType(array($this->fixture))
      ->getMethod('accept')
    ;
    $this->assertEquals(
      $this->fixture,
      $abstractMethod->getParameter(0)->getType()
    );
  }
}
