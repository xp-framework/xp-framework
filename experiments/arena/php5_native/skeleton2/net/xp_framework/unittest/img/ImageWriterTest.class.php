<?php
/* This class is part of the XP framework
 *
 * $Id: ImageWriterTest.class.php 8518 2006-11-20 19:31:40Z friebe $ 
 */

  uses(
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
  class ImageWriterTest extends TestCase {
    public
      $image= NULL;

    /**
     * Setup this test. Creates a 1x1 pixel image filled with white.
     *
     * @access  public
     */
    public function setUp() {
      $this->image= &Image::create(1, 1);
      $this->image->fill($this->image->allocate(new Color('#ffffff')));
    }
  
    /**
     * Tears down this test
     *
     * @access  public
     */
    public function tearDown() {
      delete($this->image);
    }
    
    /**
     * Writes the image to a GIF
     *
     * @see     xp://img.io.GifStreamWriter
     * @access  public
     */
    #[@test]
    public function writeGif() {
      $s= new Stream();
      $this->image->saveTo(new GifStreamWriter(ref($s)));
      $this->assertNotEmpty(FileUtil::getContents($s));
    }

    /**
     * Writes the image to a JPEG
     *
     * @see     xp://img.io.GifStreamWriter
     * @access  public
     */
    #[@test]
    public function writeJpeg() {
      $s= new Stream();
      $this->image->saveTo(new JpegStreamWriter(ref($s)));
      $this->assertNotEmpty(FileUtil::getContents($s));
    }

    /**
     * Writes the image to a GIF
     *
     * @see     xp://img.io.GifStreamWriter
     * @access  public
     */
    #[@test]
    public function writePng() {
      $s= new Stream();
      $this->image->saveTo(new PngStreamWriter(ref($s)));
      $this->assertNotEmpty(FileUtil::getContents($s));
    }
  }
?>
