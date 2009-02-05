<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'scriptlet.LocaleNegotiator'
  );

  /**
   * TestCase
   *
   * @see      xp://scriptlet.LocaleNegotiator
   */
  class LocaleNegotiatorTest extends TestCase {
    protected $fixture= NULL;

    /**
     * Sets up test case
     *
     */
    public function setUp() {
      $this->fixture= new LocaleNegotiator(
        $acceptLanguage = 'de-at, de;q=0.75, en-us;q=0.50, en;q=0.25',
        $acceptCharset  = 'ISO-8859-1,utf-8;q=0.7,*;q=0.7'
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function languagePreference() {
      $this->assertEquals(
        new Locale('de_AT'), 
        $this->fixture->getLocale($supported= array('de_AT', 'de_DE', 'en_US'), $default= 'C')
      );
      $this->assertEquals(
        new Locale('de_AT'), 
        $this->fixture->getLocale($supported= array('de_DE', 'de_AT', 'en_US'), $default= 'C')
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function secondLanguagePreference() {
      $this->assertEquals(
        new Locale('de_DE'), 
        $this->fixture->getLocale($supported= array('de_DE', 'en_US'), $default= 'C')
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function thirdLanguagePreference() {
      $this->assertEquals(
        new Locale('en_US'), 
        $this->fixture->getLocale($supported= array('en_US'), $default= 'C')
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function fourthLanguagePreference() {
      $this->assertEquals(
        new Locale('en_UK'), 
        $this->fixture->getLocale($supported= array('en_UK'), $default= 'C')
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function defaultLanguage() {
      $this->assertEquals(
        new Locale('C'), 
        $this->fixture->getLocale($supported= array('es_ES'), $default= 'C')
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function charsetPreference() {
      $this->assertEquals(
        'ISO-8859-1', 
        $this->fixture->getCharset($supported= array('ISO-8859-1', 'UTF-8', 'ISO-8859-15'), $default= 'ASCII')
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function secondCharsetPreference() {
      $this->assertEquals(
        'UTF-8', 
        $this->fixture->getCharset($supported= array('UTF-8', 'ISO-8859-15'), $default= 'ASCII')
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function anyCharsetPreference() {
      $this->assertEquals(
        'ISO-8859-15', 
        $this->fixture->getCharset($supported= array('ISO-8859-15'), $default= 'ASCII')
      );
    }
  }
?>
