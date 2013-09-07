<?php namespace net\xp_framework\unittest\core;

use lang\ArrayType;

/**
 * TestCase
 *
 * @see      xp://lang.ArrayType
 */
class ArrayTypeTest extends \unittest\TestCase {

  #[@test]
  public function typeForName() {
    $this->assertInstanceOf('lang.ArrayType', \lang\Type::forName('string[]'));
  }

  #[@test, @expect('lang.IllegalArgumentException')]
  public function arrayTypeForName() {
    ArrayType::forName('string');
  }

  #[@test]
  public function newArrayTypeWithString() {
    $this->assertEquals(ArrayType::forName('int[]'), new ArrayType('int'));
  }

  #[@test]
  public function newArrayTypeWithTypeInstance() {
    $this->assertEquals(ArrayType::forName('int[]'), new ArrayType(\lang\Primitive::$INT));
  }

  #[@test]
  public function stringComponentType() {
    $this->assertEquals(\lang\Primitive::$STRING, ArrayType::forName('string[]')->componentType());
  }

  #[@test]
  public function objectComponentType() {
    $this->assertEquals(\lang\XPClass::forName('lang.Object'), ArrayType::forName('lang.Object[]')->componentType());
  }

  #[@test]
  public function varComponentType() {
    $this->assertEquals(\lang\Type::$VAR, ArrayType::forName('var[]')->componentType());
  }

  #[@test]
  public function isInstance() {
    $this->assertInstanceOf(ArrayType::forName('string[]'), array('Hello', 'World'));
  }

  #[@test]
  public function isInstanceOfName() {
    $this->assertInstanceOf('string[]', array('Hello', 'World'));
  }

  #[@test]
  public function intArrayIsNotAnInstanceOfStringArray() {
    $this->assertFalse(ArrayType::forName('string[]')->isInstance(array(1, 2)));
  }

  #[@test]
  public function mapIsNotAnInstanceOfArray() {
    $this->assertFalse(ArrayType::forName('var[]')->isInstance(array('Hello' => 'World')));
  }

  #[@test]
  public function stringArrayAssignableFromStringArray() {
    $this->assertTrue(ArrayType::forName('string[]')->isAssignableFrom('string[]'));
  }

  #[@test]
  public function stringArrayAssignableFromStringArrayType() {
    $this->assertTrue(ArrayType::forName('string[]')->isAssignableFrom(ArrayType::forName('string[]')));
  }

  #[@test]
  public function stringArrayNotAssignableFromIntType() {
    $this->assertFalse(ArrayType::forName('string[]')->isAssignableFrom(\lang\Primitive::$INT));
  }

  #[@test]
  public function stringArrayNotAssignableFromClassType() {
    $this->assertFalse(ArrayType::forName('string[]')->isAssignableFrom($this->getClass()));
  }

  #[@test]
  public function stringArrayNotAssignableFromString() {
    $this->assertFalse(ArrayType::forName('string[]')->isAssignableFrom('string'));
  }

  #[@test]
  public function stringArrayNotAssignableFromStringMap() {
    $this->assertFalse(ArrayType::forName('string[]')->isAssignableFrom('[:string]'));
  }

  #[@test]
  public function stringArrayNotAssignableFromVar() {
    $this->assertFalse(ArrayType::forName('string[]')->isAssignableFrom('var'));
  }

  #[@test]
  public function stringArrayNotAssignableFromVoid() {
    $this->assertFalse(ArrayType::forName('string[]')->isAssignableFrom('void'));
  }

  #[@test]
  public function varArrayAssignableFromIntArray() {
    $this->assertFalse(ArrayType::forName('var[]')->isAssignableFrom('int[]'));
  }
}
