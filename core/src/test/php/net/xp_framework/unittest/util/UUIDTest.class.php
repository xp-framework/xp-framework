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
      $this->assertEquals('{6ba7b811-9dad-11d1-80b4-00c04fd430c8}', $this->fixture->toString());
    }

    /**
     * Test getUrn()
     *
     */
    #[@test]
    public function urnRepresentation() {
      $this->assertEquals('urn:uuid:6ba7b811-9dad-11d1-80b4-00c04fd430c8', $this->fixture->getUrn());
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
     * Test hashCode()
     *
     */
    #[@test]
    public function hashCodeMethod() {
      $this->assertEquals('6ba7b811-9dad-11d1-80b4-00c04fd430c8', $this->fixture->hashCode());
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
     * Test creating a UUID from bytes
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
     * Verify version 1 UUID creation
     *
     */
    #[@test]
    public function timeUUID() {
      $this->assertEquals(1, UUID::timeUUID()->version());
    }

    /**
     * Verify version 1 UUID creation
     *
     */
    #[@test]
    public function timeUUIDNotEqualToFixture() {
      $this->assertNotEquals($this->fixture, UUID::timeUUID());
    }

    /**
     * Verify version 1 UUID creation
     *
     */
    #[@test]
    public function twoTimeUUIDsNotEqual() {
      $this->assertNotEquals(UUID::timeUUID(), UUID::timeUUID());
    }

    /**
     * Verify version 4 UUID creation
     *
     */
    #[@test]
    public function randomUUID() {
      $this->assertEquals(4, UUID::randomUUID()->version());
    }

    /**
     * Verify version 4 UUID creation
     *
     */
    #[@test]
    public function randomUUIDNotEqualToFixture() {
      $this->assertNotEquals($this->fixture, UUID::randomUUID());
    }

    /**
     * Verify version 4 UUID creation
     *
     */
    #[@test]
    public function twoRandomUUIDsNotEqual() {
      $this->assertNotEquals(UUID::randomUUID(), UUID::randomUUID());
    }

    /**
     * Verify version 3 UUID creation
     *
     */
    #[@test]
    public function md5UUID() {
      $this->assertEquals(3, UUID::md5UUID(UUID::$NS_DNS, 'example.com')->version());
    }

    /**
     * Verify version 3 UUID creation
     *
     */
    #[@test]
    public function md5ExampleDotComWithDnsNs() {
      $this->assertEquals(
        '9073926b-929f-31c2-abc9-fad77ae3e8eb', 
        UUID::md5UUID(UUID::$NS_DNS, 'example.com')->hashCode()
      );
    }

    /**
     * Verify version 5 UUID creation
     *
     */
    #[@test]
    public function sha1UUID() {
      $this->assertEquals(5, UUID::sha1UUID(UUID::$NS_DNS, 'example.com')->version());
    }

    /**
     * Verify version 5 UUID creation
     *
     */
    #[@test]
    public function sha1ExampleDotComWithDnsNs() {
      $this->assertEquals(
        'cfbff0d1-9375-5685-968c-48ce8b15ae17', 
        UUID::sha1UUID(UUID::$NS_DNS, 'example.com')->hashCode()
      );
    }

    /**
     * Test version()
     *
     */
    #[@test]
    public function version() {
      $this->assertEquals(1, $this->fixture->version());
    }

    /**
     * Test serialization
     *
     */
    #[@test]
    public function serialization() {
      $this->assertEquals(
        'O:4:"UUID":1:{s:5:"value";s:36:"6ba7b811-9dad-11d1-80b4-00c04fd430c8";}', 
        serialize($this->fixture)
      );
    }

    /**
     * Test deserialization
     *
     */
    #[@test]
    public function deserialization() {
      $this->assertEquals(
        $this->fixture,
        unserialize('O:4:"UUID":1:{s:5:"value";s:36:"6ba7b811-9dad-11d1-80b4-00c04fd430c8";}')
      );
    }
  }
?>
