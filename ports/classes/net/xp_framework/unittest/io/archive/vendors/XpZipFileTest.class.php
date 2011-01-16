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

    /**
     * Tests reading an zipfile with one entry called "הצü.txt" in its 
     * root directory, using utf-8 names
     *
     */
    #[@test]
    public function unicodeZip() {
      $entries= $this->entriesIn($this->archiveReaderFor($this->vendor, 'unicode'));
      $this->assertEquals(1, sizeof($entries));
      $this->assertEquals('הצü.txt', $entries[0]->getName());
      $this->assertEquals(0, $entries[0]->getSize());
      $this->assertFalse($entries[0]->isDirectory());
    }
  }
?>
