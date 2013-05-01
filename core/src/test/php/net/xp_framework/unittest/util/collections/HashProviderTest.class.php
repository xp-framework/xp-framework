<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses('unittest.TestCase', 'util.collections.HashProvider');

  /**
   * Test HashProvider class
   *
   * @see  xp://util.collections.HashProvider
   */
  class HashProviderTest extends TestCase {
    protected $fixture;

    /**
     * Creates fixture
     */
    public function setUp() {
      $this->fixture= HashProvider::getInstance();
    }

    /**
     * Tests getImplementation() and setImplementation()
     */
    #[@test]
    public function implementation_accessors() {
      $impl= newinstance('util.collections.HashImplementation', array(), '{
        public function hashOf($str) { /* Intentionally empty */ }
      }');

      $backup= $this->fixture->getImplementation();    // Backup
      $this->fixture->setImplementation($impl);
      $cmp= $this->fixture->getImplementation();
      $this->fixture->setImplementation($backup);      // Restore

      $this->assertEquals($impl, $cmp);
    }

    /**
     * Tests hashOf()
     */
    #[@test]
    public function hashof_uses_implementations_hashof() {
      $this->assertEquals(
        $this->fixture->getImplementation()->hashOf('Test'),
        HashProvider::hashOf('Test')
      );
    }
  }
?>
