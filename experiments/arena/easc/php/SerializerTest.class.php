<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'util.profiling.unittest.TestCase', 
    'util.Date',
    'util.Hashmap',
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
    
    /**
     * Test serialization of an array containing three integers 
     * (1, 2 and 5)
     *
     * @access  public
     */
    #[@test]
    function representationOfIntegerArray() {
      $this->assertEquals(
        'a:3:{i:0;i:1;i:1;i:2;i:2;i:5;}', 
        Serializer::representationOf($var= array(1, 2, 5))
      );
    }
    
    /**
     * Test serialization of an array containing two strings 
     * ("More" and "Power")
     *
     * @access  public
     */
    #[@test]
    function representationOfStringArray() {
      $this->assertEquals(
        'a:2:{i:0;s:4:"More";i:1;s:5:"Power";}', 
        Serializer::representationOf($var= array('More', 'Power'))
      );
    }
    
    /**
     * Test serialization of a date object
     *
     * @access  public
     */
    #[@test]
    function representationOfDate() {
      $this->assertEquals('T:1122644265;', Serializer::representationOf(new Date(1122644265)));
    }

    /**
     * Test serialization of a hashmap
     *
     * @access  public
     */
    #[@test]
    function representationOfHashmap() {
      $h= &new Hashmap();
      $h->put('key', 'value');
      $h->put('number', '6100');

      $this->assertEquals(
        'a:2:{s:3:"key";s:5:"value";s:6:"number";s:4:"6100";}', 
        Serializer::representationOf($h)
      );
    }

    /**
     * Test serialization of a hashmap with mixed values
     *
     * @access  public
     */
    #[@test]
    function representationOfMixedHashmap() {
      $h= &new Hashmap();
      $h->put('key', 'value');
      $h->put('number', 6100);

      $this->assertEquals(
        'a:2:{s:3:"key";s:5:"value";s:6:"number";i:6100;}', 
        Serializer::representationOf($h)
      );
    }
  }
?>
