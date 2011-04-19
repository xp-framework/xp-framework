<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('net.xp_framework.unittest.io.archive.vendors.ZipFileVendorTest');

  /**
   * Tests ZIP file implementation with ZIP files created by
   * PHP's ZipArchive class
   *
   * @see   php://zip
   */
  class PHPZipFileTest extends ZipFileVendorTest {
    
    /**
     * Returns vendor name
     *
     * @return  string
     */
    protected function vendorName() {
      return 'php';
    }

    /**
     * Tests reading an empty zipfile
     *
     */
    #[@test, @ignore('Cannot create empty zipfiles with PHP')]
    public function emptyZipFile() {
      parent::emptyZipFile();
    }
  }
?>
