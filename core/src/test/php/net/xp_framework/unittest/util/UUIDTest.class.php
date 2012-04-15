<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('unittest.TestCase', 'util.UUID');

  /**,
   * TestCase
   *
   * @see   xp://util.UUID
   */
  class UUIDTest extends TestCase {
    protected $fixture= NULL;
  
    /**
     * Creates fixture
     *
     */
    public function setUp() {
      $this->fixture= UUID::fromString('00000000-0000-0000-C000-000000000046');
    }

    /**
     * Test toString()
     *
     */
    #[@test]
    public function stringRepresentation() {
      $this->assertEquals('00000000-0000-0000-c000-000000000046', $this->fixture->toString());
    }
  
    /**
     * Test node
     *
     */
    #[@test]
    public function node() {
      $this->assertEquals(array(0, 0, 0, 0, 0, 70), $this->fixture->node);
    }

    /**
     * Test creating a UUID
     *
     */
    #[@test]
    public function createdUUIDNotEqualToFixture() {
      $this->assertNotEquals($this->fixture, UUID::create());
    }

    /**
     * Test creating a UUID
     *
     */
    #[@test]
    public function twoCreatedUUIDsNotEqual() {
      $this->assertNotEquals(UUID::create(), UUID::create());
    }
  }
?>
