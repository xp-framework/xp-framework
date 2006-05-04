<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'util.profiling.unittest.TestCase',
    'org.json.JsonDecoder'
  );

  /**
   * Testcase for JsonDecoder
   *
   * @see      http://json.org
   * @purpose  Testcase
   */
  class JsonDecoderTest extends TestCase {
  
    /**
     * Setup text fixture
     *
     * @access  public
     */
    function setUp() {
      $this->decoder= &new JsonDecoder();
    }
    
    /**
     * Test string encoding
     *
     * @access  public
     */
    #[@test]
    function encodeString() {
      $this->assertEquals('"foo"', $this->decoder->encode('foo'));
      $this->assertEquals('"fo\\no"', $this->decoder->encode('fo'."\n".'o'));
    }
  
    /**
     * Test integer encoding
     *
     * @access  public
     */
    #[@test]
    function encodeInt() {
      $this->assertEquals('1', $this->decoder->encode(1));
      $this->assertEquals('-1', $this->decoder->encode(-1));
    }
    
    /**
     * Test float encoding
     *
     * @access  public
     */
    #[@test]
    function encodeFloat() {
      $this->assertEquals('1', $this->decoder->encode(1.0));    
      $this->assertEquals('1.1', $this->decoder->encode(1.1));
    }
    
    /**
     * Test boolean and NULL encoding
     *
     * @access  public
     */
    #[@test]
    function encodeBooleanAndNull() {
      $this->assertEquals('true', $this->decoder->encode(TRUE));
      $this->assertEquals('false', $this->decoder->encode(FALSE));
      $this->assertEquals('null', $this->decoder->encode(NULL));
    }
    
    /**
     * Test string encoding
     *
     * @access  public
     */
    #[@test]
    function encodeArray() {
      $this->assertEquals(
        '[ ]',
        $this->decoder->encode(array())
      );
      
      $this->assertEquals(
        '[ 1 , 2 , 3 ]',
        $this->decoder->encode(array(1, 2, 3))
      );
      
      $this->assertEquals(
        '[ "foo" , 2 , "bar" ]',
        $this->decoder->encode(array('foo', 2, 'bar'))
      );
    }
    
    /**
     * Test string encoding
     *
     * @access  public
     */
    #[@test]
    function encodeHashmap() {
      $this->assertEquals(
        '{ "foo" : "bar" , "bar" : "baz" }',
        $this->decoder->encode(array('foo' => 'bar', 'bar' => 'baz'))
      );
    }
    
    /**
     * Test array decoding
     *
     * @access  public
     */
    #[@test]
    function decodeArray() {
      $this->assertEquals(
        array(TRUE, FALSE, NULL),
        $this->decoder->decode('[ true , false, null ]')
      );
    }
    
    /**
     * Test string decoding
     *
     * @access  public
     */
    #[@test]
    function decodeString() {
      $this->assertEquals(
        'foobar',
        $this->decoder->decode('"foobar"')
      );
      $this->assertEquals(
        'foo\\bar',
        $this->decoder->decode('"foo\\\\bar"')
      );
      $this->assertEquals(
        'foo"bar',
        $this->decoder->decode('"foo\\"bar"')
      );
      $this->assertEquals(
        'foobar\"',
        $this->decoder->decode('"foobar\\\\\""')
      );
      $this->assertEquals(
        "foobar\t",
        $this->decoder->decode('"foobar\\t"')
      );
      $this->assertEquals(
        'foobar'."\t".'\"',
        $this->decoder->decode('"foobar\\t\\\\\""')
      );
    }
    
    /**
     * Test number decoding
     *
     * @access  public
     */
    #[@test]
    function decodeNumber() {
      $this->assertEquals(
        1,
        $this->decoder->decode('1')
      );
      $this->assertEquals(
        1.1,
        $this->decoder->decode('1.1')
      );
      $this->assertEquals(
        -1.1,
        $this->decoder->decode('-1.1')
      );
      $this->assertEquals(
        0.1,
        $this->decoder->decode('1E-1')
      );
      $this->assertEquals(
        -0.1,
        $this->decoder->decode('-1E-1')
      );
    }
    
    /**
     * Test string array decoding
     *
     * @access  public
     */
    #[@test]
    function decodeStringArray() {
      $this->assertEquals(
        array('foo', 'bar'),
        $this->decoder->decode('[ "foo" , "bar" ]')
      );
    }
    
    /**
     * Test object decoding
     *
     * @access  public
     */
    #[@test]
    function decodeHashmap() {
      $this->assertEquals(
        array('foo' => 'bar', 'bar' => 'baz'),
        $this->decoder->decode('{ "foo" : "bar", "bar" : "baz" }')
      );
    }
    
    /**
     * Test object array decoding
     *
     * @access  public
     */
    #[@test]
    function decodeObjectArray() {
      $this->assertEquals(
        array(array('foo' => 1), array('bar' => 'baz')),
        $this->decoder->decode('[ { "foo" : 1 } , { "bar" : "baz" } ]')
      );
    }
    
    /**
     * Test nested object decoding
     *
     * @access  public
     */
    #[@test]
    function decodeNestedObject() {
      $this->assertEquals(
        array('ref' => array('foo' => 'bar')),
        $this->decoder->decode('{ "ref" : { "foo" : "bar" } }')
      );
    }
    
    /**
     * Decoding non-json data should result in an exception
     *
     * @access  public
     */
    #[@test,@expect('org.json.JsonException')]
    function decodeInvalidData() {
      $this->decoder->decode('<xml version="1.0" encoding="iso-8859-1"?><document/>');
    }
    
    /**
     * Test encoding of object
     *
     * @access  public
     */
    #[@test]
    function encodeObject() {
      $o= &new Object();
      $o->__id= '<bogusid>';
      $o->prop= 'prop';

      $this->assertEquals(
        '{ "__jsonclass__" : [ "__construct()" ] , "__xpclass__" : "lang.Object" , "__id" : "<bogusid>" , "prop" : "prop" }',
        $this->decoder->encode($o)
      );
    }    

    /**
     * Test decoding of object
     *
     * @access  public
     */
    #[@test]
    function decodeObject() {
      $o= &new Object();
      $o->__id= '<bogusid>';
      $o->prop= 'prop';

      $this->assertEquals(
        $o,
        $this->decoder->decode('{ "__jsonclass__" : [ "__construct()" ] , "__xpclass__" : "lang.Object" , "__id" : "<bogusid>" , "prop" : "prop" }')
      );
    }    
  }
?>
