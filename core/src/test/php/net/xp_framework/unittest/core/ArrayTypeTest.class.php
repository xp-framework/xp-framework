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
     * Test ArrayType constructor
     *
     */
    #[@test]
    public function newArrayTypeWithString() {
      $this->assertEquals(ArrayType::forName('int[]'), new ArrayType('int'));
    }

    /**
     * Test ArrayType constructor
     *
     */
    #[@test]
    public function newArrayTypeWithTypeInstance() {
      $this->assertEquals(ArrayType::forName('int[]'), new ArrayType(Primitive::$INT));
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

    /**
     * Test isInstance() method
     *
     */
    #[@test]
    public function mapIsNotAnInstanceOfArray() {
      $this->assertFalse(ArrayType::forName('var[]')->isInstance(array('Hello' => 'World')));
    }

    /**
     * Test isAssignableFrom() method on strings
     *
     */
    #[@test]
    public function stringArrayAssignableFromStringArray() {
      $this->assertTrue(ArrayType::forName('string[]')->isAssignableFrom('string[]'));
    }

    /**
     * Test isAssignableFrom() method on strings
     *
     */
    #[@test]
    public function stringArrayAssignableFromStringArrayType() {
      $this->assertTrue(ArrayType::forName('string[]')->isAssignableFrom(ArrayType::forName('string[]')));
    }

    /**
     * Test isAssignableFrom() method on strings
     *
     */
    #[@test]
    public function stringArrayNotAssignableFromIntType() {
      $this->assertFalse(ArrayType::forName('string[]')->isAssignableFrom(Primitive::$INT));
    }

    /**
     * Test isAssignableFrom() method on strings
     *
     */
    #[@test]
    public function stringArrayNotAssignableFromClassType() {
      $this->assertFalse(ArrayType::forName('string[]')->isAssignableFrom($this->getClass()));
    }

    /**
     * Test isAssignableFrom() method on strings
     *
     */
    #[@test]
    public function stringArrayNotAssignableFromString() {
      $this->assertFalse(ArrayType::forName('string[]')->isAssignableFrom('string'));
    }

    /**
     * Test isAssignableFrom() method on strings
     *
     */
    #[@test]
    public function stringArrayNotAssignableFromStringMap() {
      $this->assertFalse(ArrayType::forName('string[]')->isAssignableFrom('[:string]'));
    }

    /**
     * Test isAssignableFrom() method on strings
     *
     */
    #[@test]
    public function stringArrayNotAssignableFromVar() {
      $this->assertFalse(ArrayType::forName('string[]')->isAssignableFrom('var'));
    }

    /**
     * Test isAssignableFrom() method on strings
     *
     */
    #[@test]
    public function stringArrayNotAssignableFromVoid() {
      $this->assertFalse(ArrayType::forName('string[]')->isAssignableFrom('void'));
    }

    /**
     * Test isAssignableFrom() method on strings
     *
     */
    #[@test]
    public function varArrayAssignableFromIntArray() {
      $this->assertFalse(ArrayType::forName('var[]')->isAssignableFrom('int[]'));
    }
  }
?>
