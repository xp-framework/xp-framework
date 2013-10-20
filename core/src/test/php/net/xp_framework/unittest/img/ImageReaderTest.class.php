<?php namespace net\xp_framework\unittest\img;

use lang\Runtime;
use unittest\TestCase;
use io\Stream;
use io\FileUtil;
use img\Image;
use img\io\GifStreamReader;
use img\io\JpegStreamReader;
use img\io\PngStreamReader;
use io\streams\MemoryInputStream;


/**
 * Tests reading images
 *
 * @see      xp://img.io.ImageReader
 * @purpose  Test case
 */
class ImageReaderTest extends TestCase {

  /**
   * Setup this test.
   *
   */
  public function setUp() {
    if (!Runtime::getInstance()->extensionAvailable('gd')) {
      throw new \unittest\PrerequisitesNotMetError('GD extension not available');
    }
  }

  /**
   * Tests the situation when an exception is caused during stream reads
   *
   */
  #[@test, @expect('img.ImagingException')]
  public function readError() {
    $s= newinstance('io.streams.InputStream', array(), '{
      public function read($limit= 8192) { throw new IOException("Could not read: Intentional exception"); }
      public function available() { return 1; }
      public function close() { }
    }');
    Image::loadFrom(new GifStreamReader($s));
  }

  /**
   * Reads the image from a stream containing no data
   *
   */
  #[@test, @expect('img.ImagingException')]
  public function readEmptyData() {
    $s= new MemoryInputStream('');
    Image::loadFrom(new PngStreamReader($s));
  }

  /**
   * Reads the image from a stream containing malformed dat
   *
   */
  #[@test, @expect('img.ImagingException')]
  public function readMalformedData() {
    $s= new MemoryInputStream('@@MALFORMED@@');
    Image::loadFrom(new PngStreamReader($s));
  }

  /**
   * Reads the image from a stream containing no data
   *
   */
  #[@test, @expect('img.ImagingException')]
  public function readEmptyDataBC() {
    $s= new Stream();
    FileUtil::setContents($s, '');
    Image::loadFrom(new \img\io\StreamReader(ref($s)));
  }

  /**
   * Reads the image from a stream containing malformed dat
   *
   */
  #[@test, @expect('img.ImagingException')]
  public function readMalformedDataBC() {
    $s= new Stream();
    FileUtil::setContents($s, '@@MALFORMED@@');
    Image::loadFrom(new \img\io\StreamReader(ref($s)));
  }
}
