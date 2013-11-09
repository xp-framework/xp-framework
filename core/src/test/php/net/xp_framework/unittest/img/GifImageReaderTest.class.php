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
#[@action([
#  new \unittest\actions\ExtensionAvailable('gd'),
#  new ImageTypeSupport('GIF')
#])]
class GifImageReaderTest extends TestCase {

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
