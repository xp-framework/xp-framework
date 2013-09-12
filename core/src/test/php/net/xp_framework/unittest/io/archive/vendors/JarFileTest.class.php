<?php namespace net\xp_framework\unittest\io\archive\vendors;



/**
 * TestCase
 *
 * @see   http://en.wikipedia.org/wiki/JAR_(file_format)
 * @see   http://download.oracle.com/javase/6/docs/technotes/guides/jar/jar.html
 */
class JarFileTest extends ZipFileVendorTest {

  /**
   * Test
   *
   */
  public function vendorName() {
    return 'jar';
  }
  
  /**
   * Tests reading an empty zipfile
   *
   */
  #[@test, @ignore('Cannot create empty zipfiles with `jar`')]
  public function emptyZipFile() {
    parent::emptyZipFile();
  }
}
