<?php namespace net\xp_framework\unittest\img;

use img\io\PngStreamWriter;

/**
 * Tests writing PNG images
 */
class PngImageWriterTest extends AbstractImageWriterTest {

  /**
   * Returns the image type to test for
   *
   * @return string
   */
  protected function imageType() {
    return 'PNG';
  }

  #[@test, @expect('img.ImagingException')]
  public function write_error() {
    $this->image->saveTo(new PngStreamWriter(newinstance('io.streams.OutputStream', array(), '{
      public function write($arg) { throw new IOException("Could not write: Intentional exception"); }
      public function flush() { }
      public function close() { }
    }')));
  }

  #[@test]
  public function write() {
    $s= new \io\streams\MemoryOutputStream();
    $this->image->saveTo(new PngStreamWriter($s));
    $this->assertNotEmpty($s->getBytes());
  }

  #[@test]
  public function write_bc() {
    $s= new \io\Stream();
    $this->image->saveTo(new PngStreamWriter(ref($s)));
    $this->assertNotEmpty(\io\FileUtil::getContents($s));
  }
}
