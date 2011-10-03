<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'util.Date',
    'lang.types.String',
    'webservices.json.JsonDecoder'
  );

  /**
   * Testcase for JsonDecoder
   *
   * @see   xp://webservices.json.JsonDecoder
   */
  class JsonDecodingTest extends TestCase {
    protected $fixture= NULL;
    protected $tz= NULL;
        
    /**
     * Setup text fixture
     *
     */
    public function setUp() {
      $this->fixture= new JsonDecoder();
      $this->tz= date_default_timezone_get();
      date_default_timezone_set('Europe/Berlin');
    }
    
    /**
     * Tear down test.
     *
     */
    public function tearDown() {
      date_default_timezone_set($this->tz);
    }
    
    /**
     * Returns decoded input
     *
     * @param   string input
     * @return  var
     */
    protected function decode($input) {
      return $this->fixture->decode($input);
    }
    
    /**
     * Test string decoding
     *
     */
    #[@test]
    public function decodeString() {
      $this->assertEquals(
        new String('foobar'),
        $this->decode('"foobar"')
      );
    }

    /**
     * Test string decoding
     *
     */
    #[@test]
    public function decodeStringWithQuotedDelimiter() {
      $this->assertEquals(
        new String('foo"bar'),
        $this->decode('"foo\\"bar"')
      );
    }

    /**
     * Test string decoding
     *
     */
    #[@test]
    public function decodeStringWithQuotes() {
      $this->assertEquals(
        new String('foo\\bar'),
        $this->decode('"foo\\\\bar"')
      );
    }

    /**
     * Tests string decoding
     *
     */
    #[@test]
    public function decodeStringWithSolidus() {
      $this->assertEquals(
        new String('foo/bar'),
        $this->decode('"foo\\/bar"')
      );
    }

    /**
     * Test string decoding
     *
     */
    #[@test]
    public function decodeStringWithQuotedQuotes() {
      $this->assertEquals(
        new String('foobar\"'),
        $this->decode('"foobar\\\\\""')
      );
    }

    /**
     * Tests string decoding
     *
     */
    #[@test]
    public function decodeStringWithBackspace() {
      $this->assertEquals(
        new String('fo'."\b".'o'),
        $this->decode('"fo\\bo"')
      );
    }

    /**
     * Tests string decoding
     *
     */
    #[@test]
    public function decodeStringWithFormfeed() {
      $this->assertEquals(
        new String('fo'."\f".'o'),
        $this->decode('"fo\\fo"')
      );
    }

     /**
     * Tests string decoding
     *
     */
    #[@test]
    public function decodeStringWithNewline() {
       $this->assertEquals(
         new String('fo'."\n".'o'),
         $this->decode('"fo\\no"')
       );
    }

     /**
     * Tests string decoding
     *
     */
    #[@test]
    public function decodeStringWithCarriageReturn() {
      $this->assertEquals(
        new String('fo'."\r".'o'),
        $this->decode('"fo\\ro"')
      );
    }

    /**
     * Test string decoding
     *
     */
    #[@test]
    public function decodeStringWithTabEscape() {
      $this->assertEquals(
        new String("foobar\t"),
        $this->decode('"foobar\\t"')
      );
    }

    /**
     * Test string decoding
     *
     */
    #[@test]
    public function decodeStringWithTabsAndQuotes() {
      $this->assertEquals(
        new String('foobar'."\t".'\"'),
        $this->decode('"foobar\\t\\\\\""')
      );
    }

    /**
     * Test string decoding - Real life example
     *
     */
    #[@test]
    public function decodeStringWithHTML() {
      $this->assertEquals(
        new String("\nbbb ".'<span style="font-weight: bold;">tes</span>t " test'."\n"),
        $this->decode('"\nbbb <span style=\"font-weight: bold;\">tes</span>t \" test\n"')
      );
    }

    /**
     * Test string decoding
     *
     */
    #[@test]
    public function decodeLongString() {
      with (
        $data= str_repeat('*', 6100),
        $decodedString= $this->decode('"'.$data.'"')
      ); {
        $this->assertEquals(
          strlen($data),
          $decodedString->length(),
          'Decoded length mismatch'
        );
      }
    }
    
    /**
     * Test string decoding
     *
     */
    #[@test]
    public function decodeUTF8String() {
      $this->assertEquals(
        new String('Knüper', 'Windows-1252'),
        $this->decode('"KnÃ¼per"')
      );
    }
    
    
    /**
     * Test string decoding
     *
     */
    #[@test]
    public function decodeUTF8StringWithUnicodeCodepoint() {
      $this->assertEquals(
        new String('Günther', 'Windows-1252'),
        $this->decode('"G\u00fcnther"')
      );
      $this->assertEquals(
        new String('¤uro'),
        $this->decode('"\u20ACuro"')
      );
    }

    /**
     * Test number decoding
     *
     */
    #[@test]
    public function decodeIntNumber() {
      $this->assertEquals(1, $this->decode('1'));
    }

    /**
     * Test number decoding
     *
     */
    #[@test]
    public function decodeNegativeIntNumber() {
      $this->assertEquals(-1, $this->decode('-1'));
    }

    /**
     * Test number decoding (LONG_MAX)
     *
     */
    #[@test]
    public function decodeLongMax() {
      $this->assertEquals(LONG_MAX, $this->decode((string)LONG_MAX));
    }

    /**
     * Test number decoding (LONG_MIN)
     *
     */
    #[@test]
    public function decodeLongMin() {
      $this->assertEquals(LONG_MIN, $this->decode((string)LONG_MIN));
    }

    /**
     * Test number decoding
     *
     */
    #[@test]
    public function decodeLargeNumber() {
      $this->assertEquals(
        (float)'9999999999999999999999999999999999999',
        $this->decode('9999999999999999999999999999999999999')
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
        $this->decode('-9999999999999999999999999999999999999')
      );
    }

    /**
     * Test number decoding
     *
     */
    #[@test]
    public function decodeFloatNumber() {
      $this->assertEquals(1.1, $this->decode('1.1'));
    }
    
    /**
     * Test number decoding
     *
     */
    #[@test]
    public function decodeNegativeFloatNumber() {
      $this->assertEquals(-1.1, $this->decode('-1.1'));
    }

    /**
     * Test number deocding
     *
     */
    #[@test]
    public function decodeSmallFloatNumber() {
      $this->assertEquals(0.0000000001, $this->decode('0.0000000001'));
    }
    
    /**
     * Test number deocding
     *
     */
    #[@test]
    public function decodeBigFloatNumber() {
      $this->assertEquals(10000000.1, $this->decode('10000000.1'));
    }

       /**
     * Test number decoding
     *
     */
    #[@test]
    public function decodeFloatNumberWithExponent() {
      $this->assertEquals(10.0, $this->decode('1E1'));
    }
    
    /**
     * Test number decoding
     *
     */
    #[@test]
    public function decodeNegativeFloatNumberWithExponent() {
      $this->assertEquals(-10.0, $this->decode('-1E1'));
    }

    /**
     * Test number decoding
     *
     */
    #[@test]
    public function decodeFloatNumberWithNegativeExponent() {
      $this->assertEquals(0.1, $this->decode('1E-1'));
    }
    
    /**
     * Test number decoding
     *
     */
    #[@test]
    public function decodeNegativeFloatNumberWithNegativeExponent() {
      $this->assertEquals(-0.1, $this->decode('-1E-1'));
    }

   /**
     * Test number decoding
     *
     */
    #[@test]
    public function decodeFloatNumberWithPositiveExponent() {
      $this->assertEquals(10.0, $this->decode('1E+1'));
    }
    
    /**
     * Test number decoding
     *
     */
    #[@test]
    public function decodeNegativeFloatNumberWithPositiveExponent() {
      $this->assertEquals(-10.0, $this->decode('-1E+1'));
    }

   /**
     * Test number decoding
     *
     */
    #[@test]
    public function decodeFloatNumberWithExponente() {
      $this->assertEquals(10.0, $this->decode('1e1'));
    }
    
    /**
     * Test number decoding
     *
     */
    #[@test]
    public function decodeNegativeFloatNumberWithExponente() {
      $this->assertEquals(-10000000000.0, $this->decode('-1e10'));
    }

    /**
     * Test NULL decoding
     *
     */
    #[@test]
    public function decodeNull() {
      $this->assertEquals(NULL, $this->decode('null'));
    }

    /**
     * Test TRUE decoding
     *
     */
    #[@test]
    public function decodeTrue() {
      $this->assertEquals(TRUE, $this->decode('true'));
    }

    /**
     * Test FALSE decoding
     *
     */
    #[@test]
    public function decodeFalse() {
      $this->assertEquals(FALSE, $this->decode('false'));
    }
    
    /**
     * Test empty array decoding
     *
     */
    #[@test]
    public function decodeEmptyArray() {
      $this->assertEquals(array(), $this->decode('[ ]'));
    }


    /**
     * Test simple numeric array decoding
     *
     */
    #[@test]
    public function decodeSimpleNumericArray() {
      $this->assertEquals(
        array(1, 2, 3),
        $this->decode('[ 1 , 2 , 3 ]')
      );
    }

    /**
     * Test string array decoding
     *
     */
    #[@test]
    public function decodeStringArray() {
      $this->assertEquals(
        array(new String('foo'), new String('bar')),
        $this->decode('[ "foo" , "bar" ]')
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
        $this->decode('[ true , false, null ]')
      );
    }

    /**
     * Test simple mixed array decoding
     *
     */
    #[@test]
    public function decodeSimpleMixedArray() {
      $this->assertEquals(
        array(new String('foo'), 2, new String('bar')),
        $this->decode('[ "foo" , 2 , "bar" ]')
      );
    }

    /**
     * Test normal mixed array decoding
     *
     */
    #[@test]
    public function decodeNormalMixedArray() {
      $this->assertEquals(
        array(new String('foo'), 0.001, FALSE, array(1, 2, 3)),
        $this->decode('[ "foo" , 0.001 , false , [ 1 , 2 , 3 ] ]')
      );
    }
    
    /**
     * Test object decoding
     *
     */
    #[@test]
    public function decodeHashmap() {
      $this->assertEquals(
        array('foo' => new String('bar'), 'bar' => new String('baz')),
        $this->decode('{ "foo" : "bar", "bar" : "baz" }')
      );
    }
    
    /**
     * Test object array decoding
     *
     */
    #[@test]
    public function decodeObjectArray() {
      $this->assertEquals(
        array(array('foo' => 1), array('bar' => new String('baz'))),
        $this->decode('[ { "foo" : 1 } , { "bar" : "baz" } ]')
      );
    }

    /**
     * Test complex mixed array decoding
     *
     */
    #[@test]
    public function decodeComplexMixedArray() {
      $this->assertEquals(
        array(new String('foo'), TRUE, array('foo' => new String('bar'), 'bar' => 2)),
        $this->decode('[ "foo" , true , { "foo" : "bar" , "bar" : 2 } ]')
      );
    }

    /**
     * Test nested object decoding
     *
     */
    #[@test]
    public function decodeNestedObject() {
      $this->assertEquals(
        array('ref' => array('foo' => new String('bar'))),
        $this->decode('{ "ref" : { "foo" : "bar" } }')
      );
    }

    /**
     * Test complex hashmap decoding
     *
     */
    #[@test]
    public function decodeComplexHashmap() {
      $this->assertEquals(
        array('foo' => new String('bar'),
          3 => 0.123,
          FALSE,
          "array" => array(1, new String("foo"), FALSE),
          "array2" => array("foo" => TRUE, "bar" => 4),
          "array3" => array("foo" => array("foo" => new String("bar")))
        ),
        $this->decode(
          '{ "foo" : "bar" , "3" : 0.123 , "4" : false , "array" : [ 1 , "foo" , false ] , '.
          '"array2" : { "foo" : true , "bar" : 4 } , "array3" : { "foo" : { "foo" : "bar" } } }'
        )
      );
    }

   /**
    * Test exception
    *
    */
   #[@test]
    public function decodeInvalidNumber1() {
      try {
        $this->decode('0.00.1');
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
        $this->decode('010');
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
        $this->decode('0-10');
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
        $this->decode('"foo');
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
        $this->decode('foo"');
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
        $this->decode('"foo"bar"');
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
        $this->decode('foo');
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
        $this->decode('1 , 2 , 3');
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
        $this->decode('[ 1 2 3 ]');
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
        $this->decode('[ 1 , 2 , 3');
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
        $this->decode('1 , 2 , 3 ]');
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
        $this->decode('{ "foo" "bar" }');
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
        $this->decode('{ 1 : "bar" }');
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
        $this->decode('{ foo : "bar" }');
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
        $this->decode('{ "foo" : bar }');
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
        $this->decode('"foo" : "bar"');
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
        $this->decode('"foo" : "bar" }');
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
        $this->decode('{ "foo" : "bar"');
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
        $this->decode('{ "foo" : "bar" "bar" : "foo" }');
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
      $this->decode('<xml version="1.0" encoding="iso-8859-1"?><document/>');
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
        $this->decode('"foobar\"');
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
        $this->decode('"foo\u20A"');
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
        $this->decode('"foo\ufoobar"');
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
        new String('foo'."\n".'200'),
        $this->decode('"foo\n200"')
      );
    }

    /**
     * Test string with json syntax
     *
     */
    #[@test]
    public function decodeStringWithJsonSyntax() {
      $this->assertEquals(
        new String('[foo, bar]'),
        $this->decode('"[foo, bar]"')
      );
    }

    /**
     * Test invalid string
     *
     */
    #[@test]
    public function decodeInvalidString8() {
      try {
        $this->decode('"foo\obar"');
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
        array('foo' => new String('bar')),
        $this->decode('{ "foo" : "bar", "foo" : "bar2" }')
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
        $this->decode('0')
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
        $this->decode('""')
      );
    }

    /**
     * Test nothing
     *
     */
    #[@test]
    public function decodeNothing() {
      try {
        $this->decode('');
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
