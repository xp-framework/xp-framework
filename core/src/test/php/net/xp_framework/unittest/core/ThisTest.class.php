<?php namespace net\xp_framework\unittest\core;

/**
 * TestCase for this() core functionality.
 *
 */
class ThisTest extends \unittest\TestCase {

  #[@test]
  public function arrayOffset() {
    $this->assertEquals(1, this(array(1, 2, 3), 0));
  }

  #[@test]
  public function mapOffset() {
    $this->assertEquals('World', this(array('Hello' => 'World'), 'Hello'));
  }

  #[@test]
  public function stringOffset() {
    $this->assertEquals('W', this('World', 0));
  }

  #[@test]
  public function intOffset() {
    $this->assertNull(this(0, 0));
  }
}
