<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('net.xp_framework.unittest.webservices.rest.RestDeserializerTest', 'webservices.rest.RestJsonDeserializer');

  /**
   * TestCase
   *
   * @see   xp://webservices.rest.RestJsonDeserializer
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

    /**
     * Test
     *
     */
    #[@test]
    public function one_keyvalue_pair() {
      $this->assertEquals(
        array('name' => 'Timm'), 
        $this->fixture->deserialize($this->input('{ "name" : "Timm" }'), Type::forName('[:string]'))
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function two_keyvalue_pairs() {
      $this->assertEquals(
        array('name' => 'Timm', 'id' => '1549'), 
        $this->fixture->deserialize($this->input('{ "name" : "Timm", "id" : "1549" }'), Type::forName('[:string]'))
      );
    }
  }
?>
