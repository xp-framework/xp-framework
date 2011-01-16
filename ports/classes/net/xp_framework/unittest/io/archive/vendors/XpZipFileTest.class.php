<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('net.xp_framework.unittest.io.archive.vendors.ZipFileVendorTest');

  /**
   * Tests our own ZIP file implementation.
   *
   * @see   xp://io.archive.zip.ZipFile
   */
  class XpZipFileTest extends ZipFileVendorTest {
    
    /**
     * Returns vendor name
     *
     * @return  string
     */
    protected function vendorName() {
      return 'xp';
    }
  }
?>
