<?php namespace net\xp_framework\unittest\core;

use unittest\TestCase;
use lang\MapType;

/**
 * TestCase
 *
 * @see      xp://lang.MapType
 */
class MapTypeTest extends TestCase {

  #[@test]
  public function typeForName() {
    $this->assertInstanceOf('lang.MapType', \lang\Type::forName('[:string]'));
  }

  #[@test, @expect('lang.IllegalArgumentException')]
  public function mapTypeForPrimitive() {
    MapType::forName('string');
  }

  #[@test, @expect('lang.IllegalArgumentException')]
  public function mapTypeForArray() {
    MapType::forName('string[]');
  }

  #[@test]
  public function newMapTypeWithString() {
    $this->assertEquals(MapType::forName('[:int]'), new MapType('int'));
  }

  #[@test]
  public function newMapTypeWithTypeInstance() {
    $this->assertEquals(MapType::forName('[:int]'), new MapType(\lang\Primitive::$INT));
  }

  #[@test]
  public function stringComponentType() {
    $this->assertEquals(\lang\Primitive::$STRING, MapType::forName('[:string]')->componentType());
  }

  #[@test]
  public function arrayComponentType() {
    $this->assertEquals(\lang\ArrayType::forName('int[]'), MapType::forName('[:int[]]')->componentType());
  }

  #[@test]
  public function mapComponentType() {
    $this->assertEquals(MapType::forName('[:int]'), MapType::forName('[:[:int]]')->componentType());
  }

  #[@test]
  public function objectComponentType() {
    $this->assertEquals(\lang\XPClass::forName('lang.Object'), MapType::forName('[:lang.Object]')->componentType());
  }

  #[@test]
  public function varComponentType() {
    $this->assertEquals(\lang\Type::$VAR, MapType::forName('[:var]')->componentType());
  }

  #[@test]
  public function isInstance() {
    $this->assertInstanceOf(MapType::forName('[:string]'), array('greet' => 'Hello', 'whom' => 'World'));
  }

  #[@test]
  public function isInstanceOfName() {
    $this->assertInstanceOf('[:string]', array('greet' => 'Hello', 'whom' => 'World'));
  }

  #[@test]
  public function intMapIsNotAnInstanceOfStringMap() {
    $this->assertFalse(MapType::forName('[:string]')->isInstance(array('one' => 1, 'two' => 2)));
  }

  #[@test]
  public function varMap() {
    $this->assertTrue(MapType::forName('[:var]')->isInstance(array('one' => 1, 'two' => 'Zwei', 'three' => new \lang\types\Integer(3))));
  }

  #[@test]
  public function arrayIsNotAnInstanceOfVarMap() {
    $this->assertFalse(MapType::forName('[:var]')->isInstance(array(1, 2, 3)));
  }

  #[@test]
  public function stringMapAssignableFromStringMap() {
    $this->assertTrue(MapType::forName('[:string]')->isAssignableFrom('[:string]'));
  }

  #[@test]
  public function stringMapAssignableFromStringMapType() {
    $this->assertTrue(MapType::forName('[:string]')->isAssignableFrom(MapType::forName('[:string]')));
  }

  #[@test]
  public function stringMapNotAssignableFromIntType() {
    $this->assertFalse(MapType::forName('[:string]')->isAssignableFrom(\lang\Primitive::$INT));
  }

  #[@test]
  public function stringMapNotAssignableFromClassType() {
    $this->assertFalse(MapType::forName('[:string]')->isAssignableFrom($this->getClass()));
  }

  #[@test]
  public function stringMapNotAssignableFromString() {
    $this->assertFalse(MapType::forName('[:string]')->isAssignableFrom('string'));
  }

  #[@test]
  public function stringMapNotAssignableFromStringArray() {
    $this->assertFalse(MapType::forName('[:string]')->isAssignableFrom('string[]'));
  }

  #[@test]
  public function stringMapNotAssignableFromVar() {
    $this->assertFalse(MapType::forName('[:string]')->isAssignableFrom('var'));
  }

  #[@test]
  public function stringMapNotAssignableFromVoid() {
    $this->assertFalse(MapType::forName('[:string]')->isAssignableFrom('void'));
  }

  #[@test]
  public function varMapAssignableFromIntMap() {
    $this->assertFalse(MapType::forName('[:var]')->isAssignableFrom('[:int]'));
  }
}
