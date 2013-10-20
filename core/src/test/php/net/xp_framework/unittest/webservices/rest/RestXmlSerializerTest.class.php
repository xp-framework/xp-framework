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
  public function emptyArray() {
    $this->assertXmlEquals(
      '<root></root>', 
      $this->fixture->serialize(array())
    );
  }

  #[@test]
  public function intArray() {
    $this->assertXmlEquals(
      '<root><root>1</root><root>2</root><root>3</root></root>', 
      $this->fixture->serialize(array(1, 2, 3))
    );
  }
}
