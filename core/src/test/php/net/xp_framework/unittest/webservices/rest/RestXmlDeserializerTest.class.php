<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('net.xp_framework.unittest.webservices.rest.RestDeserializerTest', 'webservices.rest.RestXmlDeserializer');

  /**
   * TestCase
   *
   * @see   xp://webservices.rest.RestJsonDeserializer
   */
  class RestXmlDeserializerTest extends RestDeserializerTest {

    /**
     * Creates and returns a new fixture
     *
     * @return  webservices.rest.RestDeserializer
     */
    protected function newFixture() {
      return new RestXmlDeserializer();
    }

    /**
     * Flattens a RestXmlMap into a map
     *
     * @param  webservices.rest.RestXmlMap $in
     * @return [:var]
     */
    protected function flatten(RestXmlMap $in) {
      $result= array();
      foreach ($in as $key => $value) {
        $result[$key]= $value;
      }
      return $result;
    }

    /**
     * Test
     *
     */
    #[@test]
    public function one_keyvalue_pair() {
      $this->assertEquals(
        array('name' => 'Timm'), 
        $this->flatten($this->fixture->deserialize($this->input('<root><name>Timm</name></root>')))
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
        $this->flatten($this->fixture->deserialize($this->input('<root><name>Timm</name><id>1549</id></root>')))
      );
    }
  }
?>
