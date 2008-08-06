<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'lang.types.String'
  );

  /**
   * TestCase
   *
   * @see      xp://lang.types.String
   * @purpose  Unittest
   */
  class StringTest extends TestCase {

    /**
     * Setup this test. Forces input and output encoding to ISO-8859-1
     *
     */
    public function setUp() {
      iconv_set_encoding('input_encoding', 'ISO-8859-1');
      iconv_set_encoding('output_encoding', 'ISO-8859-1');
    }

    /**
     * Test equals() method
     *
     */
    #[@test]
    public function stringIsEqualToItself() {
      $a= new String('');
      $this->assertTrue($a->equals($a));
    }

    /**
     * Test equals() method
     *
     */
    #[@test]
    public function stringIsEqualSameString() {
      $this->assertTrue(create(new String('ABC'))->equals(new String('ABC')));
    }

    /**
     * Test equals() method
     *
     */
    #[@test]
    public function stringIsNotEqualToDifferentString() {
      $this->assertFalse(create(new String('ABC'))->equals(new String('CBA')));
    }

    /**
     * Test a string with an incomplete multibyte character in it
     *
     */
    #[@test, @expect('lang.FormatException')]
    public function incompleteMultiByteCharacter() {
      new String('ä', 'UTF-8');
    }
  
    /**
     * Test a string with an illegal character in it
     *
     */
    #[@test, @expect('lang.FormatException')]
    public function illegalCharacter() {
      new String('ä', 'US-ASCII');
    }

    /**
     * Test
     *
     */
    #[@test]
    public function usAsciiString() {
      $str= new String('Hello');
      $this->assertEquals(new Bytes('Hello'), $str->getBytes());
      $this->assertEquals(5, $str->length());
    }

    /**
     * Test string invoked with integer number as sole constructor argument
     *
     */
    #[@test]
    public function integerString() {
      $str= new String(1);
      $this->assertEquals(new Bytes('1'), $str->getBytes());
      $this->assertEquals(1, $str->length());
    }

    /**
     * Test string invoked with a Character as sole constructor argument
     *
     */
    #[@test]
    public function characterString() {
      $str= new String(new Character('Ä'));
      $this->assertEquals(new Bytes("\304"), $str->getBytes('iso-8859-1'));
      $this->assertEquals(1, $str->length());
    }

    /**
     * Test string invoked with double number as sole constructor argument
     *
     */
    #[@test]
    public function doubleString() {
      $str= new String(1.1);
      $this->assertEquals(new Bytes('1.1'), $str->getBytes());
      $this->assertEquals(3, $str->length());
    }

    /**
     * Test string invoked with boolean as sole constructor argument
     *
     */
    #[@test]
    public function trueString() {
      $str= new String(TRUE);
      $this->assertEquals(new Bytes('1'), $str->getBytes());
      $this->assertEquals(1, $str->length());
    }

    /**
     * Test string invoked with boolean as sole constructor argument
     *
     */
    #[@test]
    public function falseString() {
      $str= new String(FALSE);
      $this->assertEquals(new Bytes(''), $str->getBytes());
      $this->assertEquals(0, $str->length());
    }

    /**
     * Test string invoked with null as sole constructor argument
     *
     */
    #[@test]
    public function nullString() {
      $str= new String(NULL);
      $this->assertEquals(new Bytes(''), $str->getBytes());
      $this->assertEquals(0, $str->length());
    }

    /**
     * Test a string containing German umlauts
     *
     */
    #[@test]
    public function umlautString() {
      $str= new String('Hällo');
      $this->assertEquals(new Bytes('HÃ¤llo'), $str->getBytes('utf-8'));
      $this->assertEquals(5, $str->length());
    }

    /**
     * Test a string with UTF-8 in it
     *
     */
    #[@test]
    public function utf8String() {
      $this->assertEquals(
        new String('HÃ¤llo', 'UTF-8'),
        new String('Hällo', 'ISO-8859-1')
      );
    }

    /**
     * Test translatiom
     *
     */
    #[@test]
    public function transliteration() {
      $this->assertEquals(
        'Trenciansky kraj', 
        create(new String('TrenÄiansky kraj', 'UTF-8'))->toString()
      );
    }

    /**
     * Test indexOf() method
     *
     */
    #[@test]
    public function indexOf() {
      $str= new String('Hällo');
      $this->assertEquals(0, $str->indexOf('H'));
      $this->assertEquals(1, $str->indexOf('ä'));
      $this->assertEquals(1, $str->indexOf(new String('ä')));
      $this->assertEquals(-1, $str->indexOf(''));
      $this->assertEquals(-1, $str->indexOf('4'));
    }

    /**
     * Test lastIndexOf() method
     *
     */
    #[@test]
    public function lastIndexOf() {
      $str= new String('HälloH');
      $this->assertEquals($str->length()- 1, $str->lastIndexOf('H'));
      $this->assertEquals(1, $str->lastIndexOf('ä'));
      $this->assertEquals(1, $str->lastIndexOf(new String('ä')));
      $this->assertEquals(-1, $str->lastIndexOf(''));
      $this->assertEquals(-1, $str->lastIndexOf('4'));
    }

    /**
     * Test contains() method
     *
     */
    #[@test]
    public function contains() {
      $str= new String('Hällo');
      $this->assertTrue($str->contains('H'));
      $this->assertTrue($str->contains('ä'));
      $this->assertTrue($str->contains('o'));
      $this->assertFalse($str->contains(''));
      $this->assertFalse($str->contains('4'));
    }

    /**
     * Test substring() method
     *
     */
    #[@test]
    public function substring() {
      $str= new String('Hällo');
      $this->assertEquals(new String('ällo'), $str->substring(1));
      $this->assertEquals(new String('ll'), $str->substring(2, -1));
      $this->assertEquals(new String('o'), $str->substring(-1, 1));
    }

    /**
     * Test startsWith() method
     *
     */
    #[@test]
    public function startsWith() {
      $str= new String('www.müller.com');
      $this->assertTrue($str->startsWith('www.'));
      $this->assertFalse($str->startsWith('ww.'));
      $this->assertFalse($str->startsWith('müller'));
    }

    /**
     * Test endsWith() method
     *
     */
    #[@test]
    public function endsWith() {
      $str= new String('www.müller.com');
      $this->assertTrue($str->endsWith('.com'));
      $this->assertTrue($str->endsWith('üller.com'));
      $this->assertFalse($str->endsWith('.co'));
      $this->assertFalse($str->endsWith('müller'));
    }

    /**
     * Test concat() method
     *
     */
    #[@test]
    public function concat() {
      $this->assertEquals(new String('www.müller.com'), create(new String('www'))
        ->concat(new Character('.'))
        ->concat('müller')
        ->concat('.com')
      );
    }
    
    /**
     * Test hashCode() method
     *
     */
    #[@test]
    public function hashesOfSameStringEqual() {
      $this->assertEquals(
        create(new String(''))->hashCode(),
        create(new String(''))->hashCode()
      );
    }

    /**
     * Test hashCode() method
     *
     */
    #[@test]
    public function hashesOfDifferentStringsNotEqual() {
      $this->assertNotEquals(
        create(new String('A'))->hashCode(),
        create(new String('B'))->hashCode()
      );
    }
    
    /**
     * Test charAt() method
     *
     */
    #[@test]
    public function charAt() {
      $this->assertEquals(new Character('ü'), create(new String('www.müller.com'))->charAt(5));
    }

    /**
     * Test charAt() method
     *
     */
    #[@test, @expect('lang.IndexOutOfBoundsException')]
    public function charAtNegative() {
      create(new String('ABC'))->charAt(-1);
    }

    /**
     * Test charAt() method
     *
     */
    #[@test, @expect('lang.IndexOutOfBoundsException')]
    public function charAtAfterEnd() {
      create(new String('ABC'))->charAt(4);
    }


    /**
     * Test charAt() method
     *
     */
    #[@test, @expect('lang.IndexOutOfBoundsException')]
    public function charAtEnd() {
      create(new String('ABC'))->charAt(3);
    }

    /**
     * Test replace() method
     *
     */
    #[@test]
    public function replace() {
      $str= new String('www.müller.com');
      $this->assertEquals(new String('müller'), $str->replace('www.')->replace('.com'));
      $this->assertEquals(new String('muller'), $str->replace('ü', 'u'));
    }

    /**
     * Test []= overloading
     *
     */
    #[@test]
    public function offsetSet() {
      $str= new String('www.müller.com');
      $str[5]= 'u';
      $this->assertEquals(new String('www.muller.com'), $str);
    }

    /**
     * Test []= overloading
     *
     */
    #[@test, @expect('lang.IndexOutOfBoundsException')]
    public function offsetSetNegative() {
      $str= new String('www.müller.com');
      $str[-1]= 'u';
    }

    /**
     * Test []= overloading
     *
     */
    #[@test, @expect('lang.IndexOutOfBoundsException')]
    public function offsetSetAfterEnd() {
      $str= new String('www.müller.com');
      $str[$str->length()]= 'u';
    }

    /**
     * Test []= overloading
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function offsetSetIncorrectLength() {
      $str= new String('www.müller.com');
      $str[5]= 'ue';
    }

    /**
     * Test []= overloading
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function offsetAdd() {
      $str= new String('www.müller.com');
      $str[]= '.';
    }

    /**
     * Test =[] overloading
     *
     */
    #[@test]
    public function offsetGet() {
      $str= new String('www.müller.com');
      $this->assertEquals(new Character('ü'), $str[5]);
    }

    /**
     * Test isset() overloading
     *
     */
    #[@test]
    public function offsetExists() {
      $str= new String('www.müller.com');
      $this->assertTrue(isset($str[0]), 0);
      $this->assertTrue(isset($str[5]), 5);
      $this->assertFalse(isset($str[-1]), -1);
      $this->assertFalse(isset($str[1024]), 1024);
    }

    /**
     * Test unset() overloading
     *
     */
    #[@test]
    public function offsetUnsetAtBeginning() {
      $str= new String('www.müller.com');
      unset($str[0]);
      $this->assertEquals(new String('ww.müller.com'), $str);
    }

    /**
     * Test unset() overloading
     *
     */
    #[@test]
    public function offsetUnsetAtEnd() {
      $str= new String('www.müller.com');
      unset($str[$str->length()- 1]);
      $this->assertEquals(new String('www.müller.co'), $str);
    }

    /**
     * Test unset() overloading
     *
     */
    #[@test]
    public function offsetUnsetInBetween() {
      $str= new String('www.müller.com');
      unset($str[5]);
      $this->assertEquals(new String('www.mller.com'), $str);
    }

    /**
     * Test unset() overloading
     *
     */
    #[@test, @expect('lang.IndexOutOfBoundsException')]
    public function offsetUnsetNegative() {
      $str= new String('www.müller.com');
      unset($str[-1]);
    }

    /**
     * Test unset() overloading
     *
     */
    #[@test, @expect('lang.IndexOutOfBoundsException')]
    public function offsetUnsetAfterEnd() {
      $str= new String('www.müller.com');
      unset($str[1024]);
    }

    /**
     * Test string conversion overloading
     *
     */
    #[@test]
    public function worksWithEchoStatement() {
      ob_start();
      echo new String('www.müller.com');
      $this->assertEquals('www.müller.com', ob_get_clean());
    }

    /**
     * Test string conversion overloading
     *
     */
    #[@test]
    public function stringCast() {
      $this->assertEquals('www.müller.com', (string)new String('www.müller.com'));
    }

    /**
     * Test string conversion overloading
     *
     */
    #[@test]
    public function usedInStringFunction() {
      $this->assertEquals(
        'ftp.müller.com', 
        str_replace('www', 'ftp', new String('www.müller.com')
      ));
    }
  }
?>
