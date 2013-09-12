<?php namespace net\xp_framework\unittest\util;

use unittest\TestCase;
use util\Locale;


/**
 * TestCase
 *
 * @see      xp://util.Locale
 */
class LocaleTest extends TestCase {

  /**
   * Test getDefault()
   */
  #[@test]
  public function get_default_locale() {
    $this->assertInstanceOf('util.Locale', Locale::getDefault());
  }

  /**
   * Test constructor
   */
  #[@test]
  public function constructor_with_one_arg() {
    $this->assertEquals('de_DE', create(new Locale('de_DE'))->toString());
  }

  /**
   * Test constructor
   */
  #[@test]
  public function constructor_with_two_args() {
    $this->assertEquals('de_DE', create(new Locale('de', 'DE'))->toString());
  }

  /**
   * Test constructor
   */
  #[@test]
  public function constructor_with_three_args() {
    $this->assertEquals('de_DE@utf-8', create(new Locale('de', 'DE', 'utf-8'))->toString());
  }

  /**
   * Test getLanguage()
   */
  #[@test]
  public function de_DE_language() {
    $this->assertEquals('de', create(new Locale('de_DE'))->getLanguage());
  }

  /**
   * Test getLanguage()
   */
  #[@test]
  public function de_DE_country() {
    $this->assertEquals('DE', create(new Locale('de_DE'))->getCountry());
  }

  /**
   * Test getVariant()
   */
  #[@test]
  public function de_DE_variant() {
    $this->assertEquals('', create(new Locale('de_DE'))->getVariant());
  }

  /**
   * Test getVariant()
   */
  #[@test]
  public function de_DE_with_variant() {
    $this->assertEquals('@utf-8', create(new Locale('de_DE@utf-8'))->getVariant());
  }
}
