<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'util.Properties',
    'util.ResourcePropertySource'
  );

  /**
   * Test for ResourcePropertySource
   *
   * @see      xp://util.ResourcePropertySource
   */
  class ResourcePropertySourceTest extends TestCase {
    protected $fixture= NULL;

    public function setUp() {
      $this->fixture= new ResourcePropertySource('net/xp_framework/unittest/util');
    }

    /**
     * Test
     *
     */
    #[@test]
    public function doesNotHaveProperty() {
      $this->assertFalse($this->fixture->provides('non-existent'));
    }

    /**
     * Test
     *
     */
    #[@test]
    public function hasProperty() {
      $this->assertTrue($this->fixture->provides('example'));
    }

    /**
     * Test
     *
     * Relies on a file "example.ini" existing parallel to this class
     */
    #[@test]
    public function returnsProperties() {
      $this->assertEquals('value', $this->fixture->fetch('example')->readString('section', 'key'));
    }

    /**
     * Test
     *
     */
    #[@test]
    public function sourcesAreEqual() {
      $p1= new ResourcePropertySource('net/xp_framework/unittest/util');
      $p2= new ResourcePropertySource('net/xp_framework/unittest/util');

      $this->assertEquals($p1, $p2);
    }

    /**
     * Test
     *
     * Relies on a file "example.ini" existing parallel to this class
     */
    #[@test]
    public function propertiesFromSameResourceAreEqual() {
      $p1= new ResourcePropertySource('net/xp_framework/unittest/util');
      $p2= new ResourcePropertySource('net/xp_framework/unittest/util');

      $this->assertEquals($p1->fetch('example'), $p2->fetch('example'));
    }
  }
?>
