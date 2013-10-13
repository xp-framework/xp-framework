<?php namespace net\xp_framework\unittest\img;

use unittest\TestCase;
use img\Image;
use img\io\GifStreamReader;
use io\Stream;
use io\FileUtil;
use io\streams\MemoryInputStream;


/**
 * Tests reading GIF images
 *
 * @see   xp://img.io.JpegStreamReader
 */
class GifImageReaderTest extends TestCase {

  /**
   * Setup this test, checking prerequisites.
   */
  public function setUp() {
    if (!\lang\Runtime::getInstance()->extensionAvailable('gd')) {
      throw new \unittest\PrerequisitesNotMetError('GD extension not available');
    }
    if (!(imagetypes() & IMG_GIF)) {
      throw new \unittest\PrerequisitesNotMetError('GIF support not enabled');
    }
  }

  #[@test]
  public function read() {
    $s= new MemoryInputStream(base64_decode('R0lGODdhAQABAIAAAP///wAAACwAAAAAAQABAAACAkQBADs='));
    Image::loadFrom(new GifStreamReader($s));
  }

  #[@test]
  public function read_bc() {
    $s= new Stream();
    FileUtil::setContents($s, base64_decode('R0lGODdhAQABAIAAAP///wAAACwAAAAAAQABAAACAkQBADs='));
    Image::loadFrom(new \img\io\StreamReader(ref($s)));
  }
}
