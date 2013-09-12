<?php namespace net\xp_framework\unittest\io\archive;



/**
 * Tests ZipArchiveReader class
 *
 * @see   xp://io.archive.zip.ZipArchiveReader
 */
class ZipArchiveReaderTest extends ZipFileTest {

  /**
   * Tests close()
   *
   */
  #[@test]
  public function close() {
    $stream= newinstance('io.streams.InputStream', array(), '{
      public $closed= FALSE;
      public function read($limit= 8192) { return ""; }
      public function available() { return 0; }
      public function close() { $this->closed= TRUE; }
    }');
    $reader= new \io\archive\zip\ZipArchiveReader($stream);
    $reader->close();
    $this->assertTrue($stream->closed);
  }
}
