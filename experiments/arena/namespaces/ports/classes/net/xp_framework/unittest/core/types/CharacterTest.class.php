<?php
/* This class is part of the XP framework
 *
 * $Id: CharacterTest.class.php 10171 2007-04-29 17:10:45Z friebe $ 
 */

  namespace net::xp_framework::unittest::core::types;

  ::uses(
    'unittest.TestCase',
    'lang.types.Character'
  );

  /**
   * TestCase
   *
   * @see      xp://lang.types.Character
   * @purpose  Unittest
   */
  class CharacterTest extends unittest::TestCase {

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
      new lang::types::Character('ä', 'UTF-8');
    }

    /**
     * Test a string with an incomplete multibyte character in it
     *
     */
    #[@test]
    public function nullByte() {
      $this->assertEquals("\x00", ::create(new lang::types::Character(0))->getBytes());
    }

    /**
     * Test a string with an incomplete multibyte character in it
     *
     */
    #[@test]
    public function euroSymbol() {
      $this->assertEquals("\xe2\x82\xac", ::create(new lang::types::Character(8364))->getBytes()); // &#8364; in HTML
    }
  
    /**
     * Test a string with an illegal character in it
     *
     */
    #[@test, @expect('lang.FormatException')]
    public function illegalCharacter() {
      new lang::types::Character('ä', 'US-ASCII');
    }

    /**
     * Test
     *
     */
    #[@test]
    public function usAsciiCharacter() {
      $this->assertEquals('H', ::create(new lang::types::Character('H'))->getBytes());
    }

    /**
     * Test a string containing German umlauts
     *
     */
    #[@test]
    public function umlautCharacter() {
      $this->assertEquals('Ã¤', ::create(new lang::types::Character('ä'))->getBytes());
    }

    /**
     * Test a string with UTF-8 in it
     *
     */
    #[@test]
    public function utf8Character() {
      $this->assertEquals(
        new lang::types::Character('Ã¤', 'UTF-8'),
        new lang::types::Character('ä', 'ISO-8859-1')
      );
    }

    /**
     * Test translatiom
     *
     */
    #[@test]
    public function transliteration() {
      $this->assertEquals('c', ::create(new lang::types::String('Ä', 'UTF-8'))->toString());
    }
  }
?>
