<?php
/* This class is part of the XP framework
 *
 * $Id: ImageReaderTest.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace net::xp_framework::unittest::img;

  ::uses(
    'unittest.TestCase',
    'io.Stream',
    'io.FileUtil',
    'img.Image',
    'img.io.GifStreamReader',
    'img.io.JpegStreamReader',
    'img.io.PngStreamReader'
  );

  /**
   * Tests reading images
   *
   * @see      xp://img.io.ImageReader
   * @purpose  Test case
   */
  class ImageReaderTest extends unittest::TestCase {

    /**
     * Setup this test.
     *
     */
    public function setUp() {
      if (!extension_loaded('gd')) throw new PrerequisitesNotMetError('GD extension not available');
    }
        
    /**
     * Reads the image from a GIF
     *
     * @see     xp://img.io.GifStreamReader
     */
    #[@test]
    public function readGif() {
      $s= new io::Stream();
      io::FileUtil::setContents($s, base64_decode('R0lGODdhAQABAIAAAP///wAAACwAAAAAAQABAAACAkQBADs='));
      img::Image::loadFrom(new StreamReader(::ref($s)));
    }

    /**
     * Reads the image from a JPEG
     *
     * @see     xp://img.io.GifStreamReader
     */
    #[@test]
    public function readJpeg() {
      $s= new io::Stream();
      io::FileUtil::setContents($s, base64_decode('/9j/4AAQSkZJRgABAQAAAQABAAD//gA7Q1JFQVRPUjogZ2QtanBlZyB2MS4wICh1c2luZyBJSkcgSlBFRyB2NjIpLCBxdWFsaXR5ID0gNzUK/9sAQwAIBgYHBgUIBwcHCQkICgwUDQwLCwwZEhMPFB0aHx4dGhwcICQuJyAiLCMcHCg3KSwwMTQ0NB8nOT04MjwuMzQy/9sAQwEJCQkMCwwYDQ0YMiEcITIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIy/8AAEQgAAQABAwEiAAIRAQMRAf/EAB8AAAEFAQEBAQEBAAAAAAAAAAABAgMEBQYHCAkKC//EALUQAAIBAwMCBAMFBQQEAAABfQECAwAEEQUSITFBBhNRYQcicRQygZGhCCNCscEVUtHwJDNicoIJChYXGBkaJSYnKCkqNDU2Nzg5OkNERUZHSElKU1RVVldYWVpjZGVmZ2hpanN0dXZ3eHl6g4SFhoeIiYqSk5SVlpeYmZqio6Slpqeoqaqys7S1tre4ubrCw8TFxsfIycrS09TV1tfY2drh4uPk5ebn6Onq8fLz9PX29/j5+v/EAB8BAAMBAQEBAQEBAQEAAAAAAAABAgMEBQYHCAkKC//EALURAAIBAgQEAwQHBQQEAAECdwABAgMRBAUhMQYSQVEHYXETIjKBCBRCkaGxwQkjM1LwFWJy0QoWJDThJfEXGBkaJicoKSo1Njc4OTpDREVGR0hJSlNUVVZXWFlaY2RlZmdoaWpzdHV2d3h5eoKDhIWGh4iJipKTlJWWl5iZmqKjpKWmp6ipqrKztLW2t7i5usLDxMXGx8jJytLT1NXW19jZ2uLj5OXm5+jp6vLz9PX29/j5+v/aAAwDAQACEQMRAD8A9/ooooA//9k='));
      img::Image::loadFrom(new StreamReader(::ref($s)));
    }

    /**
     * Reads the image from a GIF
     *
     * @see     xp://img.io.GifStreamReader
     */
    #[@test]
    public function readPng() {
      $s= new io::Stream();
      io::FileUtil::setContents($s, base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABAQMAAAAl21bKAAAAA1BMVEX///+nxBvIAAAACklEQVQImWNgAAAAAgAB9HFkpgAAAABJRU5ErkJggg=='));
      img::Image::loadFrom(new StreamReader(::ref($s)));
    }

    /**
     * Reads the image from a stream containing no data
     *
     */
    #[@test, @expect('img.ImagingException')]
    public function readEmptyData() {
      $s= new io::Stream();
      io::FileUtil::setContents($s, '');
      img::Image::loadFrom(new StreamReader(::ref($s)));
    }

    /**
     * Reads the image from a stream containing malformed dat
     *
     */
    #[@test, @expect('img.ImagingException')]
    public function readMalformedData() {
      $s= new io::Stream();
      io::FileUtil::setContents($s, '@@MALFORMED@@');
      img::Image::loadFrom(new StreamReader(::ref($s)));
    }
  }
?>
