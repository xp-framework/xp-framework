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
    function testObject() {
      $this->assertEquals(
        '{ "foo" : "bar" , "bar" : "baz" }',
        $this->decoder->encode((object)array('foo' => 'bar', 'bar' => 'baz'))
      );
    }    
  }
?>
