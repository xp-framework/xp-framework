<?php namespace net\xp_framework\unittest\core;

/**
 * Tests typeof() functionality
 *
 */
class TypeOfTest extends \unittest\TestCase {

  #[@test]
  public function null() {
    $this->assertEquals(\lang\Type::$VOID, typeof(NULL));
  }

  #[@test]
  public function this() {
    $this->assertEquals($this->getClass(), typeof($this));
  }

  #[@test]
  public function string() {
    $this->assertEquals(\lang\Primitive::$STRING, typeof($this->name));
  }

  #[@test]
  public function intArray() {
    $this->assertEquals(\lang\ArrayType::forName('var[]'), typeof(array(1, 2, 3)));
  }

  #[@test]
  public function intMap() {
    $this->assertEquals(\lang\MapType::forName('[:var]'), typeof(array('one' => 1, 'two' => 2, 'three' => 3)));
  }
}
