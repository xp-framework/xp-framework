<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'io.Stream',
    'io.FileUtil',
    'img.Image',
    'io.streams.MemoryOutputStream'
  );

  /**
   * Tests writing images
   *
   * @see  xp://img.io.ImageWriter
   */
  abstract class AbstractImageWriterTest extends TestCase {
    protected $image= NULL;

    /**
     * Returns the image type to test for
     *
     * @return string
     */
    protected abstract function imageType();

    /**
     * Setup this test. Creates a 1x1 pixel image filled with white.
     */
    public function setUp() {
      if (!Runtime::getInstance()->extensionAvailable('gd')) {
        throw new PrerequisitesNotMetError('GD extension not available');
      }
      $type= $this->imageType();
      if (!(imagetypes() & constant('IMG_'.$type))) {
        throw new PrerequisitesNotMetError($type.' support not enabled');
      }
      $this->image= Image::create(1, 1);
      $this->image->fill($this->image->allocate(new Color('#ffffff')));
    }
  
    /**
     * Tears down this test
     *
     */
    public function tearDown() {
      delete($this->image);
    }
  }
?>
