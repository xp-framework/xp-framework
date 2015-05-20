<?php namespace net\xp_framework\unittest\webservices\rest;

use webservices\rest\RestJsonDeserializer;


/**
 * TestCase
 *
 * @see   xp://webservices.rest.RestJsonDeserializer
 * @see   https://github.com/xp-framework/xp-framework/issues/362
 */
class RestJsonDeserializerTest extends RestDeserializerTest {

  /**
   * Creates and returns a new fixture
   *
   * @return  webservices.rest.RestDeserializer
   */
  protected function newFixture() {
    return new RestJsonDeserializer();
  }

  #[@test]
  public function one_keyvalue_pair() {
    $this->assertEquals(
      array('name' => 'Timm'), 
      $this->fixture->deserialize($this->input('{ "name" : "Timm" }'))
    );
  }

  #[@test]
  public function two_keyvalue_pairs() {
    $this->assertEquals(
      array('name' => 'Timm', 'id' => '1549'), 
      $this->fixture->deserialize($this->input('{ "name" : "Timm", "id" : "1549" }'))
    );
  }

  #[@test]
  public function deserialize_unicode() {
    $this->assertEquals(
      array('en-dash' => '–'),
      $this->fixture->deserialize($this->input('{ "en-dash" : "\u2013" }'), 'utf-8')
    );
  }
}
