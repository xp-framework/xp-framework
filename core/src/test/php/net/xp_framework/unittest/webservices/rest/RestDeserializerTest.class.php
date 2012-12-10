<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'webservices.rest.RestJsonDeserializer',
    'io.streams.MemoryInputStream'
  );

  /**
   * TestCase
   *
   * @see   xp://webservices.rest.RestDeserializer
   */
  abstract class RestDeserializerTest extends TestCase {
    protected $fixture= NULL;
  
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
      $this->fixture->deserialize($this->input(''), Type::$VAR);
    }
  }
?>
