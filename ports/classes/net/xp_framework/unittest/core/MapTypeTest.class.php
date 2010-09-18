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
  }
?>
