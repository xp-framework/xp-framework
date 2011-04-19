<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('net.xp_framework.unittest.io.archive.vendors.ZipFileVendorTest');

  /**
   * Tests ZIP file implementation with ZIP files created by the
   * Windows built-in ZIP file support.
   *
   */
  class WindowsZipFileTest extends ZipFileVendorTest {
    
    /**
     * Returns vendor name
     *
     * @return  string
     */
    protected function vendorName() {
      return 'windows';
    }
  }
?>
