<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'webservices.rest.RestJsonSerializer',
    'util.Date',
    'util.TimeZone'
  );

  /**
   * TestCase
   *
   * @see   xp://webservices.rest.RestJsonSerializer
   */
  class RestJsonSerializerTest extends TestCase {
    protected $fixture= NULL;
  
    /**
     * Sets up test case
     *
     */
    public function setUp() {
      $this->fixture= new RestJsonSerializer();
    }
    
    /**
     * Test
     *
     */
    #[@test]
    public function emptyArray() {
      $this->assertEquals('[ ]', $this->fixture->serialize(array()));
    }

    /**
     * Test
     *
     */
    #[@test]
    public function intArray() {
      $this->assertEquals('[ 1 , 2 , 3 ]', $this->fixture->serialize(array(1, 2, 3)));
    }
  }
?>
