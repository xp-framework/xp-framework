<?php namespace net\xp_framework\unittest\core;

/**
 * Tests the lang.Object class
 *
 * @see  xp://lang.Object
 */
class ObjectTest extends \unittest\TestCase {

  #[@test]
  public function noConstructor() {
    $this->assertFalse(\lang\XPClass::forName('lang.Object')->hasConstructor());
  }

  #[@test]
  public function baseClass() {
    $this->assertNull(\lang\XPClass::forName('lang.Object')->getParentClass());
  }

  #[@test]
  public function genericInterface() {
    $interfaces= \lang\XPClass::forName('lang.Object')->getInterfaces();
    $this->assertEquals(1, sizeof($interfaces));
    $this->assertInstanceOf('lang.XPClass', $interfaces[0]);
    $this->assertEquals('lang.Generic', $interfaces[0]->getName());
  }

  #[@test]
  public function typeOf() {
    $this->assertEquals('lang.Object', \xp::typeOf(new \lang\Object()));
  }

  #[@test]
  public function hashCodeMethod() {
    $o= new \lang\Object();
    $this->assertTrue((bool)preg_match('/^[0-9a-z\.]+\.[0-9a-z\.]+$/', $o->hashCode()));
  }

  #[@test]
  public function objectIsEqualToSelf() {
    $o= new \lang\Object();
    $this->assertTrue($o->equals($o));
  }

  #[@test]
  public function objectIsNotEqualToOtherObject() {
    $o= new \lang\Object();
    $this->assertFalse($o->equals(new \lang\Object()));
  }

  #[@test]
  public function objectIsNotEqualToPrimitive() {
    $o= new \lang\Object();
    $this->assertFalse($o->equals(0));
  }
  
  #[@test]
  public function getClassNameMethod() {
    $o= new \lang\Object();
    $this->assertEquals('lang.Object', $o->getClassName());
  }

  #[@test]
  public function getClassMethod() {
    $o= new \lang\Object();
    $class= $o->getClass();
    $this->assertInstanceOf('lang.XPClass', $class);
    $this->assertEquals('lang.Object', $class->getName());
  }

  #[@test]
  public function toStringMethod() {
    $o= new \lang\Object();
    $this->assertEquals(
      'lang.Object {'."\n".
      '  __id => "'.$o->hashCode().'"'."\n".
      '}', 
      $o->toString()
    );
  }

  #[@test, @expect(class= 'lang.Error', withMessage= '/Call to undefined method .+::undefMethod\(\) from scope net\.xp_framework\.unittest\.core\.ObjectTest/')]
  public function callUndefinedMethod() {
    create(new \lang\Object())->undefMethod();
  }

  #[@test, @expect(class= 'lang.Error', withMessage= '/Call to undefined method .+::undefMethod\(\) from scope net\.xp_framework\.unittest\.core\.ObjectTest/')]
  public function callUndefinedMethod_call_user_func_array() {
    call_user_func_array(array(new \lang\Object(), 'undefMethod'), array());
  }
}
