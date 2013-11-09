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
#[@action([
#  new \unittest\actions\ExtensionAvailable('gd'),
#  new ImageTypeSupport('PNG')
#])]
class PngImageReaderTest extends TestCase {

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
