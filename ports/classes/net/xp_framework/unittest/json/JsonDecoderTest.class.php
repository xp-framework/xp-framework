<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'util.Date',
    'lang.types.Character',
    'webservices.json.JsonDecoder'
  );

  /**
   * Testcase for JsonDecoder
   *
   * @see      http://json.org
   * @purpose  Testcase
   */
  class JsonDecoderTest extends TestCase {
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
    public function encodeStringWithControlChars() {
      $this->assertEquals('"fo\\no"', $this->decoder->encode('fo'."\n".'o'));
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
     * Test string encoding
     *
     */
    #[@test]
    public function encodeStringInstance() {
      $this->assertEquals('"foo"', $this->decoder->encode(new String('foo')));
    }

    /**
     * Test string encoding
     *
     */
    #[@test]
    public function encodeStringInstanceWithControlChars() {
      $this->assertEquals('"fo\\no"', $this->decoder->encode(new String('fo'."\n".'o')));
    }

    /**
     * Test string encoding
     *
     */
    #[@test]
    public function encodeUTF8StringInstance() {
      $this->assertEquals('"fÃ¶o"', $this->decoder->encode(new String('föo', 'iso-8859-1')));
    }

    /**
     * Test string encoding
     *
     */
    #[@test]
    public function encodeCharacterInstance() {
      $this->assertEquals('"f"', $this->decoder->encode(new Character('f')));
    }

    /**
     * Test string encoding
     *
     */
    #[@test]
    public function encodeUTF8CharacterInstance() {
      $this->assertEquals('"Ã¶"', $this->decoder->encode(new Character('ö', 'iso-8859-1')));
    }
  
    /**
     * Test integer encoding
     *
     */
    #[@test]
    public function encodeInt() {
      $this->assertEquals('1', $this->decoder->encode(1));
      $this->assertEquals('-1', $this->decoder->encode(-1));
    }
    
    /**
     * Test float encoding
     *
     */
    #[@test]
    public function encodeFloat() {
      $this->assertEquals('1', $this->decoder->encode(1.0));    
      $this->assertEquals('1.1', $this->decoder->encode(1.1));
    }
    
    /**
     * Test boolean and NULL encoding
     *
     */
    #[@test]
    public function encodeBooleanAndNull() {
      $this->assertEquals('true', $this->decoder->encode(TRUE));
      $this->assertEquals('false', $this->decoder->encode(FALSE));
      $this->assertEquals('null', $this->decoder->encode(NULL));
    }
    
    /**
     * Test string encoding
     *
     */
    #[@test]
    public function encodeArray() {
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
     */
    #[@test]
    public function encodeHashmap() {
      $this->assertEquals(
        '{ "foo" : "bar" , "bar" : "baz" }',
        $this->decoder->encode(array('foo' => 'bar', 'bar' => 'baz'))
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
     * Test string decoding
     *
     */
    #[@test]
    public function decodeString() {
      $this->assertEquals(
        new String('foobar'),
        $this->decoder->decode('"foobar"')
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
        $this->decoder->decode('"foo\\\\bar"')
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
        $this->decoder->decode('"foo\\"bar"')
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
        $this->decoder->decode('"foobar\\\\\""')
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
        new String('foobar'."\t".'\"'),
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
        new String("\nbbb ".'<span style="font-weight: bold;">tes</span>t " test'."\n"),
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
        $this->assertEquals(6100, $this->decoder->decode('"'.$data.'"')->length(), 'Decoded length mismatch');
      }
    }
    
    /**
     * Test string decoding
     *
     */
    #[@test]
    public function decodeUTF8String() {
      $this->assertEquals(new String('Knüper', 'iso-8859-1'), $this->decoder->decode('"KnÃ¼per"'));
    }
    
    
    /**
     * Test string decoding
     *
     */
    #[@test]
    public function decodeUTF8StringWithUnicodeCodepoint() {
      $this->assertEquals(new String('Günther', 'iso-8859-1'), $this->decoder->decode('"G\u00fcnther"'));
      $this->assertEquals(new String('¤uro', 'iso-8859-15'), $this->decoder->decode('"\u20ACuro"'));
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
    public function decodeIntNumber() {
      $this->assertEquals(1, $this->decoder->decode('1'));
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
     * Test number decoding
     *
     */
    #[@test]
    public function decodeFloatNumberWithExponent() {
      $this->assertEquals(0.1, $this->decoder->decode('1E-1'));
    }
    
    /**
     * Test number decoding
     *
     */
    #[@test]
    public function decodeNegativeFloatNumberWithExponent() {
      $this->assertEquals(-0.1, $this->decoder->decode('-1E-1'));
    }
    
    /**
     * Test string array decoding
     *
     */
    #[@test]
    public function decodeStringArray() {
      $this->assertEquals(
        array(new String('foo'), new String('bar')),
        $this->decoder->decode('[ "foo" , "bar" ]')
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
        array(array('foo' => 1), array('bar' => new String('baz'))),
        $this->decoder->decode('[ { "foo" : 1 } , { "bar" : "baz" } ]')
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
        $this->decoder->decode('{ "ref" : { "foo" : "bar" } }')
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
  }
?>
