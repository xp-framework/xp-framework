<?php
/* This class is part of the XP framework
 *
 * $Id: ImageWriterTest.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace net::xp_framework::unittest::img;

  ::uses(
    'unittest.TestCase',
    'io.Stream',
    'io.FileUtil',
    'img.Image',
    'img.io.GifStreamWriter',
    'img.io.JpegStreamWriter',
    'img.io.PngStreamWriter'
  );

  /**
   * Tests writing images
   *
   * @see      xp://img.io.ImageWriter
   * @purpose  Test case
   */
  class ImageWriterTest extends unittest::TestCase {
    public
      $image= NULL;

    /**
     * Setup this test. Creates a 1x1 pixel image filled with white.
     *
     */
    public function setUp() {
      if (!extension_loaded('gd')) throw new PrerequisitesNotMetError('GD extension not available');

      $this->image= img::Image::::create(1, 1);
      $this->image->fill($this->image->allocate(new Color('#ffffff')));
    }
  
    /**
     * Tears down this test
     *
     */
    public function tearDown() {
      ::delete($this->image);
    }
    
    /**
     * Writes the image to a GIF
     *
     * @see     xp://img.io.GifStreamWriter
     */
    #[@test]
    public function writeGif() {
      $s= new io::Stream();
      $this->image->saveTo(new img::io::GifStreamWriter(::ref($s)));
      $this->assertNotEmpty(io::FileUtil::getContents($s));
    }

    /**
     * Writes the image to a JPEG
     *
     * @see     xp://img.io.GifStreamWriter
     */
    #[@test]
    public function writeJpeg() {
      $s= new io::Stream();
      $this->image->saveTo(new img::io::JpegStreamWriter(::ref($s)));
      $this->assertNotEmpty(io::FileUtil::getContents($s));
    }

    /**
     * Writes the image to a GIF
     *
     * @see     xp://img.io.GifStreamWriter
     */
    #[@test]
    public function writePng() {
      $s= new io::Stream();
      $this->image->saveTo(new img::io::PngStreamWriter(::ref($s)));
      $this->assertNotEmpty(io::FileUtil::getContents($s));
    }
  }
?>
