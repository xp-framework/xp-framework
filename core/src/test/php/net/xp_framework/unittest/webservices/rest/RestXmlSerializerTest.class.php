<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'webservices.rest.RestXmlSerializer'
  );

  /**
   * TestCase
   *
   * @see   xp://webservices.rest.RestXmlSerializer
   */
  class RestXmlSerializerTest extends TestCase {
    protected $fixture= NULL;
  
    /**
     * Sets up test case
     *
     */
    public function setUp() {
      $this->fixture= new RestXmlSerializer();
    }
    
    /**
     * Asserttion helper
     *
     * @param   string expected
     * @param   string actual
     * @throws  unittest.AssertionFailedError
     */
    protected function assertXmlEquals($expected, $actual) {
      $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?>'."\n".$expected, $actual);
    }
    
    /**
     * Test
     *
     */
    #[@test]
    public function emptyArray() {
      $this->assertXmlEquals(
        '<root></root>', 
        $this->fixture->serialize(array())
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function intArray() {
      $this->assertXmlEquals(
        '<root><root>1</root><root>2</root><root>3</root></root>', 
        $this->fixture->serialize(array(1, 2, 3))
      );
    }
  }
?>
