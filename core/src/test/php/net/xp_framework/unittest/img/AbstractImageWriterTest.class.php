<?php namespace net\xp_framework\unittest\img;

/**
 * Tests writing images
 *
 * @see  xp://img.io.ImageWriter
 */
abstract class AbstractImageWriterTest extends \unittest\TestCase {
  protected $image= null;

  /**
   * Setup this test. Creates a 1x1 pixel image filled with white.
   */
  public function setUp() {
    $this->image= \img\Image::create(1, 1);
    $this->image->fill($this->image->allocate(new \img\Color('#ffffff')));
  }

  /**
   * Tears down this test
   */
  public function tearDown() {
    delete($this->image);
  }
}
