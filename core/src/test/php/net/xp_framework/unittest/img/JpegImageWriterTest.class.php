<?php namespace net\xp_framework\unittest\img;

use img\io\JpegStreamWriter;

/**
 * Tests writing JPEG images
 */
#[@action([
#  new \unittest\actions\ExtensionAvailable('gd'),
#  new ImageTypeSupport('JPEG')
#])]
class JpegImageWriterTest extends AbstractImageWriterTest {

  #[@test, @expect('img.ImagingException')]
  public function write_error() {
    $this->image->saveTo(new JpegStreamWriter(newinstance('io.streams.OutputStream', array(), '{
      public function write($arg) { throw new IOException("Could not write: Intentional exception"); }
      public function flush() { }
      public function close() { }
    }')));
  }

  #[@test]
  public function write() {
    $s= new \io\streams\MemoryOutputStream();
    $this->image->saveTo(new JpegStreamWriter($s));
    $this->assertNotEmpty($s->getBytes());
  }

  #[@test]
  public function write_bc() {
    $s= new \io\Stream();
    $this->image->saveTo(new JpegStreamWriter(ref($s)));
    $this->assertNotEmpty(\io\FileUtil::getContents($s));
  }
}
