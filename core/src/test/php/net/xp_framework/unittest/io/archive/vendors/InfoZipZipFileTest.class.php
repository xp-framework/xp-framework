<?php namespace net\xp_framework\unittest\io\archive\vendors;



/**
 * Tests ZIP file implementation with ZIP files created by the
 * "zip" command line utility (Info-ZIP)
 *
 */
class InfoZipZipFileTest extends ZipFileVendorTest {
  
  /**
   * Returns vendor name
   *
   * @return  string
   */
  protected function vendorName() {
    return 'infozip';
  }
}
