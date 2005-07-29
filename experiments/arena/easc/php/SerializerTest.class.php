<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'util.profiling.unittest.TestCase', 
    'Serializer'
  );

  /**
   * Tests the serialization / deserialization functionality
   *
   * @purpose  xp://Serializer
   */
  class SerializerTest extends TestCase {

    /**
     * Test serialization of NULL
     *
     * @access  public
     */
    #[@test]
    function representationOfNull() {
      $this->assertEquals('N;', Serializer::representationOf($var= NULL));
    }

    /**
     * Test serialization of booleans
     *
     * @access  public
     */
    #[@test]
    function representationOfBooleans() {
      $this->assertEquals('b:1;', Serializer::representationOf($var= TRUE));
      $this->assertEquals('b:0;', Serializer::representationOf($var= FALSE));
    }

    /**
     * Test serialization of integers
     *
     * @access  public
     */
    #[@test]
    function representationOfIntegers() {
      $this->assertEquals('i:6100;', Serializer::representationOf($var= 6100));
      $this->assertEquals('i:-6100;', Serializer::representationOf($var= -6100));
    }

    /**
     * Test serialization of floats
     *
     * @access  public
     */
    #[@test]
    function representationOfFloats() {
      $this->assertEquals('f:0.1;', Serializer::representationOf($var= 0.1));
      $this->assertEquals('f:-0.1;', Serializer::representationOf($var= -0.1));
    }

    /**
     * Test serialization of the string "Hello World"
     *
     * @access  public
     */
    #[@test]
    function representationOfString() {
      $this->assertEquals('s:11:"Hello World";', Serializer::representationOf($var= 'Hello World'));
    }
  }
?>
