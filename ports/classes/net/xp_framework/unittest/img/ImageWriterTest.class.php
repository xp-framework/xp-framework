<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'io.Stream',
    'io.FileUtil',
    'img.Image',
    'img.io.GifStreamWriter',
    'img.io.JpegStreamWriter',
    'img.io.PngStreamWriter',
    'io.streams.MemoryOutputStream'
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
     */
    public function setUp() {
      if (!extension_loaded('gd')) throw new PrerequisitesNotMetError('GD extension not available');

      $this->image= Image::create(1, 1);
      $this->image->fill($this->image->allocate(new Color('#ffffff')));
    }
  
    /**
     * Tears down this test
     *
     */
    public function tearDown() {
      delete($this->image);
    }

    /**
     * Tests the situation when an exception is caused during stream writes.
     *
     */
    #[@test, @expect('img.ImagingException')]
    public function writeError() {
      $this->image->saveTo(new GifStreamWriter(newinstance('io.streams.OutputStream', array(), '{
        public function write($arg) { throw new IOException("Could not write: Intentional exception"); }
        public function flush() { }
        public function close() { }
      }')));
    }
    
    /**
     * Writes the image to a GIF
     *
     * @see     xp://img.io.GifStreamWriter
     */
    #[@test]
    public function writeGif() {
      $s= new MemoryOutputStream();
      $this->image->saveTo(new GifStreamWriter($s));
      $this->assertNotEmpty($s->getBytes());
    }

    /**
     * Writes the image to a JPEG
     *
     * @see     xp://img.io.GifStreamWriter
     */
    #[@test]
    public function writeJpeg() {
      $s= new MemoryOutputStream();
      $this->image->saveTo(new JpegStreamWriter($s));
      $this->assertNotEmpty($s->getBytes());
    }

    /**
     * Writes the image to a GIF
     *
     * @see     xp://img.io.GifStreamWriter
     */
    #[@test]
    public function writePng() {
      $s= new MemoryOutputStream();
      $this->image->saveTo(new PngStreamWriter($s));
      $this->assertNotEmpty($s->getBytes());
    }

    /**
     * Writes the image to a GIF using the backwards-compatible method
     *
     * @see     xp://img.io.GifStreamWriter
     */
    #[@test]
    public function writeGifBC() {
      $s= new Stream();
      $this->image->saveTo(new GifStreamWriter(ref($s)));
      $this->assertNotEmpty(FileUtil::getContents($s));
    }

    /**
     * Writes the image to a JPEG using the backwards-compatible method
     *
     * @see     xp://img.io.GifStreamWriter
     */
    #[@test]
    public function writeJpegBC() {
      $s= new Stream();
      $this->image->saveTo(new JpegStreamWriter(ref($s)));
      $this->assertNotEmpty(FileUtil::getContents($s));
    }

    /**
     * Writes the image to a GIF using the backwards-compatible method
     *
     * @see     xp://img.io.GifStreamWriter
     */
    #[@test]
    public function writePngBC() {
      $s= new Stream();
      $this->image->saveTo(new PngStreamWriter(ref($s)));
      $this->assertNotEmpty(FileUtil::getContents($s));
    }
  }
?>
