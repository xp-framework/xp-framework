<?php namespace net\xp_framework\unittest\webservices\rest;

use unittest\TestCase;
use webservices\rest\RestXmlSerializer;
use util\Date;
use util\TimeZone;

/**
 * TestCase
 *
 * @see   xp://webservices.rest.RestXmlSerializer
 */
class RestXmlSerializerTest extends TestCase {
  protected $fixture= null;

  /**
   * Sets up test case
   */
  public function setUp() {
    $this->fixture= new RestXmlSerializer();
  }

  /**
   * Assertion helper
   *
   * @param   string $expected
   * @param   string $actual
   * @throws  unittest.AssertionFailedError
   */
  protected function assertXmlEquals($expected, $actual) {
    $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?>'."\n".$expected, $actual);
  }

  #[@test]
  public function null() {
    $this->assertXmlEquals(
      '<root></root>',
      $this->fixture->serialize(null)
    );
  }

  #[@test, @values(['', 'Test'])]
  public function strings($str) {
    $this->assertXmlEquals(
      '<root>'.$str.'</root>',
      $this->fixture->serialize($str)
    );
  }

  #[@test, @values([-1, 0, 1, 4711])]
  public function integers($int) {
    $this->assertXmlEquals(
      '<root>'.$int.'</root>',
      $this->fixture->serialize($int)
    );
  }

  #[@test, @values([-1.0, 0.0, 1.0, 47.11])]
  public function decimals($decimal) {
    $this->assertXmlEquals(
      '<root>'.$decimal.'</root>',
      $this->fixture->serialize($decimal)
    );
  }

  #[@test, @values([false, true])]
  public function booleans($bool) {
    $this->assertXmlEquals(
      '<root>'.$bool.'</root>',
      $this->fixture->serialize($bool)
    );
  }

  #[@test]
  public function empty_array() {
    $this->assertXmlEquals(
      '<root></root>',
      $this->fixture->serialize(array())
    );
  }

  #[@test]
  public function int_array() {
    $this->assertXmlEquals(
      '<root><root>1</root><root>2</root><root>3</root></root>',
      $this->fixture->serialize(array(1, 2, 3))
    );
  }

  #[@test]
  public function string_array() {
    $this->assertXmlEquals(
      '<root><root>a</root><root>b</root><root>c</root></root>',
      $this->fixture->serialize(array('a', 'b', 'c'))
    );
  }

  #[@test]
  public function string_map() {
    $this->assertXmlEquals(
      '<root><a>One</a><b>Two</b><c>Three</c></root>',
      $this->fixture->serialize(array('a' => 'One', 'b' => 'Two', 'c' => 'Three'))
    );
  }

  #[@test]
  public function date() {
    $this->assertXmlEquals(
      '<root><value>2012-12-31 18:00:00+0100</value><__id></__id></root>',
      $this->fixture->serialize(new Date('2012-12-31 18:00:00', new TimeZone('Europe/Berlin')))
    );
  }
}
