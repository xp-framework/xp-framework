<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('net.xp_framework.unittest.webservices.rest.RestDeserializerTest', 'webservices.rest.RestFormDeserializer');

  /**
   * TestCase
   *
   * @see   xp://webservices.rest.RestJsonDeserializer
   */
  class RestFormDeserializerTest extends RestDeserializerTest {

    /**
     * Creates and returns a new fixture
     *
     * @return  webservices.rest.RestDeserializer
     */
    protected function newFixture() {
      return new RestFormDeserializer();
    }

    /**
     * Test
     *
     */
    #[@test]
    public function one_keyvalue_pair() {
      $this->assertEquals(
        array('name' => 'Timm'), 
        $this->fixture->deserialize($this->input('name=Timm'), Type::forName('[:string]'))
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
        $this->fixture->deserialize($this->input('name=Timm&id=1549'), Type::forName('[:string]'))
      );
    }

    /**
     * Test key[]=...
     *
     */
    #[@test]
    public function array_of_strings() {
      $this->assertEquals(
        array('name' => array('Timm', 'Alex')), 
        $this->fixture->deserialize($this->input('name[]=Timm&name[]=Alex'), Type::forName('[:string[]]'))
      );
    }

    /**
     * Test key[map]=...
     *
     */
    #[@test]
    public function map_of_strings() {
      $this->assertEquals(
        array('name' => array('thekid' => 'Timm', 'kiesel' => 'Alex')), 
        $this->fixture->deserialize($this->input('name[thekid]=Timm&name[kiesel]=Alex'), Type::forName('[:[:string]]'))
      );
    }

    /**
     * Test url encoding
     *
     */
    #[@test]
    public function urlencoded_key() {
      $this->assertEquals(
        array('The Name' => 'Timm'), 
        $this->fixture->deserialize($this->input('The%20Name=Timm'), Type::forName('[:string]'))
      );
    }

    /**
     * Test url encoding
     *
     */
    #[@test]
    public function urlencoded_value() {
      $this->assertEquals(
        array('name' => 'Timm Friebe'), 
        $this->fixture->deserialize($this->input('name=Timm%20Friebe'), Type::forName('[:string]'))
      );
    }

    /**
     * Test name[=...
     *
     */
    #[@test, @expect('lang.FormatException')]
    public function unbalanced_opening_bracket_in_key() {
      $this->fixture->deserialize($this->input('name[=...'), Type::$VAR);
    }

    /**
     * Test name]=...
     *
     */
    #[@test, @expect('lang.FormatException')]
    public function unbalanced_closing_bracket_in_key() {
      $this->fixture->deserialize($this->input('name]=...'), Type::$VAR);
    }
  }
?>
