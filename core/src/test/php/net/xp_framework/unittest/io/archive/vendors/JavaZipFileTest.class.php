<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('net.xp_framework.unittest.io.archive.vendors.ZipFileVendorTest');

  /**
   * Tests ZIP file implementation with ZIP files created by
   * Java's java.util.zip API
   *
   * @see   http://java.sun.com/javase/6/docs/api/java/util/zip/package-summary.html
   */
  class JavaZipFileTest extends ZipFileVendorTest {
    
    /**
     * Returns vendor name
     *
     * @return  string
     */
    protected function vendorName() {
      return 'java';
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
?>
