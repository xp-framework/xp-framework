<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'net.xp_framework.unittest.img.AbstractImageWriterTest',
    'img.io.JpegStreamWriter'
  );

  /**
   * Tests writing JPEG images
   */
  class JpegImageWriterTest extends AbstractImageWriterTest {

    /**
     * Returns the image type to test for
     *
     * @return string
     */
    protected function imageType() {
      return 'JPEG';
    }

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
      $s= new MemoryOutputStream();
      $this->image->saveTo(new JpegStreamWriter($s));
      $this->assertNotEmpty($s->getBytes());
    }

    #[@test]
    public function write_bc() {
      $s= new Stream();
      $this->image->saveTo(new JpegStreamWriter(ref($s)));
      $this->assertNotEmpty(FileUtil::getContents($s));
    }
  }
?>
