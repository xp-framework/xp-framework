<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'lang.types.Character'
  );

  /**
   * TestCase
   *
   * @see      xp://lang.types.Character
   * @purpose  Unittest
   */
  class CharacterTest extends TestCase {

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
      new Character('ä', 'UTF-8');
    }

    /**
     * Test a string with an incomplete multibyte character in it
     *
     */
    #[@test]
    public function nullByte() {
      $this->assertEquals("\x00", create(new Character(0))->getBytes());
    }

    /**
     * Test a string with an incomplete multibyte character in it
     *
     */
    #[@test]
    public function euroSymbol() {
      $this->assertEquals("\xe2\x82\xac", create(new Character(8364))->getBytes()); // &#8364; in HTML
    }
  
    /**
     * Test a string with an illegal character in it
     *
     */
    #[@test, @expect('lang.FormatException')]
    public function illegalCharacter() {
      new Character('ä', 'US-ASCII');
    }

    /**
     * Test constructor invocation with three characters
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function illegalLength() {
      new Character('ABC');
    }

    /**
     * Test
     *
     */
    #[@test]
    public function usAsciiCharacter() {
      $this->assertEquals('H', create(new Character('H'))->getBytes());
    }

    /**
     * Test a string containing German umlauts
     *
     */
    #[@test]
    public function umlautCharacter() {
      $this->assertEquals('Ã¤', create(new Character('ä'))->getBytes());
    }

    /**
     * Test a string with UTF-8 in it
     *
     */
    #[@test]
    public function utf8Character() {
      $this->assertEquals(
        new Character('Ã¤', 'UTF-8'),
        new Character('ä', 'ISO-8859-1')
      );
    }

    /**
     * Test translatiom
     *
     */
    #[@test]
    public function transliteration() {
      $this->assertEquals('c', create(new String('Ä', 'UTF-8'))->toString());
    }

    /**
     * Test string conversion overloading
     *
     */
    #[@test]
    public function worksWithEchoStatement() {
      ob_start();
      echo new Character('ü');
      $this->assertEquals('ü', ob_get_clean());
    }

    /**
     * Test string conversion overloading
     *
     */
    #[@test]
    public function stringCast() {
      $this->assertEquals('w', (string)new Character('w'));
    }

    /**
     * Test string conversion overloading
     *
     */
    #[@test]
    public function usedInStringFunction() {
      $this->assertEquals(
        'z', 
        str_replace('Z', 'z', new Character('Z')
      ));
    }
  }
?>
