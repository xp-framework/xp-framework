<?php
/* This class is part of the XP framework
 *
 * $Id: MapTypeTest.class.php 14822 2010-09-14 07:51:30Z friebe $ 
 */

  uses(
    'unittest.TestCase',
    'lang.MapType'
  );

  /**
   * TestCase
   *
   * @see      xp://lang.MapType
   */
  class MapTypeTest extends TestCase {
  
    /**
     * Test static Type::forName() method returns an array type
     *
     */
    #[@test]
    public function typeForName() {
      $this->assertInstanceOf('lang.MapType', Type::forName('[:string]'));
    }

    /**
     * Test static MapType::forName() method
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function mapTypeForPrimitive() {
      MapType::forName('string');
    }

    /**
     * Test static MapType::forName() method
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function mapTypeForArray() {
      MapType::forName('string[]');
    }

    /**
     * Test MapType constructor
     *
     */
    #[@test]
    public function newMapTypeWithString() {
      $this->assertEquals(MapType::forName('[:int]'), new MapType('int'));
    }

    /**
     * Test MapType constructor
     *
     */
    #[@test]
    public function newMapTypeWithTypeInstance() {
      $this->assertEquals(MapType::forName('[:int]'), new MapType(Primitive::$INT));
    }

    /**
     * Test componentType() method
     *
     */
    #[@test]
    public function stringComponentType() {
      $this->assertEquals(Primitive::$STRING, MapType::forName('[:string]')->componentType());
    }

    /**
     * Test componentType() method
     *
     */
    #[@test]
    public function arrayComponentType() {
      $this->assertEquals(ArrayType::forName('int[]'), MapType::forName('[:int[]]')->componentType());
    }

    /**
     * Test componentType() method
     *
     */
    #[@test]
    public function mapComponentType() {
      $this->assertEquals(MapType::forName('[:int]'), MapType::forName('[:[:int]]')->componentType());
    }

    /**
     * Test componentType() method
     *
     */
    #[@test]
    public function objectComponentType() {
      $this->assertEquals(XPClass::forName('lang.Object'), MapType::forName('[:lang.Object]')->componentType());
    }

    /**
     * Test componentType() method
     *
     */
    #[@test]
    public function varComponentType() {
      $this->assertEquals(Type::$VAR, MapType::forName('[:var]')->componentType());
    }

    /**
     * Test isInstance() method
     *
     */
    #[@test]
    public function isInstance() {
      $this->assertInstanceOf(MapType::forName('[:string]'), array('greet' => 'Hello', 'whom' => 'World'));
    }

    /**
     * Test isInstance() method
     *
     */
    #[@test]
    public function isInstanceOfName() {
      $this->assertInstanceOf('[:string]', array('greet' => 'Hello', 'whom' => 'World'));
    }

    /**
     * Test isInstance() method
     *
     */
    #[@test]
    public function intMapIsNotAnInstanceOfStringMap() {
      $this->assertFalse(MapType::forName('[:string]')->isInstance(array('one' => 1, 'two' => 2)));
    }

    /**
     * Test isInstance() method
     *
     */
    #[@test]
    public function varMap() {
      $this->assertTrue(MapType::forName('[:var]')->isInstance(array('one' => 1, 'two' => 'Zwei', 'three' => new Integer(3))));
    }

    /**
     * Test isAssignableFrom() method on strings
     *
     */
    #[@test]
    public function stringMapAssignableFromStringMap() {
      $this->assertTrue(MapType::forName('[:string]')->isAssignableFrom('[:string]'));
    }

    /**
     * Test isAssignableFrom() method on strings
     *
     */
    #[@test]
    public function stringMapAssignableFromStringMapType() {
      $this->assertTrue(MapType::forName('[:string]')->isAssignableFrom(MapType::forName('[:string]')));
    }

    /**
     * Test isAssignableFrom() method on strings
     *
     */
    #[@test]
    public function stringMapNotAssignableFromIntType() {
      $this->assertFalse(MapType::forName('[:string]')->isAssignableFrom(Primitive::$INT));
    }

    /**
     * Test isAssignableFrom() method on strings
     *
     */
    #[@test]
    public function stringMapNotAssignableFromClassType() {
      $this->assertFalse(MapType::forName('[:string]')->isAssignableFrom($this->getClass()));
    }

    /**
     * Test isAssignableFrom() method on strings
     *
     */
    #[@test]
    public function stringMapNotAssignableFromString() {
      $this->assertFalse(MapType::forName('[:string]')->isAssignableFrom('string'));
    }

    /**
     * Test isAssignableFrom() method on strings
     *
     */
    #[@test]
    public function stringMapNotAssignableFromStringArray() {
      $this->assertFalse(MapType::forName('[:string]')->isAssignableFrom('string[]'));
    }

    /**
     * Test isAssignableFrom() method on strings
     *
     */
    #[@test]
    public function stringMapNotAssignableFromVar() {
      $this->assertFalse(MapType::forName('[:string]')->isAssignableFrom('var'));
    }

    /**
     * Test isAssignableFrom() method on strings
     *
     */
    #[@test]
    public function stringMapNotAssignableFromVoid() {
      $this->assertFalse(MapType::forName('[:string]')->isAssignableFrom('void'));
    }

    /**
     * Test isAssignableFrom() method on strings
     *
     */
    #[@test]
    public function varMapAssignableFromIntMap() {
      $this->assertFalse(MapType::forName('[:var]')->isAssignableFrom('[:int]'));
    }
  }
?>
