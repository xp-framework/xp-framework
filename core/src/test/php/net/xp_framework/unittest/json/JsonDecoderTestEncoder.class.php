<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'util.Date',
    'webservices.json.JsonDecoder'
  );

  /**
   * Testcase for JsonDecoder
   *
   * @see      http://json.org
   * @purpose  Testcase
   */
  class JsonDecoderTestEncoder extends TestCase {
    protected $decoder= NULL;
        
    /**
     * Setup text fixture
     *
     */
    public function setUp() {
      $this->decoder= new JsonDecoder();
      date_default_timezone_set('Europe/Berlin');
    }
    
    /**
     * Test string encoding
     *
     */
    #[@test]
    public function encodeString() {
      $this->assertEquals('"foo"', $this->decoder->encode('foo'));
    }
    
    /**
     * Test string encoding
     *
     */
    #[@test]
    public function encodeUTF8String() {
      $this->assertEquals('"fÃ¶o"', $this->decoder->encode('föo'));
    }

    /**
     * Test String with quotation mark encoding
     *
     */
     #[@test]
     public function encodeQuotationMarkString() {
       $this->assertEquals('"f\\"o\\"o"', $this->decoder->encode('f"o"o'));
     }

     /**
     * Test String with reverse solidus encoding
     *
     */
     #[@test]
     public function encodeReverseSolidusString() {
       $this->assertEquals('"fo\\\\o"', $this->decoder->encode('fo\\o'));
     }

     /**
     * Test String with solidus encoding
     *
     */
     #[@test]
     public function encodeSolidusString() {
       $this->assertEquals('"fo\\/o"', $this->decoder->encode('fo/o'));
     }

     /**
     * Test String with backspace encoding
     *
     */
     #[@test]
     public function encodeBackspaceString() {
       $this->assertEquals('"fo\\bo"', $this->decoder->encode('fo'."\b".'o'));
     }

     /**
     * Test String with formfeed encoding
     *
     */
     #[@test]
     public function encodeFormfeedString() {
       $this->assertEquals('"fo\\fo"', $this->decoder->encode('fo'."\f".'o'));
     }

    /**
     * Test string with newline encoding
     *
     */
     #[@test]
     public function encodeNewlineString() {
       $this->assertEquals('"fo\\no"', $this->decoder->encode('fo'."\n".'o'));
     }

     /**
     * Test String with carriage return encoding
     *
     */
     #[@test]
     public function encodeCarriageReturnString() {
       $this->assertEquals('"fo\\ro"', $this->decoder->encode('fo'."\r".'o'));
     }

     /**
     * Test String with horizontal tab encoding
     *
     */
     #[@test]
     public function encodeHorizontalTabString() {
       $this->assertEquals('"fo\\to"', $this->decoder->encode('fo'."\t".'o'));
     }
  
    /**
     * Test positive small integer encoding
     *
     */
    #[@test]
    public function encodePositiveSmallInt() {
      $this->assertEquals('1', $this->decoder->encode(1));
    }

    /**
     * Test negative small integer encoding
     *
     */
    #[@test]
    public function encodeNegativeSmallInt() {
      $this->assertEquals('-1', $this->decoder->encode(-1));
    }

    /**
     * Test positive big integer encoding
     *
     */
    #[@test]
    public function encodePositiveBigInt() {
      $this->assertEquals('2147483647', $this->decoder->encode(2147483647));
    }
    
    /**
     * Test negative big integer encoding
     *
     */
    #[@test]
    public function encodeNegativeBigInt() {
      $this->assertEquals('-2147483647', $this->decoder->encode(-2147483647));
    }

    /**
     * Test integer float encoding
     *
     */
    #[@test]
    public function encodeIntegerFloat() {
      $this->assertEquals('1', $this->decoder->encode(1.0));
    }

    /**
     * Test small positive float encoding
     *
     */
    #[@test]
    public function encodeSmallPositiveFloat() { 
      $this->assertEquals('1.1', $this->decoder->encode(1.1));
    }
    
    /**
     * Test small negative float encoding
     *
     */
    #[@test]
    public function encodeFloat() { 
      $this->assertEquals('-1.1', $this->decoder->encode(-1.1));
    }

    /**
     * Test big positive float encoding
     *
     */
    #[@test]
    public function encodeBigPositiveFloat() { 
      $this->assertEquals('9999999999999.1', $this->decoder->encode(9999999999999.1));
    }

    /**
     * Test big negative float encoding
     *
     */
    #[@test]
    public function encodeBigNevativeFloat() { 
      $this->assertEquals('-9999999999999.1', $this->decoder->encode(-9999999999999.1));
    }

    /**
     * Test very small float encoding
     *
     */
    #[@test]
    public function encodeVerySmallFloat() { 
      $this->assertEquals('1.0E-11', $this->decoder->encode(0.00000000001));
    }

    /**
     * Test almost very small float encoding
     *
     */
    #[@test]
    public function encodeAlmostVerySmallFloat() { 
      $this->assertEquals('0.123456789', $this->decoder->encode(0.123456789));
    }

    /**
     * Test NULL encoding
     *
     */
    #[@test]
    public function encodeNull() {
      $this->assertEquals('null', $this->decoder->encode(NULL));
    }

    /**
     * Test TRUE encoding
     *
     */
    #[@test]
    public function encodeTrue() {
      $this->assertEquals('true', $this->decoder->encode(TRUE));
    }

    /**
     * Test FALSE encoding
     *
     */
    #[@test]
    public function encodeFalse() {
      $this->assertEquals('false', $this->decoder->encode(FALSE));
    }
    
    /**
     * Test empty array encoding
     *
     */
    #[@test]
    public function encodeEmptyArray() {
      $this->assertEquals('[ ]', $this->decoder->encode(array()));
    }

    /**
     * Test simple numeric array encoding
     *
     */
    #[@test]
    public function encodeSimpleNumericArray() {
      $this->assertEquals(
        '[ 1 , 2 , 3 ]',
        $this->decoder->encode(array(1, 2, 3))
      );
    }

    /**
     * Test simple mixed array encoding
     *
     */
    #[@test]
    public function encodeSimpleMixedArray() {
      $this->assertEquals(
        '[ "foo" , 2 , "bar" ]',
        $this->decoder->encode(array('foo', 2, 'bar'))
      );
    }

    /**
     * Test normal mixed array encoding
     *
     */
    #[@test]
    public function encodeNormalMixedArray() {
      $this->assertEquals(
        '[ "foo" , 0.001 , false , [ 1 , 2 , 3 ] ]',
        $this->decoder->encode(array('foo', 0.001, FALSE, array(1, 2, 3)))
      );
    }
       
    /**
     * Test simple hashmap encoding
     *
     */
    #[@test]
    public function encodeSimpleHashmap() {
      $this->assertEquals(
        '{ "foo" : "bar" , "bar" : "baz" }',
        $this->decoder->encode(array('foo' => 'bar', 'bar' => 'baz'))
      );
    }

    /**
     * Test complex mixed array encoding
     *
     */
    #[@test]
    public function encodeComplexMixedArray() {
      $this->assertEquals(
       '[ "foo" , true , { "foo" : "bar" , "0" : 2 } ]',
       $this->decoder->encode(array('foo', TRUE, array('foo' => 'bar', 2)))
      );
    }

    /**
     * Test complex hashmap encoding
     *
     */
    #[@test]
    public function encodeComplexHashmap() {
      $this->assertEquals(
        '{ "foo" : "bar" , "3" : 0.123 , "4" : false , "array" : [ 1 , "foo" , false ] , '.
        '"array2" : { "0" : true , "bar" : 4 } , "array3" : { "foo" : { "foo" : "bar" } } }',
        $this->decoder->encode(array('foo' => 'bar',
          3 => 0.123,
          FALSE,
          "array" => array(1, "foo", FALSE),
          "array2" => array(TRUE, "bar" => 4),
          "array3" => array("foo" => array("foo" => "bar"))
        ))
        );
    }
    
    /**
     * Test encoding of object
     *
     */
    #[@test]
    public function encodeObject() {
      $o= ClassLoader::defineClass('JsonTestValueClass', 'lang.Object', array(), '{
        public $prop  = NULL;
      }')->newInstance();

      $o->__id= '<bogusid>';
      $o->prop= 'prop';

      $this->assertEquals(
        '{ "__jsonclass__" : [ "__construct()" ] , "__xpclass__" : "JsonTestValueClass" , "prop" : "prop" , "__id" : "<bogusid>" }',
        $this->decoder->encode($o)
      );
    }
    
    /**
     * Test encoding of objects in array
     *
     */
    #[@test]
    public function encodeMoreObjects() {
      $o= ClassLoader::defineClass('JsonTestValueClass', 'lang.Object', array(), '{
        public $prop  = NULL;
      }');

      $oa=$o->newInstance();
      $ob=$o->newInstance();
      $oc=$o->newInstance();

      $oa->__id= '<bogusid>';
      $oa->prop= 'prop-a';
      $ob->__id= '<bogusid>';
      $ob->prop= 'prop-b';
      $oc->__id= '<bogusid>';
      $oc->prop= 'prop-c';

      $this->assertEquals(
        '{ "oa" : { "__jsonclass__" : [ "__construct()" ] , "__xpclass__" : "JsonTestValueClass" , "prop" : "prop-a" , "__id" : "<bogusid>" } , '.
        '"ob" : { "__jsonclass__" : [ "__construct()" ] , "__xpclass__" : "JsonTestValueClass" , "prop" : "prop-b" , "__id" : "<bogusid>" } , '.
        '"oc" : { "__jsonclass__" : [ "__construct()" ] , "__xpclass__" : "JsonTestValueClass" , "prop" : "prop-c" , "__id" : "<bogusid>" } }',
        $this->decoder->encode(array("oa" => $oa, "ob" => $ob, "oc" => $oc))
      );
    } 
    
    /**
     * Test date encoding
     *
     */
    #[@test]
    public function encodeDate() {
      $this->assertEquals(
        '{ "__jsonclass__" : [ "__construct()" ] , "__xpclass__" : "util.Date" , "constructor" : "__construct()" , "value" : "2009-05-18 01:02:03+0200" , "__id" : null }',
        $this->decoder->encode(new Date('2009-05-18 01:02:03'))
      );
    }

   /**
    * Test exception
    *
    */
   #[@test]
    public function encodeFileResource() {
      $je= NULL;
      $file= tmpfile();

      try {
        $this->decoder->encode($file);
      } catch (JsonException $je) {
        // Do nothing here
      }

      $this->assertInstanceOf(
        'webservices.json.JsonException',
        $je
      );
    }

    /**
     * Additional Tests
     * 
     */

    /**
     * Test Array with only one element
     *
     */
    #[@test]
    public function encodeOneElementArray() {
      $this->assertEquals('[ "foo" ]', $this->decoder->encode(array('foo')));
    }

    /**
     * Test Object with only one element
     *
     */
    #[@test]
    public function encodeOneElementObejct() {
      $this->assertEquals(
        '{ "foo" : "bar" }',
        $this->decoder->encode(array('foo' => 'bar'))
      );
    }
  }
?>
