<?php namespace net\xp_framework\unittest\util\collections;
 
use util\collections\DJBX33AHashImplementation;


/**
 * Test DJBX33A
 *
 * @see  xp://util.collections.DJBX33AHashImplementation
 */
class DJBX33AHashImplementationTest extends AbstractHashImplementationTest {

  /**
   * Creates new fixture
   *
   * @return  util.collections.HashImplementation
   */
  protected function newFixture() {
    return new DJBX33AHashImplementation();
  }

  /**
   * Tests hashOf()
   */
  #[@test]
  public function hashof_empty() {
    $this->assertEquals(5381, $this->fixture->hashOf(''));
  }

  /**
   * Tests hashOf()
   */
  #[@test, @ignore('Different result on 64-bit systems!')]
  public function hashof_test() {
    $this->assertEquals(2090756197, $this->fixture->hashOf('test'));
  }
}
