<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses('unittest.TestCase', 'util.collections.HashImplementation');

  /**
   * Base class for all HashImplementation tests
   *
   * @see  xp://util.collections.HashImplementation
   */
  abstract class AbstractHashImplementationTest extends TestCase {
    protected $fixture;

    /**
     * Initializes fixture
     */
    public function setUp() {
      $this->fixture= $this->newFixture();
    }

    /**
     * Creates new fixture
     *
     * @return  util.collections.HashImplementation
     */
    protected abstract function newFixture();

    /**
     * Tests hashOf()
     */
    #[@test]
    public function hashof_returns_non_empty_string_for_empty_input() {
      $this->assertNotEquals('', $this->fixture->hashOf(''));
    }

    /**
     * Tests hashOf()
     */
    #[@test]
    public function hashof_returns_non_empty_string_for_non_empty_input() {
      $this->assertNotEquals('', $this->fixture->hashOf('Test'));
    }
  }
?>
