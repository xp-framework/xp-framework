<?php namespace net\xp_framework\unittest\img;

use unittest\TestCase;
use img\Image;
use img\io\PngStreamReader;
use io\Stream;
use io\FileUtil;
use io\streams\MemoryInputStream;


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
    if (!\lang\Runtime::getInstance()->extensionAvailable('gd')) {
      throw new \unittest\PrerequisitesNotMetError('GD extension not available');
    }
    if (!(imagetypes() & IMG_PNG)) {
      throw new \unittest\PrerequisitesNotMetError('PNG support not enabled');
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
    Image::loadFrom(new \img\io\StreamReader(ref($s)));
  }
}
