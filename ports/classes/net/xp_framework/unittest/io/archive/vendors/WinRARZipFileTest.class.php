<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('net.xp_framework.unittest.io.archive.vendors.ZipFileVendorTest');

  /**
   * Tests ZIP file implementation with ZIP files created by
   * WinRAR
   *
   * @see   http://www.winrar.de/
   */
  class WinRARZipFileTest extends ZipFileVendorTest {
    
    /**
     * Returns vendor name
     *
     * @return  string
     */
    protected function vendorName() {
      return 'winrar';
    }

    /**
     * Tests reading an empty zipfile
     *
     */
    #[@test, @ignore('Cannot create empty zipfiles with WinRAR')]
    public function emptyZipFile() {
      parent::emptyZipFile();
    }
  }
?>
