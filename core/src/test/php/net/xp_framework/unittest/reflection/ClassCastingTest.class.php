<?php namespace net\xp_framework\unittest\reflection;

use unittest\TestCase;


/**
 * TestCase
 *
 * @see      xp://lang.XPClass#cast
 */
class ClassCastingTest extends TestCase {

  /**
   * Tests cast() method
   *
   */
  #[@test]
  public function thisClassCastingThis() {
    $this->assertEquals($this, $this->getClass()->cast($this));
  }

  /**
   * Tests cast() method
   *
   */
  #[@test]
  public function parentClassCastingThis() {
    $this->assertEquals($this, $this->getClass()->getParentClass()->cast($this));
  }

  /**
   * Tests cast() method
   *
   */
  #[@test]
  public function objectClassCastingThis() {
    $this->assertEquals($this, \lang\XPClass::forName('lang.Object')->cast($this));
  }

  /**
   * Tests cast() method
   *
   */
  #[@test, @expect('lang.ClassCastException')]
  public function thisClassCastingAnObject() {
    $this->getClass()->cast(new \lang\Object());
  }

  /**
   * Tests cast() method
   *
   */
  #[@test, @expect('lang.ClassCastException')]
  public function thisClassCastingAnUnrelatedClass() {
    $this->getClass()->cast(new \lang\types\String('Hello'));
  }

  /**
   * Tests cast() method
   *
   */
  #[@test]
  public function thisClassCastingNull() {
    $this->assertEquals(\xp::null(), $this->getClass()->cast(null));
  }

  /**
   * Tests cast() method
   *
   */
  #[@test, @expect('lang.IllegalArgumentException')]
  public function castPrimitive() {
    $this->getClass()->cast(0);
  }
}
