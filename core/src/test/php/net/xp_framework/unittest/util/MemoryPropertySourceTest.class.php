<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'util.MemoryPropertySource'
  );

  /**
   * Test for MemoryPropertySource
   *
   * @see      xp://util.MemoryPropertySource
   */
  class MemoryPropertySourceTest extends TestCase {
    protected $fixture= NULL;

    public function setUp() {
      $this->fixture= new MemoryPropertySource();
    }
    
    /**
     * Test
     *
     */
    #[@test]
    public function create() {
      new MemoryPropertySource();
    }

    /**
     * Test
     *
     */
    #[@test]
    public function doesNotHaveAnyProperties() {
      $this->assertFalse($this->fixture->provides('properties'));
    }

    /**
     * Test
     *
     */
    #[@test]
    public function hasPropertiesDetectsRegisteredProperties() {
      $this->fixture->register('properties', new Properties(NULL));
      $this->assertTrue($this->fixture->provides('properties'));
    }

    /**
     * Test
     *
     */
    #[@test]
    public function returnsRegisteredProperties() {
      $p= new Properties(NULL);
      $this->fixture->register('properties', $p);
      $this->assertTrue($p === $this->fixture->fetch('properties'));
    }
  }
?>
