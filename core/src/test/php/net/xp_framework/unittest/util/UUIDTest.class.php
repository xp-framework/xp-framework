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
      $this->fixture= new UUID('00000000-0000-0000-C000-000000000046');
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
     * Test toUrn()
     *
     */
    #[@test]
    public function urnRepresentation() {
      $this->assertEquals('urn:uuid:00000000-0000-0000-c000-000000000046', $this->fixture->toUrn());
    }

    /**
     * Test hashCode()
     *
     */
    #[@test]
    public function hashCodeMethod() {
      $this->assertEquals('00000000-0000-0000-c000-000000000046', $this->fixture->hashCode());
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
    public function fixtureEqualToSelf() {
      $this->assertEquals($this->fixture, $this->fixture);
    }

    /**
     * Test creating a UUID
     *
     */
    #[@test]
    public function fixtureEqualToLowerCaseSelf() {
      $this->assertEquals($this->fixture, new UUID('00000000-0000-0000-c000-000000000046'));
    }

    /**
     * Test creating a UUID
     *
     */
    #[@test]
    public function fixtureEqualToBracedSelf() {
      $this->assertEquals($this->fixture, new UUID('{00000000-0000-0000-c000-000000000046}'));
    }

    /**
     * Test creating a UUID
     *
     */
    #[@test]
    public function fixtureEqualToUrnNotation() {
      $this->assertEquals($this->fixture, new UUID('urn:uuid:00000000-0000-0000-c000-000000000046'));
    }

    /**
     * Test malformed UUID
     *
     */
    #[@test, @expect('lang.FormatException')]
    public function emptyInput() {
      new UUID('');
    }

    /**
     * Test malformed UUID
     *
     */
    #[@test, @expect('lang.FormatException')]
    public function malFormedMissingOctets() {
      new UUID('00000000-0000-0000-c000');
    }

    /**
     * Test malformed UUID
     *
     */
    #[@test, @expect('lang.FormatException')]
    public function malFormedNonHexOctets() {
      new UUID('00000000-0000-0000-c000-XXXXXXXXXXXX');
    }

    /**
     * Test malformed UUID
     *
     */
    #[@test, @expect('lang.FormatException')]
    public function emptyBracedNotation() {
      new UUID('{}');
    }

    /**
     * Test malformed UUID
     *
     */
    #[@test, @expect('lang.FormatException')]
    public function malFormedBracedNotationMissingOctets() {
      new UUID('{00000000-0000-0000-c000}');
    }

    /**
     * Test malformed UUID
     *
     */
    #[@test, @expect('lang.FormatException')]
    public function malFormedBracedNotationNonHexOctets() {
      new UUID('{00000000-0000-0000-c000-XXXXXXXXXXXX}');
    }

    /**
     * Test malformed UUID
     *
     */
    #[@test, @expect('lang.FormatException')]
    public function emptyUrnNotation() {
      new UUID('urn:uuid:');
    }

    /**
     * Test malformed UUID
     *
     */
    #[@test, @expect('lang.FormatException')]
    public function malFormedUrnNotationMissingOctets() {
      new UUID('urn:uuid:00000000-0000-0000-c000');
    }


    /**
     * Test malformed UUID
     *
     */
    #[@test, @expect('lang.FormatException')]
    public function malFormedUrnNotationNonHexOctets() {
      new UUID('urn:uuid:00000000-0000-0000-c000-XXXXXXXXXXXX');
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
