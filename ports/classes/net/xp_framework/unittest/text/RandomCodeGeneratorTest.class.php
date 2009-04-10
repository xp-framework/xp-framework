<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'text.util.RandomCodeGenerator'
  );

  /**
   * TestCase
   *
   */
  class RandomCodeGeneratorTest extends TestCase {

    /**
     * Setup test fixture
     *
     */
    public function setUp() {
      $this->fixture= new RandomCodeGenerator(16);
    }
      
    /**
     * Test
     *
     */
    #[@test]
    public function length() {
      $this->assertEquals(
        16,
        strlen($this->fixture->generate())
      );
    }
  }
?>
