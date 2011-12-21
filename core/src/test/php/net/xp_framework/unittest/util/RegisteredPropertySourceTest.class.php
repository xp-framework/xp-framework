<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'util.RegisteredPropertySource'
  );

  /**
   * Test for RegisteredPropertySource
   *
   * @see      xp://util.RegisteredPropertySource
   */
  class RegisteredPropertySourceTest extends TestCase {
    protected $fixture= NULL;

    public function setUp() {
      $this->fixture= new RegisteredPropertySource('props', new Properties(NULL));
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
    public function hasRegisteredProperty() {
      $this->assertTrue($this->fixture->provides('props'));
    }

    /**
     * Test
     *
     */
    #[@test]
    public function returnsRegisteredProperties() {
      $p= new Properties(NULL);
      $m= new RegisteredPropertySource('name', $p);

      $this->assertTrue($p === $m->fetch('name'));
    }

    /**
     * Test
     *
     */
    #[@test]
    public function equalsReturnsFalseForDifferingName() {
      $p1= new RegisteredPropertySource('name1', new Properties(NULL));
      $p2= new RegisteredPropertySource('name2', new Properties(NULL));

      $this->assertNotEquals($p1, $p2);
    }

    /**
     * Test
     *
     */
    #[@test]
    public function equalsReturnsFalseForDifferingProperties() {
      $p1= new RegisteredPropertySource('name1', new Properties(NULL));
      $p2= new RegisteredPropertySource('name1', Properties::fromString('[section]'));

      $this->assertNotEquals($p1, $p2);
    }

    /**
     * Test
     *
     */
    #[@test]
    public function equalsReturnsTrueForSameInnerPropertiesAndName() {
      $p1= new RegisteredPropertySource('name1', Properties::fromString('[section]'));
      $p2= new RegisteredPropertySource('name1', Properties::fromString('[section]'));

      $this->assertEquals($p1, $p2);
    }
  }
?>
