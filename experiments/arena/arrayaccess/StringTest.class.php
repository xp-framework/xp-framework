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
      $this->assertEquals('Hello', $str->getBytes());
      $this->assertEquals(5, $str->length());
    }

    /**
     * Test a string containing German umlauts
     *
     */
    #[@test]
    public function umlautString() {
      $str= new String('Hällo');
      $this->assertEquals('HÃ¤llo', $str->getBytes());
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
      $this->assertEquals(1, $str->indexOf('ä'));
      $this->assertEquals(1, $str->indexOf(new String('ä')));
      $this->assertEquals(-1, $str->indexOf(''));
      $this->assertEquals(-1, $str->indexOf('4'));
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
     * Test startsWith() method
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
     * Test replace() method
     *
     */
    #[@test]
    public function replace() {
      $str= new String('www.müller.com');
      $this->assertEquals(new String('müller'), $str->replace('www.')->replace('.com'));
      $this->assertEquals(new String('muller'), $str->replace('ü', 'u'));
    }
  }
?>
