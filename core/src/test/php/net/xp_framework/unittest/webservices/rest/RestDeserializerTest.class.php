<?php namespace net\xp_framework\unittest\webservices\rest;

use unittest\TestCase;
use webservices\rest\RestJsonDeserializer;
use io\streams\MemoryInputStream;


/**
 * TestCase
 *
 * @see   xp://webservices.rest.RestDeserializer
 */
abstract class RestDeserializerTest extends TestCase {
  protected $fixture= null;

  /**
   * Sets up test case
   *
   */
  public function setUp() {
    $this->fixture= $this->newFixture();
  }

  /**
   * Creates and returns a new fixture
   *
   * @return  webservices.rest.RestDeserializer
   */
  protected abstract function newFixture();

  /**
   * CReates an input stream
   *
   * @param   string bytes
   * @return  io.streams.MemoryInputStream
   */
  protected function input($bytes) {
    return new MemoryInputStream($bytes);
  }
  
  /**
   * Test empty input
   *
   */
  #[@test, @expect('lang.FormatException')]
  public function empty_content() {
    $this->fixture->deserialize($this->input(''), \lang\Type::$VAR);
  }
}
