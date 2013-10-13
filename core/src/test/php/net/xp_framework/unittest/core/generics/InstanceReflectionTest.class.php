<?php namespace net\xp_framework\unittest\core\generics;

use lang\types\String;

/**
 * TestCase for instance reflection
 *
 * @see   xp://net.xp_framework.unittest.core.generics.Lookup
 */
class InstanceReflectionTest extends \unittest\TestCase {
  
  /**
   * Creates fixture, a Lookup with String and TestCase as component
   * types.
   *
   */  
  public function setUp() {
    $this->fixture= create('new net.xp_framework.unittest.core.generics.Lookup<String, TestCase>()');
  }

  #[@test]
  public function getClassNameMethod() {
    $this->assertEquals(
      'net.xp_framework.unittest.core.generics.Lookup<lang.types.String,unittest.TestCase>', 
      $this->fixture->getClassName()
    );
  }

  #[@test]
  public function nameOfClass() {
    $this->assertEquals(
      'net.xp_framework.unittest.core.generics.Lookup<lang.types.String,unittest.TestCase>', 
      $this->fixture->getClass()->getName()
    );
  }

  #[@test]
  public function simpleNameOfClass() {
    $this->assertEquals(
      'Lookup<lang.types.String,unittest.TestCase>', 
      $this->fixture->getClass()->getSimpleName()
    );
  }

  #[@test]
  public function reflectedNameOfClass() {
    $class= $this->fixture->getClass();
    $this->assertEquals(
      'net\xp_framework\unittest\core\generics\Lookup··String¸TestCase', 
      \xp::reflect($class->getName())
    );
  }

  #[@test]
  public function instanceIsGeneric() {
    $this->assertTrue($this->fixture->getClass()->isGeneric());
  }

  #[@test]
  public function instanceIsNoGenericDefinition() {
    $this->assertFalse($this->fixture->getClass()->isGenericDefinition());
  }

  #[@test]
  public function genericDefinition() {
    $this->assertEquals(
      \lang\XPClass::forName('net.xp_framework.unittest.core.generics.Lookup'),
      $this->fixture->getClass()->genericDefinition()
    );
  }

  #[@test]
  public function genericArguments() {
    $this->assertEquals(
      array(\lang\XPClass::forName('lang.types.String'), \lang\XPClass::forName('unittest.TestCase')),
      $this->fixture->getClass()->genericArguments()
    );
  }

  #[@test, @ignore('No longer existant in new implementation')]
  public function delegateFieldType() {
    $this->assertEquals(
      'net.xp_framework.unittest.core.generics.Lookup',
      $this->fixture->getClass()->getField('delegate')->getType()
    );
  }

  #[@test]
  public function putParameters() {
    $params= $this->fixture->getClass()->getMethod('put')->getParameters();
    $this->assertEquals(2, sizeof($params));
    $this->assertEquals(\lang\XPClass::forName('lang.types.String'), $params[0]->getType());
    $this->assertEquals(\lang\XPClass::forName('unittest.TestCase'), $params[1]->getType());
  }

  #[@test]
  public function getReturnType() {
    $this->assertEquals(
      'unittest.TestCase',
      $this->fixture->getClass()->getMethod('get')->getReturnTypeName()
    );
  }

  #[@test]
  public function valuesReturnType() {
    $this->assertEquals(
      'unittest.TestCase[]',
      $this->fixture->getClass()->getMethod('values')->getReturnTypeName()
    );
  }
}
