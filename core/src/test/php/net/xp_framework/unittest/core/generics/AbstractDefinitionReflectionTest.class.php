<?php namespace net\xp_framework\unittest\core\generics;

use lang\types\String;
use lang\XPClass;

/**
 * TestCase for definition reflection
 *
 * @see   xp://net.xp_framework.unittest.core.generics.Lookup
 */
abstract class AbstractDefinitionReflectionTest extends \unittest\TestCase {
  protected $fixture= null;

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
   */  
  public function setUp() {
    $this->fixture= $this->fixtureClass();
  }

  #[@test]
  public function isAGenericDefinition() {
    $this->assertTrue($this->fixture->isGenericDefinition());
  }

  #[@test]
  public function isNotAGeneric() {
    $this->assertFalse($this->fixture->isGeneric());
  }

  #[@test]
  public function components() {
    $this->assertEquals(array('K', 'V'), $this->fixture->genericComponents());
  }

  #[@test]
  public function newGenericTypeIsGeneric() {
    $t= $this->fixture->newGenericType(array(
      XPClass::forName('lang.types.String'), 
      XPClass::forName('unittest.TestCase')
    ));
    $this->assertTrue($t->isGeneric());
  }

  #[@test]
  public function newLookupWithStringAndTestCase() {
    $arguments= array(
      XPClass::forName('lang.types.String'), 
      XPClass::forName('unittest.TestCase')
    );
    $this->assertEquals(
      $arguments, 
      $this->fixture->newGenericType($arguments)->genericArguments()
    );
  }

  #[@test]
  public function newLookupWithStringAndObject() {
    $arguments= array(
      XPClass::forName('lang.types.String'), 
      XPClass::forName('lang.Object')
    );
    $this->assertEquals(
      $arguments, 
      $this->fixture->newGenericType($arguments)->genericArguments()
    );
  }

  #[@test]
  public function newLookupWithPrimitiveStringAndObject() {
    $arguments= array(
      \lang\Primitive::$STRING,
      XPClass::forName('lang.Object')
    );
    $this->assertEquals(
      $arguments, 
      $this->fixture->newGenericType($arguments)->genericArguments()
    );
  }

  #[@test]
  public function classesFromReflectionAndCreateAreEqual() {
    $this->assertEquals(
      $this->fixtureInstance()->getClass(),
      $this->fixture->newGenericType(array(
        XPClass::forName('lang.types.String'), 
        XPClass::forName('unittest.TestCase')
      ))
    );
  }

  #[@test]
  public function classesCreatedWithDifferentTypesAreNotEqual() {
    $this->assertNotEquals(
      $this->fixture->newGenericType(array(
        XPClass::forName('lang.types.String'), 
        XPClass::forName('lang.Object')
      )),
      $this->fixture->newGenericType(array(
        XPClass::forName('lang.types.String'), 
        XPClass::forName('unittest.TestCase')
      ))
    );
  }

  #[@test, @expect('lang.IllegalArgumentException')]
  public function missingArguments() {
    $this->fixture->newGenericType(array());
  }

  #[@test, @expect('lang.IllegalArgumentException')]
  public function missingArgument() {
    $this->fixture->newGenericType(array($this->getClass()));
  }

  #[@test, @expect('lang.IllegalArgumentException')]
  public function tooManyArguments() {
    $c= $this->getClass();
    $this->fixture->newGenericType(array($c, $c, $c));
  }

  #[@test]
  public function abstractMethod() {
    $abstractMethod= XPClass::forName('net.xp_framework.unittest.core.generics.ArrayFilter')
      ->newGenericType(array($this->fixture))
      ->getMethod('accept')
    ;
    $this->assertEquals(
      $this->fixture,
      $abstractMethod->getParameter(0)->getType()
    );
  }
}
