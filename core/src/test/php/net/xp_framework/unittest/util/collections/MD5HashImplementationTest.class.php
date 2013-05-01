<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses(
    'net.xp_framework.unittest.util.collections.AbstractHashImplementationTest', 
    'util.collections.MD5HashImplementation'
  );

  /**
   * Test MD5
   *
   * @see  xp://util.collections.MD5HashImplementation
   */
  class MD5HashImplementationTest extends AbstractHashImplementationTest {

    /**
     * Creates new fixture
     *
     * @return  util.collections.HashImplementation
     */
    protected function newFixture() {
      return new MD5HashImplementation();
    }

    /**
     * Tests hashOf()
     */
    #[@test]
    public function hashof_empty() {
      $this->assertEquals('0xd41d8cd98f00b204e9800998ecf8427e', $this->fixture->hashOf(''));
    }

    /**
     * Tests hashOf()
     */
    #[@test]
    public function hashof_test() {
      $this->assertEquals('0x098f6bcd4621d373cade4e832627b4f6', $this->fixture->hashOf('test'));
    }
  }
?>
