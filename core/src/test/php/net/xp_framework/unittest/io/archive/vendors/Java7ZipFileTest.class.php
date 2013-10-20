<?php namespace net\xp_framework\unittest\io\archive\vendors;



/**
 * Tests ZIP file implementation with ZIP files created by
 * Java 7's java.util.zip API
 *
 * @see   http://blogs.sun.com/xuemingshen/entry/non_utf_8_encoding_in
 * @see   http://cr.openjdk.java.net/~sherman/4244499/webrev/
 * @see   http://cr.openjdk.java.net/~sherman/4244499/webrev/test/java/util/zip/zip.java.patch
 * @see   http://java.sun.com/javase/6/docs/api/java/util/zip/package-summary.html
 */
class Java7ZipFileTest extends ZipFileVendorTest {
  
  /**
   * Returns vendor name
   *
   * @return  string
   */
  protected function vendorName() {
    return 'java7';
  }

  /**
   * Tests reading an empty zipfile
   *
   */
  #[@test, @ignore('Cannot create empty zipfiles with java.util.zip')]
  public function emptyZipFile() {
    parent::emptyZipFile();
  }
}
