<?php namespace net\xp_framework\unittest\core\generics;

use unittest\TestCase;
use lang\types\String;

/**
 * TestCase for generic behaviour at runtime.
 *
 * @see   xp://net.xp_framework.unittest.core.generics.Lookup
 */
class PrimitivesTest extends TestCase {

  #[@test]
  public function primitiveStringKey() {
    $l= create('new net.xp_framework.unittest.core.generics.Lookup<string, TestCase>', array(
      'this' => $this
    ));
    $this->assertEquals($this, $l->get('this'));
  }

  #[@test]
  public function primitiveStringValue() {
    $l= create('new net.xp_framework.unittest.core.generics.Lookup<TestCase, string>()');
    $l->put($this, 'this');
    $this->assertEquals('this', $l->get($this));
  }

  #[@test, @expect('lang.IllegalArgumentException')]
  public function primitiveVerification() {
    $l= create('new net.xp_framework.unittest.core.generics.Lookup<string, TestCase>()');
    $l->put(1, $this);
  }

  #[@test, @expect('lang.IllegalArgumentException')]
  public function instanceVerification() {
    $l= create('new net.xp_framework.unittest.core.generics.Lookup<string, TestCase>()');
    $l->put(new String('Hello'), $this);
  }

  #[@test]
  public function nameOfClass() {
    $type= \lang\XPClass::forName('net.xp_framework.unittest.core.generics.Lookup')->newGenericType(array(
      \lang\Primitive::$STRING,
      \lang\XPClass::forName('unittest.TestCase')
    ));
    $this->assertEquals('net.xp_framework.unittest.core.generics.Lookup<string,unittest.TestCase>', $type->getName());
  }

  #[@test]
  public function typeArguments() {
    $this->assertEquals(
      array(\lang\Primitive::$STRING, \lang\XPClass::forName('unittest.TestCase')),
      create('new net.xp_framework.unittest.core.generics.Lookup<string, TestCase>()')->getClass()->genericArguments()
    );
  }
}
