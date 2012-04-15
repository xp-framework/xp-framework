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
      $this->fixture= new UUID('6ba7b811-9dad-11d1-80b4-00c04fd430c8');
    }

    /**
     * Test toString()
     *
     */
    #[@test]
    public function stringRepresentation() {
      $this->assertEquals('6ba7b811-9dad-11d1-80b4-00c04fd430c8', $this->fixture->toString());
    }

    /**
     * Test toUrn()
     *
     */
    #[@test]
    public function urnRepresentation() {
      $this->assertEquals('urn:uuid:6ba7b811-9dad-11d1-80b4-00c04fd430c8', $this->fixture->toUrn());
    }

    /**
     * Test hashCode()
     *
     */
    #[@test]
    public function hashCodeMethod() {
      $this->assertEquals('6ba7b811-9dad-11d1-80b4-00c04fd430c8', $this->fixture->hashCode());
    }

    /**
     * Test getBytes()
     *
     */
    #[@test]
    public function getBytes() {
      $this->assertEquals(
        new Bytes("k\xa7\xb8\x11\x9d\xad\x11\xd1\x80\xb4\x00\xc0O\xd40\xc8"), 
        $this->fixture->getBytes()
      );
    }
  
    /**
     * Test node
     *
     */
    #[@test]
    public function node() {
      $this->assertEquals(array(0, 192, 79, 212, 48, 200), $this->fixture->node);
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
    public function fixtureEqualToUpperCaseSelf() {
      $this->assertEquals($this->fixture, new UUID('6BA7B811-9DAD-11D1-80B4-00C04FD430C8'));
    }

    /**
     * Test creating a UUID
     *
     */
    #[@test]
    public function fixtureEqualToBracedSelf() {
      $this->assertEquals($this->fixture, new UUID('{6ba7b811-9dad-11d1-80b4-00c04fd430c8}'));
    }

    /**
     * Test creating a UUID
     *
     */
    #[@test]
    public function fixtureEqualToUrnNotation() {
      $this->assertEquals($this->fixture, new UUID('urn:uuid:6ba7b811-9dad-11d1-80b4-00c04fd430c8'));
    }

    /**
     * Test getBytes()
     *
     */
    #[@test]
    public function fixtureEqualsToBytes() {
      $this->assertEquals($this->fixture, new UUID(new Bytes("k\xa7\xb8\x11\x9d\xad\x11\xd1\x80\xb4\x00\xc0O\xd40\xc8")));
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
