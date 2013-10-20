<?php namespace net\xp_framework\unittest\webservices\rest;

use unittest\TestCase;
use webservices\rest\RestJsonSerializer;
use util\Date;
use util\TimeZone;

/**
 * TestCase
 *
 * @see   xp://webservices.rest.RestJsonSerializer
 */
class RestJsonSerializerTest extends TestCase {
  protected $fixture= null;

  /**
   * Sets up test case
   */
  public function setUp() {
    $this->fixture= new RestJsonSerializer();
  }
  
  #[@test]
  public function empty_array() {
    $this->assertEquals('[ ]', $this->fixture->serialize(array()));
  }

  #[@test]
  public function int_array() {
    $this->assertEquals('[ 1 , 2 , 3 ]', $this->fixture->serialize(array(1, 2, 3)));
  }

  #[@test]
  public function string_array() {
    $this->assertEquals('[ "a" , "b" , "c" ]', $this->fixture->serialize(array('a', 'b', 'c')));
  }
}
