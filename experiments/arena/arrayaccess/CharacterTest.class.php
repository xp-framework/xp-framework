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
      $c= new Character(0);
      $this->assertEquals("\x00", $c->getBytes());
    }

    /**
     * Test a string with an incomplete multibyte character in it
     *
     */
    #[@test]
    public function euroSymbol() {
      $c= new Character(0x20A0);    // &#8352; in HTML
      $this->assertEquals("\xe2\x82\xa0", $c->getBytes());
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
     * Test
     *
     */
    #[@test]
    public function usAsciiCharacter() {
      $str= new Character('H');
      $this->assertEquals('H', $str->getBytes());
    }

    /**
     * Test a string containing German umlauts
     *
     */
    #[@test]
    public function umlautCharacter() {
      $str= new Character('ä');
      $this->assertEquals('Ã¤', $str->getBytes());
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
      $s= new String('Ä', 'UTF-8');
      $this->assertEquals('c', $s->toString());
    }
  }
?>
