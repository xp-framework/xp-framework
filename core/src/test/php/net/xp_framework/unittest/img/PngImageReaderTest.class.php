<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'img.Image',
    'img.io.PngStreamReader',
    'io.Stream',
    'io.FileUtil',
    'io.streams.MemoryInputStream'
  );

  /**
   * Tests reading PNG images
   *
   * @see   xp://img.io.JpegStreamReader
   */
  class PngImageReaderTest extends TestCase {

    /**
     * Setup this test, checking prerequisites.
     */
    public function setUp() {
      if (!Runtime::getInstance()->extensionAvailable('gd')) {
        throw new PrerequisitesNotMetError('GD extension not available');
      }
      if (!(imagetypes() & IMG_PNG)) {
        throw new PrerequisitesNotMetError('PNG support not enabled');
      }
    }

    #[@test]
    public function read() {
      $s= new MemoryInputStream(base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABAQMAAAAl21bKAAAAA1BMVEX///+nxBvIAAAACklEQVQImWNgAAAAAgAB9HFkpgAAAABJRU5ErkJggg=='));
      Image::loadFrom(new PngStreamReader($s));
    }

    #[@test]
    public function read_bc() {
      $s= new Stream();
      FileUtil::setContents($s, base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABAQMAAAAl21bKAAAAA1BMVEX///+nxBvIAAAACklEQVQImWNgAAAAAgAB9HFkpgAAAABJRU5ErkJggg=='));
      Image::loadFrom(new StreamReader(ref($s)));
    }
  }
?>
