<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'webservices.rest.RestDeserializer'
  );

  /**
   * TestCase
   *
   * @see   xp://webservices.rest.RestDeserializer
   */
  class RestConversionTest extends TestCase {
    protected $fixture= NULL;
  
    /**
     * Sets up test case
     *
     */
    public function setUp() {
      $this->fixture= newinstance('RestDeserializer', array(), '{
        public function deserialize($in, $type) { /* Intentionally empty */ }
      }');
    }
    
    /**
     * Test null
     *
     */
    #[@test]
    public function null() {
      $this->assertEquals(NULL, $this->fixture->convert(Type::$VAR, NULL));
    }

    /**
     * Test null in string context
     *
     */
    #[@test]
    public function null_as_string() {
      $this->assertEquals(NULL, $this->fixture->convert(Primitive::$STRING, NULL));
    }

    /**
     * Test null in int context
     *
     */
    #[@test]
    public function null_as_int() {
      $this->assertEquals(NULL, $this->fixture->convert(Primitive::$INT, NULL));
    }

    /**
     * Test null in double context
     *
     */
    #[@test]
    public function null_as_double() {
      $this->assertEquals(NULL, $this->fixture->convert(Primitive::$DOUBLE, NULL));
    }

    /**
     * Test null in bool context
     *
     */
    #[@test]
    public function null_as_bool() {
      $this->assertEquals(NULL, $this->fixture->convert(Primitive::$BOOL, NULL));
    }

    /**
     * Test string
     *
     */
    #[@test]
    public function string() {
      $this->assertEquals('Test', $this->fixture->convert(Primitive::$STRING, 'Test'));
    }

    /**
     * Test string
     *
     */
    #[@test]
    public function int_as_string() {
      $this->assertEquals('1', $this->fixture->convert(Primitive::$STRING, 1));
    }

    /**
     * Test string
     *
     */
    #[@test]
    public function double_as_string() {
      $this->assertEquals('1', $this->fixture->convert(Primitive::$STRING, 1.0));
    }

    /**
     * Test string
     *
     */
    #[@test]
    public function bool_as_string() {
      $this->assertEquals('1', $this->fixture->convert(Primitive::$STRING, TRUE));
      $this->assertEquals('', $this->fixture->convert(Primitive::$STRING, FALSE));
    }

    /**
     * Test string
     *
     */
    #[@test]
    public function array_as_string() {
      $this->assertEquals('Test', $this->fixture->convert(Primitive::$STRING, array('Test')));
    }

    /**
     * Test string
     *
     */
    #[@test]
    public function map_as_string() {
      $this->assertEquals('Test', $this->fixture->convert(Primitive::$STRING, array('name' => 'Test')));
    }

    /**
     * Test int
     *
     */
    #[@test]
    public function int() {
      $this->assertEquals(1, $this->fixture->convert(Primitive::$INT, 1));
    }

    /**
     * Test strings as ints
     *
     */
    #[@test]
    public function string_as_int() {
      $this->assertEquals(1, $this->fixture->convert(Primitive::$INT, '1'));
    }

    /**
     * Test doubles as ints
     *
     */
    #[@test]
    public function double_as_int() {
      $this->assertEquals(1, $this->fixture->convert(Primitive::$INT, 1.0));
    }

    /**
     * Test bools as doubles
     *
     */
    #[@test]
    public function bool_as_int() {
      $this->assertEquals(1, $this->fixture->convert(Primitive::$INT, TRUE));
      $this->assertEquals(0, $this->fixture->convert(Primitive::$INT, FALSE));
    }

    /**
     * Test int
     *
     */
    #[@test]
    public function array_as_int() {
      $this->assertEquals(1, $this->fixture->convert(Primitive::$INT, array(1)));
    }

    /**
     * Test int
     *
     */
    #[@test]
    public function map_as_int() {
      $this->assertEquals(1, $this->fixture->convert(Primitive::$INT, array('one' => 1)));
    }

    /**
     * Test double
     *
     */
    #[@test]
    public function double() {
      $this->assertEquals(1.0, $this->fixture->convert(Primitive::$DOUBLE, 1.0));
    }

    /**
     * Test strings as doubles
     *
     */
    #[@test]
    public function string_as_double() {
      $this->assertEquals(1.0, $this->fixture->convert(Primitive::$DOUBLE, '1.0'));
    }

    /**
     * Test ints as doubles
     *
     */
    #[@test]
    public function int_as_double() {
      $this->assertEquals(1.0, $this->fixture->convert(Primitive::$DOUBLE, 1));
    }

    /**
     * Test bools as doubles
     *
     */
    #[@test]
    public function bool_as_double() {
      $this->assertEquals(1.0, $this->fixture->convert(Primitive::$DOUBLE, TRUE));
      $this->assertEquals(0.0, $this->fixture->convert(Primitive::$DOUBLE, FALSE));
    }

    /**
     * Test double
     *
     */
    #[@test]
    public function array_as_double() {
      $this->assertEquals(1.0, $this->fixture->convert(Primitive::$DOUBLE, array(1.0)));
    }

    /**
     * Test double
     *
     */
    #[@test]
    public function map_as_double() {
      $this->assertEquals(1.0, $this->fixture->convert(Primitive::$DOUBLE, array('one' => 1.0)));
    }

    /**
     * Test bool
     *
     */
    #[@test]
    public function bool() {
      $this->assertEquals(TRUE, $this->fixture->convert(Primitive::$BOOL, TRUE));
      $this->assertEquals(FALSE, $this->fixture->convert(Primitive::$BOOL, FALSE));
    }

    /**
     * Test bool
     *
     */
    #[@test]
    public function int_as_bool() {
      $this->assertEquals(TRUE, $this->fixture->convert(Primitive::$BOOL, 1));
      $this->assertEquals(FALSE, $this->fixture->convert(Primitive::$BOOL, 0));
    }

    /**
     * Test bool
     *
     */
    #[@test]
    public function double_as_bool() {
      $this->assertEquals(TRUE, $this->fixture->convert(Primitive::$BOOL, 1.0));
      $this->assertEquals(FALSE, $this->fixture->convert(Primitive::$BOOL, 0.0));
    }

    /**
     * Test bool
     *
     */
    #[@test]
    public function string_as_bool() {
      $this->assertEquals(TRUE, $this->fixture->convert(Primitive::$BOOL, 'non-empty'));
      $this->assertEquals(FALSE, $this->fixture->convert(Primitive::$BOOL, ''));
    }

    /**
     * Test bool
     *
     */
    #[@test]
    public function array_as_bool() {
      $this->assertEquals(TRUE, $this->fixture->convert(Primitive::$BOOL, array(TRUE)));
      $this->assertEquals(FALSE, $this->fixture->convert(Primitive::$BOOL, array(FALSE)));
    }

    /**
     * Test bool
     *
     */
    #[@test]
    public function map_as_bool() {
      $this->assertEquals(TRUE, $this->fixture->convert(Primitive::$BOOL, array('one' => TRUE)));
      $this->assertEquals(FALSE, $this->fixture->convert(Primitive::$BOOL, array('one' => FALSE)));
    }

    /**
     * Test var-array
     *
     */
    #[@test]
    public function var_array() {
      $this->assertEquals(
        array(1, 2, 3), 
        $this->fixture->convert(new ArrayType('var[]'), array(1, 2, 3))
      );
    }

    /**
     * Test var-array
     *
     */
    #[@test]
    public function int_array() {
      $this->assertEquals(
        array(1, 2, 3), 
        $this->fixture->convert(new ArrayType('int[]'), array(1, '2', 3.0))
      );
    }

    /**
     * Test var-map
     *
     */
    #[@test]
    public function var_map() {
      $this->assertEquals(
        array('one' => 1, 'two' => 2, 'three' => 3),
        $this->fixture->convert(new MapType('[:var]'), array('one' => 1, 'two' => 2, 'three' => 3))
      );
    }

    /**
     * Test int-map
     *
     */
    #[@test]
    public function int_map() {
      $this->assertEquals(
        array('one' => 1, 'two' => 2, 'three' => 3),
        $this->fixture->convert(new MapType('[:int]'), array('one' => 1, 'two' => '2', 'three' => 3.0))
      );
    }
  }
?>
