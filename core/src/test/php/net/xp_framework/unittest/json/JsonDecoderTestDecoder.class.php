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
  class JsonDecoderTestDecoder extends TestCase {
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
     * Test string decoding
     *
     */
    #[@test]
    public function decodeString() {
      $this->assertEquals(
        'foobar',
        $this->decoder->decode('"foobar"')
      );
    }

    /**
     * Test string decoding
     *
     */
    #[@test]
    public function decodeStringWithQuotedDelimiter() {
      $this->assertEquals(
        'foo"bar',
        $this->decoder->decode('"foo\\"bar"')
      );
    }

    /**
     * Test string decoding
     *
     */
    #[@test]
    public function decodeStringWithQuotes() {
      $this->assertEquals(
        'foo\\bar',
        $this->decoder->decode('"foo\\\\bar"')
      );
    }

    /**
     * Tests string decoding
     *
     */
    #[@test]
    public function decodeStringWithSolidus() {
      $this->assertEquals(
        'foo/bar',
        $this->decoder->decode('"foo\\/bar"')
      );
    }

    /**
     * Test string decoding
     *
     */
    #[@test]
    public function decodeStringWithQuotedQuotes() {
      $this->assertEquals(
        'foobar\"',
        $this->decoder->decode('"foobar\\\\\""')
      );
    }

    /**
     * Tests string decoding
     *
     */
    #[@test]
    public function decodeStringWithBackspace() {
      $this->assertEquals('fo'."\b".'o', $this->decoder->decode('"fo\\bo"'));
    }

    /**
     * Tests string decoding
     *
     */
    #[@test]
    public function decodeStringWithFormfeed() {
      $this->assertEquals('fo'."\f".'o', $this->decoder->decode('"fo\\fo"'));
    }

     /**
     * Tests string decoding
     *
     */
    #[@test]
    public function decodeStringWithNewline() {
       $this->assertEquals('fo'."\n".'o', $this->decoder->decode('"fo\\no"'));
    }

     /**
     * Tests string decoding
     *
     */
    #[@test]
    public function decodeStringWithCarriageReturn() {
      $this->assertEquals('fo'."\r".'o', $this->decoder->decode('"fo\\ro"'));
    }

    /**
     * Test string decoding
     *
     */
    #[@test]
    public function decodeStringWithTabEscape() {
      $this->assertEquals(
        "foobar\t",
        $this->decoder->decode('"foobar\\t"')
      );
    }

    /**
     * Test string decoding
     *
     */
    #[@test]
    public function decodeStringWithTabsAndQuotes() {
      $this->assertEquals(
        'foobar'."\t".'\"',
        $this->decoder->decode('"foobar\\t\\\\\""')
      );
    }

    /**
     * Test string decoding - Real life example
     *
     */
    #[@test]
    public function decodeStringWithHTML() {
      $this->assertEquals(
        "\nbbb ".'<span style="font-weight: bold;">tes</span>t " test'."\n",
        $this->decoder->decode('"\nbbb <span style=\"font-weight: bold;\">tes</span>t \" test\n"')
      );
    }

    /**
     * Test string decoding
     *
     */
    #[@test]
    public function decodeLongString() {
      with ($data= str_repeat('*', 6100)); {
        $this->assertEquals(strlen($data), strlen($this->decoder->decode('"'.$data.'"')), 'Decoded length mismatch');
      }
    }
    
    /**
     * Test string decoding
     *
     */
    #[@test]
    public function decodeUTF8String() {
      $this->assertEquals('Knüper', $this->decoder->decode('"KnÃ¼per"'));
    }
    
    
    /**
     * Test string decoding
     *
     */
    #[@test]
    public function decodeUTF8StringWithUnicodeCodepoint() {
      $this->assertEquals('Günther', $this->decoder->decode('"G\u00fcnther"'));
      $this->assertEquals('¤uro', $this->decoder->decode('"\u20ACuro"'));
    }

    /**
     * Test number decoding
     *
     */
    #[@test]
    public function decodeIntNumber() {
      $this->assertEquals(1, $this->decoder->decode('1'));
    }

    /**
     * Test number decoding
     *
     */
    #[@test]
    public function decodeNegativeIntNumber() {
      $this->assertEquals(-1, $this->decoder->decode('-1'));
    }

    /**
     * Test number decoding (LONG_MAX)
     *
     */
    #[@test]
    public function decodeLongMax() {
      $this->assertEquals(LONG_MAX, $this->decoder->decode((string)LONG_MAX));
    }

    /**
     * Test number decoding (LONG_MIN)
     *
     */
    #[@test]
    public function decodeLongMin() {
      $this->assertEquals(LONG_MIN, $this->decoder->decode((string)LONG_MIN));
    }

    /**
     * Test number decoding
     *
     */
    #[@test]
    public function decodeLargeNumber() {
      $this->assertEquals(
        (float)'9999999999999999999999999999999999999',
        $this->decoder->decode('9999999999999999999999999999999999999')
      );
    }

    /**
     * Test number decoding
     *
     */
    #[@test]
    public function decodeLargeNegativeNumber() {
      $this->assertEquals(
        (float)'-9999999999999999999999999999999999999',
        $this->decoder->decode('-9999999999999999999999999999999999999')
      );
    }

    /**
     * Test number decoding
     *
     */
    #[@test]
    public function decodeFloatNumber() {
      $this->assertEquals(1.1, $this->decoder->decode('1.1'));
    }
    
    /**
     * Test number decoding
     *
     */
    #[@test]
    public function decodeNegativeFloatNumber() {
      $this->assertEquals(-1.1, $this->decoder->decode('-1.1'));
    }

    /**
     * Test number deocding
     *
     */
    #[@test]
    public function decodeSmallFloatNumber() {
      $this->assertEquals(0.0000000001, $this->decoder->decode('0.0000000001'));
    }
    
    /**
     * Test number deocding
     *
     */
    #[@test]
    public function decodeBigFloatNumber() {
      $this->assertEquals(10000000.1, $this->decoder->decode('10000000.1'));
    }

       /**
     * Test number decoding
     *
     */
    #[@test]
    public function decodeFloatNumberWithExponent() {
      $this->assertEquals(10.0, $this->decoder->decode('1E1'));
    }
    
    /**
     * Test number decoding
     *
     */
    #[@test]
    public function decodeNegativeFloatNumberWithExponent() {
      $this->assertEquals(-10.0, $this->decoder->decode('-1E1'));
    }

    /**
     * Test number decoding
     *
     */
    #[@test]
    public function decodeFloatNumberWithNegativeExponent() {
      $this->assertEquals(0.1, $this->decoder->decode('1E-1'));
    }
    
    /**
     * Test number decoding
     *
     */
    #[@test]
    public function decodeNegativeFloatNumberWithNegativeExponent() {
      $this->assertEquals(-0.1, $this->decoder->decode('-1E-1'));
    }

   /**
     * Test number decoding
     *
     */
    #[@test]
    public function decodeFloatNumberWithPositiveExponent() {
      $this->assertEquals(10.0, $this->decoder->decode('1E+1'));
    }
    
    /**
     * Test number decoding
     *
     */
    #[@test]
    public function decodeNegativeFloatNumberWithPositiveExponent() {
      $this->assertEquals(-10.0, $this->decoder->decode('-1E+1'));
    }

   /**
     * Test number decoding
     *
     */
    #[@test]
    public function decodeFloatNumberWithExponente() {
      $this->assertEquals(10.0, $this->decoder->decode('1e1'));
    }
    
    /**
     * Test number decoding
     *
     */
    #[@test]
    public function decodeNegativeFloatNumberWithExponente() {
      $this->assertEquals(-10000000000.0, $this->decoder->decode('-1e10'));
    }

    /**
     * Test NULL decoding
     *
     */
    #[@test]
    public function decodeNull() {
      $this->assertEquals(NULL, $this->decoder->decode('null'));
    }

    /**
     * Test TRUE decoding
     *
     */
    #[@test]
    public function decodeTrue() {
      $this->assertEquals(TRUE, $this->decoder->decode('true'));
    }

    /**
     * Test FALSE decoding
     *
     */
    #[@test]
    public function decodeFalse() {
      $this->assertEquals(FALSE, $this->decoder->decode('false'));
    }
    
    /**
     * Test empty array decoding
     *
     */
    #[@test]
    public function decodeEmptyArray() {
      $this->assertEquals(array(), $this->decoder->decode('[ ]'));
    }


    /**
     * Test simple numeric array decoding
     *
     */
    #[@test]
    public function decodeSimpleNumericArray() {
      $this->assertEquals(
        array(1, 2, 3),
        $this->decoder->decode('[ 1 , 2 , 3 ]')
      );
    }

    /**
     * Test string array decoding
     *
     */
    #[@test]
    public function decodeStringArray() {
      $this->assertEquals(
        array('foo', 'bar'),
        $this->decoder->decode('[ "foo" , "bar" ]')
      );
    }

    /**
     * Test array decoding
     *
     */
    #[@test]
    public function decodeArray() {
      $this->assertEquals(
        array(TRUE, FALSE, NULL),
        $this->decoder->decode('[ true , false, null ]')
      );
    }

    /**
     * Test simple mixed array decoding
     *
     */
    #[@test]
    public function decodeSimpleMixedArray() {
      $this->assertEquals(
        array('foo', 2, 'bar'),
        $this->decoder->decode('[ "foo" , 2 , "bar" ]')
      );
    }

    /**
     * Test normal mixed array decoding
     *
     */
    #[@test]
    public function decodeNormalMixedArray() {
      $this->assertEquals(
        array('foo', 0.001, FALSE, array(1, 2, 3)),
        $this->decoder->decode('[ "foo" , 0.001 , false , [ 1 , 2 , 3 ] ]')
      );
    }
    
    /**
     * Test object decoding
     *
     */
    #[@test]
    public function decodeHashmap() {
      $this->assertEquals(
        array('foo' => 'bar', 'bar' => 'baz'),
        $this->decoder->decode('{ "foo" : "bar", "bar" : "baz" }')
      );
    }
    
    /**
     * Test object array decoding
     *
     */
    #[@test]
    public function decodeObjectArray() {
      $this->assertEquals(
        array(array('foo' => 1), array('bar' => 'baz')),
        $this->decoder->decode('[ { "foo" : 1 } , { "bar" : "baz" } ]')
      );
    }

    /**
     * Test complex mixed array decoding
     *
     */
    #[@test]
    public function decodeComplexMixedArray() {
      $this->assertEquals(
        array('foo', TRUE, array('foo' => 'bar', 'bar' => 2)),
        $this->decoder->decode('[ "foo" , true , { "foo" : "bar" , "bar" : 2 } ]')
      );
    }

    /**
     * Test nested object decoding
     *
     */
    #[@test]
    public function decodeNestedObject() {
      $this->assertEquals(
        array('ref' => array('foo' => 'bar')),
        $this->decoder->decode('{ "ref" : { "foo" : "bar" } }')
      );
    }

    /**
     * Test complex hashmap decoding
     *
     */
    #[@test]
    public function decodeComplexHashmap() {
      $this->assertEquals(
        array('foo' => 'bar',
          3 => 0.123,
          FALSE,
          "array" => array(1, "foo", FALSE),
          "array2" => array("foo" => TRUE, "bar" => 4),
          "array3" => array("foo" => array("foo" => "bar"))
        ),
        $this->decoder->decode(
          '{ "foo" : "bar" , "3" : 0.123 , "4" : false , "array" : [ 1 , "foo" , false ] , '.
          '"array2" : { "foo" : true , "bar" : 4 } , "array3" : { "foo" : { "foo" : "bar" } } }'
        )
      );
    }

    /**
     * Test decoding of object
     *
     */
    #[@test]
    public function decodeObject() {
      $o= newinstance('lang.Object', array(), '{
        public $prop= 1;
        
        public function equals($cmp) {
          return $cmp instanceof self && $cmp->prop == $this->prop;
        }
      }');

      $this->assertEquals(
        $o,
        $this->decoder->decode('{ "__jsonclass__" : [ "__construct()" ] , "__xpclass__" : "'.utf8_encode($o->getClassName()).'" , "prop" : 1 }')
      );
    }

    /**
     * Test decoding of array in objects
     *
     */
    #[@test]
    public function decodeMoreObjects() {
      $oa= newinstance('lang.Object', array(), '{
        public $prop= 1;
        
        public function equals($cmp) {
          return $cmp instanceof self && $cmp->prop == $this->prop;
        }
      }');

      $ob= newinstance('lang.Object', array(), '{
        public $prop= 1;
        
        public function equals($cmp) {
          return $cmp instanceof self && $cmp->prop == $this->prop;
        }
      }');

      $oc= newinstance('lang.Object', array(), '{
        public $prop= 1;
        
        public function equals($cmp) {
          return $cmp instanceof self && $cmp->prop == $this->prop;
        }
      }');

      $this->assertEquals(
        array("oa" => $oa, "ob" => $ob, "oc" => $oc),
        $this->decoder->decode(
          '{ "oa" : { "__jsonclass__" : [ "__construct()" ] , "__xpclass__" : "'.utf8_encode($oa->getClassName()).'" , "prop" : 1 }, '.
          '"ob" : { "__jsonclass__" : [ "__construct()" ] , "__xpclass__" : "'.utf8_encode($ob->getClassName()).'" , "prop" : 1 }, '.
          '"oc" : { "__jsonclass__" : [ "__construct()" ] , "__xpclass__" : "'.utf8_encode($oc->getClassName()).'" , "prop" : 1 } }'
        )
      );
    }
    
    /**
     * Test date decoding
     *
     */
    #[@test]
    public function decodeDate() {
      $this->assertEquals(
        new Date('2009-05-18 01:02:03'),
        $this->decoder->decode('{ "__jsonclass__" : [ "__construct()" ] , "__xpclass__" : "util.Date" , "constructor" : "__construct()" , "value" : "2009-05-18 01:02:03+0200" , "__id" : null }')
      );
    }

   /**
    * Test exception
    *
    */
   #[@test]
    public function decodeInvalidNumber1() {
      try {
        $this->decoder->decode('0.00.1');
      } catch (JsonException $je) {
        // Do nothing here
      }

      $this->assertInstanceOf(
        'webservices.json.JsonException',
        $je
      );
    }

   /**
    * Test exception
    *
    */
   #[@test]
    public function decodeInvalidNumber2() {
      try {
        $this->decoder->decode('010');
      } catch (JsonException $je) {
        // Do nothing here
      }

      $this->assertInstanceOf(
        'webservices.json.JsonException',
        $je
      );
    }

   /**
    * Test exception
    *
    */
   #[@test]
    public function decodeInvalidNumber3() {
      try {
        $this->decoder->decode('0-10');
      } catch (JsonException $je) {
        // Do nothing here
      }

      $this->assertInstanceOf(
        'webservices.json.JsonException',
        $je
      );
    }

   /**
    * Test exception
    *
    */
   #[@test]
    public function decodeInvalidString1() {
      try {
        $this->decoder->decode('"foo');
      } catch (JsonException $je) {
        // Do nothing here
      }

      $this->assertInstanceOf(
        'webservices.json.JsonException',
        $je
      );
    }

   /**
    * Test exception
    *
    */
   #[@test]
    public function decodeInvalidString2() {
      try {
        $this->decoder->decode('foo"');
      } catch (JsonException $je) {
        // Do nothing here
      }

      $this->assertInstanceOf(
        'webservices.json.JsonException',
        $je
      );
    }

   /**
    * Test exception
    *
    */
   #[@test]
    public function decodeInvalidString3() {
      try {
        $this->decoder->decode('"foo"bar"');
      } catch (JsonException $je) {
        // Do nothing here
      }

      $this->assertInstanceOf(
        'webservices.json.JsonException',
        $je
      );
    }

   /**
    * Test exception
    *
    */
   #[@test]
    public function decodeInvalidString4() {
      try {
        $this->decoder->decode('foo');
      } catch (JsonException $je) {
        // Do nothing here
      }

      $this->assertInstanceOf(
        'webservices.json.JsonException',
        $je
      );
    }

   /**
    * Test exception
    *
    */
   #[@test]
    public function decodeInvalidArray1() {
      try {
        $this->decoder->decode('1 , 2 , 3');
      } catch (JsonException $je) {
        // Do nothing here
      }

      $this->assertInstanceOf(
        'webservices.json.JsonException',
        $je
      );
    }


   /**
    * Test exception
    *
    */
   #[@test]
    public function decodeInvalidArray2() {
      try {
        $this->decoder->decode('[ 1 2 3 ]');
      } catch (JsonException $je) {
        // Do nothing here
      }

      $this->assertInstanceOf(
        'webservices.json.JsonException',
        $je
      );
    }

   /**
    * Test exception
    *
    */
   #[@test]
    public function decodeInvalidArray3() {
      try {
        $this->decoder->decode('[ 1 , 2 , 3');
      } catch (JsonException $je) {
        // Do nothing here
      }

      $this->assertInstanceOf(
        'webservices.json.JsonException',
        $je
      );
    }

   /**
    * Test exception
    *
    */
   #[@test]
     public function decodeInvalidArray4() {
      try {
        $this->decoder->decode('1 , 2 , 3 ]');
      } catch (JsonException $je) {
        // Do nothing here
      }

      $this->assertInstanceOf(
        'webservices.json.JsonException',
        $je
      );
    }

   /**
    * Test exception
    *
    */
   #[@test]
    public function decodeInvalidObject1() {
      try {
        $this->decoder->decode('{ "foo" "bar" }');
      } catch (JsonException $je) {
        // Do nothing here
      }

      $this->assertInstanceOf(
        'webservices.json.JsonException',
        $je
      );
    }

   /**
    * Test exception
    *
    */
   #[@test]
    public function decodeInvalidObject2() {
      try {
        $this->decoder->decode('{ 1 : "bar" }');
      } catch (JsonException $je) {
        // Do nothing here
      }

      $this->assertInstanceOf(
        'webservices.json.JsonException',
        $je
      );
    }

   /**
    * Test exception
    *
    */
   #[@test]
    public function decodeInvalidObject3() {
      try {
        $this->decoder->decode('{ foo : "bar" }');
      } catch (JsonException $je) {
        // Do nothing here
      }

      $this->assertInstanceOf(
        'webservices.json.JsonException',
        $je
      );
    }

   /**
    * Test exception
    *
    */
   #[@test]
    public function decodeInvalidObject4() {
      try {
        $this->decoder->decode('{ "foo" : bar }');
      } catch (JsonException $je) {
        // Do nothing here
      }

      $this->assertInstanceOf(
        'webservices.json.JsonException',
        $je
      );
    }

   /**
    * Test exception
    *
    */
   #[@test]
    public function decodeInvalidObject5() {
      try {
        $this->decoder->decode('"foo" : "bar"');
      } catch (JsonException $je) {
        // Do nothing here
      }

      $this->assertInstanceOf(
        'webservices.json.JsonException',
        $je
      );
    }

   /**
    * Test exception
    *
    */
   #[@test]
    public function decodeInvalidObject6() {
      try {
        $this->decoder->decode('"foo" : "bar" }');
      } catch (JsonException $je) {
        // Do nothing here
      }

      $this->assertInstanceOf(
        'webservices.json.JsonException',
        $je
      );
    }

   /**
    * Test exception
    *
    */
   #[@test]
    public function decodeInvalidObject7() {
      try {
        $this->decoder->decode('{ "foo" : "bar"');
      } catch (JsonException $je) {
        // Do nothing here
      }

      $this->assertInstanceOf(
        'webservices.json.JsonException',
        $je
      );
    }

   /**
    * Test exception
    *
    */
   #[@test]
    public function decodeInvalidObject8() {
      try {
        $this->decoder->decode('{ "foo" : "bar" "bar" : "foo" }');
      } catch (JsonException $je) {
        // Do nothing here
      }

      $this->assertInstanceOf(
        'webservices.json.JsonException',
        $je
      );
    }

   /**
    * Test exception
    *
    */
   #[@test]
    public function decodeInvalidXPObject() {
      try {
        $this->decoder->decode('{ "__jsonclass__" : [ "__construct()" ] , "__xpclass__" : "Foo" , "constructor" : "__construct()" , "value" : "2009-05-18 01:02:03+0200" , "__id" : null }');
      } catch (JsonException $je) {
        // Do nothing here
      }

      $this->assertInstanceOf(
        'webservices.json.JsonException',
        $je
      );
    }

    /**
     * Decoding non-json data should result in an exception
     *
     */
    #[@test,@expect('webservices.json.JsonException')]
    public function decodeInvalidData() {
      $this->decoder->decode('<xml version="1.0" encoding="iso-8859-1"?><document/>');
    }
    
    /**
     *  Additional tests
     */


    /**
     * Test invalid string
     *
     */
    #[@test]
    public function decodeInvalidString5() {
      try {
        $this->decoder->decode('"foobar\"');
      } catch (JsonException $je) {
        // Do nothing here
      }

      $this->assertInstanceOf(
        'webservices.json.JsonException',
        $je
      );
    }

    /**
     * Test invalid string
     *
     */
    #[@test]
    public function decodeInvalidString6() {
      try {
        $this->decoder->decode('"foo\u20A"');
      } catch (JsonException $je) {
        // Do nothing here
      }

      $this->assertInstanceOf(
        'webservices.json.JsonException',
        $je
      );
    }

    /**
     * Test invalid string
     *
     */
    #[@test]
    public function decodeInvalidString7() {
      try {
        $this->decoder->decode('"foo\ufoobar"');
      } catch (JsonException $je) {
        // Do nothing here
      }

      $this->assertInstanceOf(
        'webservices.json.JsonException',
        $je
      );
    }

    /**
     * Test string with numbers
     *
     */
    #[@test]
    public function decodeStringWithNumbers() {
      $this->assertEquals(
        'foo'."\n".'200',
        $this->decoder->decode('"foo\n200"')
      );
    }

    /**
     * Test string with json syntax
     *
     */
    #[@test]
    public function decodeStringWithJsonSyntax() {
      $this->assertEquals(
        '[foo, bar]',
        $this->decoder->decode('"[foo, bar]"')
      );
    }

    /**
     * Test invalid string
     *
     */
    #[@test]
    public function decodeInvalidString8() {
      try {
        $this->decoder->decode('"foo\obar"');
      } catch (JsonException $je) {
        // Do nothing here
      }

      $this->assertInstanceOf(
        'webservices.json.JsonException',
        $je
      );
    }

    /**
     * Test object with two identical keys
     *
     */
    #[@test]
    public function decodeObjectWithIdenticalKeys() {
      $this->assertEquals(
        array('foo' => 'bar'),
        $this->decoder->decode('{ "foo" : "bar", "foo" : "bar2" }')
      );
    }

    /**
     * Test zero
     *
     */
    #[@test]
    public function decodeZero() {
      $this->assertEquals(
        0,
        $this->decoder->decode('0')
      );
    }

    /**
     * Test empty string
     *
     */
    #[@test]
    public function decodeEmptyString() {
      $this->assertEquals(
        "",
        $this->decoder->decode('""')
      );
    }

    /**
     * Test nothing
     *
     */
    #[@test]
    public function decodeNothing() {
      try {
        $this->decoder->decode('');
      } catch (JsonException $je) {
        // Do nothing here
      }

      $this->assertInstanceOf(
        'webservices.json.JsonException',
        $je
      );
    }

  }
?>
