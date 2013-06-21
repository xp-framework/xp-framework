<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'img.Image',
    'img.io.GifStreamReader',
    'io.Stream',
    'io.FileUtil',
    'io.streams.MemoryInputStream'
  );

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
      if (!Runtime::getInstance()->extensionAvailable('gd')) {
        throw new PrerequisitesNotMetError('GD extension not available');
      }
      if (!(imagetypes() & IMG_Gif)) {
        throw new PrerequisitesNotMetError('GIF support not enabled');
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
      Image::loadFrom(new StreamReader(ref($s)));
    }
  }
?>
