<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase'
  );

  /**
   * TestCase for this() core functionality.
   *
   */
  class ThisTest extends TestCase {
  
    /**
     * Test reading an array offset
     *
     */
    #[@test]
    public function arrayOffset() {
      $this->assertEquals(1, this(array(1, 2, 3), 0));
    }

    /**
     * Test reading a map offset
     *
     */
    #[@test]
    public function mapOffset() {
      $this->assertEquals('World', this(array('Hello' => 'World'), 'Hello'));
    }

    /**
     * Test reading a string offset
     *
     */
    #[@test]
    public function stringOffset() {
      $this->assertEquals('W', this('World', 0));
    }

    /**
     * Test reading an offset from an integer
     *
     */
    #[@test]
    public function intOffset() {
      $this->assertNull(this(0, 0));
    }
  }
?>
