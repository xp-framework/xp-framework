<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'lang.ArrayType'
  );

  /**
   * TestCase
   *
   * @see      xp://lang.ArrayType
   */
  class ArrayTypeTest extends TestCase {
  
    /**
     * Test static Type::forName() method returns an array type
     *
     */
    #[@test]
    public function typeForName() {
      $this->assertInstanceOf('lang.ArrayType', Type::forName('string[]'));
    }

    /**
     * Test static ArrayType::forName() method
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function arrayTypeForName() {
      ArrayType::forName('string');
    }

    /**
     * Test componentType() method
     *
     */
    #[@test]
    public function stringComponentType() {
      $this->assertEquals(Primitive::$STRING, ArrayType::forName('string[]')->componentType());
    }

    /**
     * Test componentType() method
     *
     */
    #[@test]
    public function objectComponentType() {
      $this->assertEquals(XPClass::forName('lang.Object'), ArrayType::forName('lang.Object[]')->componentType());
    }

    /**
     * Test componentType() method
     *
     */
    #[@test]
    public function varComponentType() {
      $this->assertEquals(Type::$VAR, ArrayType::forName('var[]')->componentType());
    }

    /**
     * Test isInstance() method
     *
     */
    #[@test]
    public function isInstance() {
      $this->assertInstanceOf(ArrayType::forName('string[]'), array('Hello', 'World'));
    }

    /**
     * Test isInstance() method
     *
     */
    #[@test]
    public function isInstanceOfName() {
      $this->assertInstanceOf('string[]', array('Hello', 'World'));
    }

    /**
     * Test isInstance() method
     *
     */
    #[@test]
    public function intArrayIsNotAnInstanceOfStringArray() {
      $this->assertFalse(ArrayType::forName('string[]')->isInstance(array(1, 2)));
    }
  }
?>
