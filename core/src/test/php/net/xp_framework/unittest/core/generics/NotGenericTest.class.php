<?php namespace net\xp_framework\unittest\core\generics;

/**
 * TestCase for reflection on a non-generic
 *
 */
class NotGenericTest extends \unittest\TestCase {
  
  #[@test]
  public function thisIsNotAGeneric() {
    $this->assertFalse($this->getClass()->isGeneric());
  }

  #[@test]
  public function thisIsNotAGenericDefinition() {
    $this->assertFalse($this->getClass()->isGenericDefinition());
  }

  #[@test, @expect('lang.IllegalStateException')]
  public function cannotCreateGenericTypeFromThis() {
    $this->getClass()->newGenericType(array());
  }

  #[@test, @expect('lang.IllegalStateException')]
  public function cannotGetGenericArgumentsForThis() {
    $this->getClass()->genericArguments();
  }

  #[@test, @expect('lang.IllegalStateException')]
  public function cannotGetGenericComponentsForThis() {
    $this->getClass()->genericComponents();
  }
}
