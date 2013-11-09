<?php namespace net\xp_framework\unittest\img;

use img\io\GifStreamWriter;

/**
 * Tests writing GIF images
 */
#[@action([
#  new \unittest\actions\ExtensionAvailable('gd'),
#  new ImageTypeSupport('GIF')
#])]
class GifImageWriterTest extends AbstractImageWriterTest {

  #[@test, @expect('img.ImagingException')]
  public function write_error() {
    $this->image->saveTo(new GifStreamWriter(newinstance('io.streams.OutputStream', array(), '{
      public function write($arg) { throw new IOException("Could not write: Intentional exception"); }
      public function flush() { }
      public function close() { }
    }')));
  }

  #[@test]
  public function write() {
    $s= new \io\streams\MemoryOutputStream();
    $this->image->saveTo(new GifStreamWriter($s));
    $this->assertNotEmpty($s->getBytes());
  }

  #[@test]
  public function write_bc() {
    $s= new \io\Stream();
    $this->image->saveTo(new GifStreamWriter(ref($s)));
    $this->assertNotEmpty(\io\FileUtil::getContents($s));
  }
}
