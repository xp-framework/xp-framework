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
   * @see   xp://text.util.RandomCodeGenerator
   */
  class RandomCodeGeneratorTest extends TestCase {
    protected $fixture= NULL;

    /**
     * Setup test fixture
     *
     */
    public function setUp() {
      $this->fixture= new RandomCodeGenerator(16);
    }
      
    /**
     * Test that the generated code is 16 characters long
     *
     */
    #[@test]
    public function length() {
      $this->assertEquals(16, strlen($this->fixture->generate()));
    }

    /**
     * Test that the generated code contains only lowercase a-z letters
     * and numbers.
     *
     */
    #[@test]
    public function format() {
      $this->assertTrue((bool)preg_match('/^[a-z0-9]{16}$/', $this->fixture->generate()));
    }

    /**
     * Test constructing a RandomCodeGenerator with a length of zero
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function zeroLength() {
      new RandomCodeGenerator(0);
    }

    /**
     * Test constructing a RandomCodeGenerator with a negative length
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function negativeLength() {
      new RandomCodeGenerator(-1);
    }

    /**
     * Test constructing a RandomCodeGenerator with a huge length
     *
     */
    #[@test]
    public function hugeLength() {
      $this->assertEquals(10000, strlen(create(new RandomCodeGenerator(10000))->generate()));
    }
  }
?>
